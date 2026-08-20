/**
 * CouponWheel Admin JS
 * Handles: slice manager, live probability preview, product search, form save
 */
(function ($) {
    'use strict';

    /* =========================================================
       CONFIG & STATE
    ========================================================= */
    const WEIGHTS = window.CWData ? window.CWData.difficultyWeights : {
        very_easy: 50, easy: 30, normal: 15, hard: 5, very_hard: 1, never: 0
    };
    let sliceIndex = 0; // monotonically-increasing unique index per session

    /* =========================================================
       SLICE MANAGER
    ========================================================= */
    const SliceManager = {

        container: null,
        template: null,

        init() {
            this.container = document.getElementById('cw-slices-container');
            this.template  = document.getElementById('cw-slice-template');

            // Load existing slices (edit mode)
            if (window.CWData && window.CWData.slices && window.CWData.slices.length) {
                window.CWData.slices.forEach(slice => this.addRow(slice));
            }

            this._bindEvents();
            ProbabilityPreview.recalc();
        },

        addRow(data) {
            data = data || {};
            const idx  = sliceIndex++;
            const num  = this.container.children.length + 1;

            // Clone template content
            const tplHtml = this.template.innerHTML
                .replace(/\${index}/g, idx)
                .replace(/\${num}/g,   num);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = tplHtml;
            const row = wrapper.firstElementChild;

            // Pre-fill values if editing
            if (data.name)       row.querySelector('.cw-field-name').value       = data.name;
            if (data.difficulty) row.querySelector('.cw-field-difficulty').value = data.difficulty;
            if (data.bg_color)   row.querySelector('.cw-field-bg-color').value   = data.bg_color;
            if (data.text_color) row.querySelector('.cw-field-text-color').value = data.text_color;
            const qtyEl = row.querySelector('.cw-field-quantity');
            if (qtyEl) qtyEl.value = data.quantity ?? 0;

            const prizeType = data.prize_type || 'text';
            row.querySelectorAll('.cw-prize-type').forEach(radio => {
                radio.checked = (radio.value === prizeType);
            });

            if (prizeType === 'text') {
                row.querySelector('.cw-field-prize-text').value = data.prize_text || '';
                row.querySelector('.cw-prize-text-wrap').style.display = '';
                row.querySelector('.cw-prize-product-wrap').style.display = 'none';
            } else {
                row.querySelector('.cw-prize-text-wrap').style.display = 'none';
                row.querySelector('.cw-prize-product-wrap').style.display = '';
            }

            // Inject popoverAdvance widget vào placeholder
            const placeholder = row.querySelector('.cw-popover-product-placeholder');
            if (placeholder && window.CWProductPopoverTpl) {
                const popoverId   = 'cw-product-popover-' + idx;
                const fieldName   = 'cw_product_' + idx;
                const popoverHtml = window.CWProductPopoverTpl
                    .replace(/__CW_IDX__/g, popoverId)
                    .replace(/__CW_FIELD_NAME__/g, fieldName);
                placeholder.outerHTML = popoverHtml;
            }

            this.container.appendChild(row);

            // Khởi tạo PopoverAdvance widget sau khi đã append vào DOM
            const popoverEl = row.querySelector('.popover_advance');
            if (popoverEl) {
                const popId = uniqid();
                popoverAdvances[popId] = new PopoverAdvance($(popoverEl), popId);

                // Nếu edit mode và có product_id, load lại giá trị vào popover
                if (data.product_id) {
                    const pa = popoverAdvances[popId];
                    pa.value = [data.product_id];
                    pa.loadReview();
                }
            }

            ProbabilityPreview.recalc();
        },

        removeRow(row) {
            row.remove();
            this._renumberRows();
            ProbabilityPreview.recalc();
        },

        collectSlices() {
            const rows   = this.container.querySelectorAll('.cw-slice-row');
            const slices = [];
            rows.forEach(row => {
                const prizeTypeChecked = row.querySelector('.cw-prize-type:checked');
                const prizeType = prizeTypeChecked ? prizeTypeChecked.value : 'text';

                // Đọc product_id từ popoverAdvance (checkbox đã chọn)
                const productChecked = row.querySelector('.popover_advance input.input-popover-advance-value:checked');
                const productId   = productChecked ? productChecked.value : 0;
                const productName = row.querySelector('.popover_advance .popover_advance__list .item__name')?.textContent?.trim() || '';

                slices.push({
                    name:         row.querySelector('.cw-field-name').value.trim(),
                    difficulty:   row.querySelector('.cw-field-difficulty').value,
                    quantity:     parseInt(row.querySelector('.cw-field-quantity')?.value || 0) || 0,
                    prize_type:   prizeType,
                    prize_text:   row.querySelector('.cw-field-prize-text').value.trim(),
                    product_id:   productId,
                    product_name: productName,
                    bg_color:     row.querySelector('.cw-field-bg-color').value,
                    text_color:   row.querySelector('.cw-field-text-color').value,
                });
            });
            return slices;
        },

        _renumberRows() {
            const rows = this.container.querySelectorAll('.cw-slice-row');
            rows.forEach((row, i) => {
                const titleEl = row.querySelector('.cw-slice-title');
                if (titleEl) titleEl.textContent = 'Ô #' + (i + 1);
            });
        },

        _bindEvents() {
            // Add slice button
            document.querySelector('.js-cw-add-slice').addEventListener('click', () => {
                this.addRow();
            });

            // Remove slice (delegated)
            this.container.addEventListener('click', e => {
                if (e.target.closest('.js-cw-remove-slice')) {
                    const row = e.target.closest('.cw-slice-row');
                    if (row) this.removeRow(row);
                }
            });

            // Toggle prize type (delegated)
            this.container.addEventListener('change', e => {
                if (e.target.classList.contains('cw-prize-type')) {
                    const row = e.target.closest('.cw-slice-row');
                    const isProduct = e.target.value === 'product';
                    row.querySelector('.cw-prize-text-wrap').style.display    = isProduct ? 'none' : '';
                    row.querySelector('.cw-prize-product-wrap').style.display = isProduct ? '' : 'none';
                }

                // Recalc probability on any difficulty change
                if (e.target.classList.contains('cw-field-difficulty')) {
                    ProbabilityPreview.recalc();
                }
            });
        }
    };

    /* =========================================================
       PROBABILITY PREVIEW
    ========================================================= */
    const ProbabilityPreview = {

        recalc() {
            const rows   = document.querySelectorAll('#cw-slices-container .cw-slice-row');
            const list   = document.getElementById('cw-prob-list');
            const empty  = document.getElementById('cw-prob-empty');
            const warn   = document.getElementById('cw-prob-warning');

            if (!rows.length) {
                if (empty) empty.style.display = '';
                if (list)  list.innerHTML = '';
                if (warn)  warn.style.display = 'none';
                return;
            }

            if (empty) empty.style.display = 'none';

            // Gather weights
            const items = [];
            rows.forEach((row, i) => {
                const diffSelect = row.querySelector('.cw-field-difficulty');
                const diff   = diffSelect ? diffSelect.value : 'normal';
                const weight = WEIGHTS[diff] || 0;
                const name   = row.querySelector('.cw-field-name').value.trim() || ('Ô #' + (i + 1));
                const color  = row.querySelector('.cw-field-bg-color') ? row.querySelector('.cw-field-bg-color').value : '#4e73df';
                items.push({ name, weight, color, index: i, row });
            });

            const total = items.reduce((s, it) => s + it.weight, 0);

            if (total === 0) {
                if (list) list.innerHTML = '';
                if (warn) warn.style.display = '';
                // Update all badges
                rows.forEach(row => {
                    const badge = row.querySelector('.cw-prob-badge');
                    if (badge) { badge.textContent = '0%'; badge.style.background = '#dc3545'; }
                });
                return;
            }

            if (warn) warn.style.display = 'none';

            // Build preview HTML
            let html = '';
            items.forEach(it => {
                const pct = it.weight === 0 ? 0 : +(it.weight / total * 100).toFixed(1);
                const label = it.weight === 0
                    ? `<span style="color:#dc3545">Không bao giờ trúng</span>`
                    : `<strong>${pct}%</strong>`;

                html += `
                <div class="cw-prob-item mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-truncate" style="max-width:140px;font-size:12px;">${it.name}</span>
                        ${label}
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar" role="progressbar"
                             style="width:${pct}%;background-color:${it.color};"
                             aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>`;

                // Update badge on the slice row card
                const badge = it.row.querySelector('.cw-prob-badge');
                if (badge) {
                    badge.textContent = it.weight === 0 ? '0%' : `~${pct}%`;
                    badge.style.background = it.weight === 0 ? '#dc3545' : '#0d6efd';
                }
            });

            if (list) list.innerHTML = html;
        }
    };


    /* =========================================================
       TRIGGER DELAY UI
    ========================================================= */
    function initTriggerUI() {
        const cbAuto   = document.querySelector('#cw-program-form input[name="trigger_auto"]');
        const cbButton = document.querySelector('#cw-program-form input[name="trigger_button"]');

        function getDelayWrap() {
            const delayInput = document.querySelector('#cw-program-form input[name="trigger_delay"]');
            return delayInput ? delayInput.closest('[class*="col-"]') : null;
        }

        // Hiện trigger_delay chỉ khi "auto" được chọn
        function toggleDelay() {
            const delayWrap = getDelayWrap();
            if (delayWrap) {
                delayWrap.style.display = cbAuto && cbAuto.checked ? '' : 'none';
            }
        }

        // Validate: không cho bỏ chọn nếu đây là checkbox cuối cùng còn chọn
        function preventUncheckAll(e) {
            const autoChecked   = cbAuto   && cbAuto.checked;
            const buttonChecked = cbButton && cbButton.checked;
            if (!autoChecked && !buttonChecked) {
                // Khôi phục lại checkbox vừa bỏ chọn
                e.target.checked = true;
                // Hiện cảnh báo nhỏ
                const label = e.target.closest('.form-check') || e.target.closest('[class*="col-"]');
                if (label) {
                    const warn = label.querySelector('.cw-trigger-warn') || (() => {
                        const el = document.createElement('small');
                        el.className = 'cw-trigger-warn text-danger ms-1';
                        el.textContent = window.CWData?.i18n?.triggerAtLeastOne || 'Phải chọn ít nhất 1 hình thức.';
                        label.appendChild(el);
                        return el;
                    })();
                    warn.style.display = '';
                    setTimeout(() => { warn.style.display = 'none'; }, 2500);
                }
            }
            toggleDelay();
        }

        if (cbAuto)   cbAuto.addEventListener('change',   preventUncheckAll);
        if (cbButton) cbButton.addEventListener('change', preventUncheckAll);
        toggleDelay();
    }

    /* =========================================================
       FORM SAVE
    ========================================================= */
    /** Đọc value input theo name, scope vào form wrapper */
    function val(name) {
        const el = document.querySelector(`#cw-program-form [name="${name}"]`);
        return el ? el.value : '';
    }

    /** Thu thập tất cả text fields trong tab #text (bao gồm các field đa ngôn ngữ _langKey) */
    function collectDisplayText() {
        const tab = document.getElementById('text');
        if (!tab) return {};

        const result = {};
        tab.querySelectorAll('input[type="text"][name], textarea[name]').forEach(el => {
            const name = el.getAttribute('name');
            if (name) result[name] = el.value;
        });
        return result;
    }

    /** Thu thập dữ liệu headingStyle từ textBuilding field (nested array inputs) */
    function collectHeadingStyle() {
        const formEl = $('#cw-program-form');
        // serializeJSON sẽ tự động build nested object từ headingStyle[text], headingStyle[style], v.v.
        const all = formEl.find('[name^="headingStyle"]').serializeJSON();
        return all.headingStyle || {};
    }

    function initSave() {
        const btn = document.querySelector('.js-cw-save');
        if (!btn) return;

        btn.addEventListener('click', () => {
            const formEl = document.getElementById('cw-program-form');
            const id     = parseInt(formEl.dataset.id) || 0;
            const slices = SliceManager.collectSlices();

            const cbAuto   = document.querySelector('#cw-program-form input[name="trigger_auto"]');
            const cbButton = document.querySelector('#cw-program-form input[name="trigger_button"]');

            /** Đọc radio đang checked trong form */
            const radio = (name) => {
                const el = document.querySelector(`#cw-program-form input[name="${name}"]:checked`);
                return el ? el.value : '';
            };

            const payload = {
                action:        'CouponWheel\\Ajax\\Admin\\ProgramAjax::save',
                id,
                name:          val('name'),
                start_at:      val('start_at'),
                end_at:        val('end_at'),
                status:        val('status'),
                require_login: val('require_login'),
                spin_limit:    val('spin_limit'),
                contact_limit: val('contact_limit'),
                show_email:    document.querySelector('#cw-program-form input[name="show_email"]')?.value ?? 1,
                show_phone:    document.querySelector('#cw-program-form input[name="show_phone"]')?.value ?? 1,
                trigger_auto:   cbAuto   && cbAuto.checked   ? 1 : 0,
                trigger_button: cbButton && cbButton.checked ? 1 : 0,
                trigger_delay: val('trigger_delay'),
                slices,
                // Display settings từ tab Giao diện
                background:       radio('background'),
                frame:            radio('frame'),
                center:           radio('center'),
                triggerStyle:     radio('triggerStyle'),
                triggerPosition:  val('triggerPosition'),
                triggerIcon:      val('triggerIcon'),
                triggerEffect:    val('triggerEffect'),
                triggerBg:        val('triggerBg'),
                // Văn bản (bao gồm đa ngôn ngữ)
                displayText:      collectDisplayText(),
                // Kiểu chữ tiêu đề vòng xoay
                headingStyle:     collectHeadingStyle(),
            };

            // Loading state
            const loading = SkilldoUtil.buttonLoading(btn);
            loading.start();

            request.post(window.CWData.ajaxUrl, payload).then(res => {
                loading.stop();
                SkilldoMessage.response(res);
                if (res.data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = window.CWData.backUrl;
                    }, 800);
                }
            }).catch(() => {
                loading.stop();
            });        });
    }

    /* =========================================================
       TOGGLE STATUS (index page)
    ========================================================= */
    function initToggleStatus() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-cw-toggle-status');
            if (!btn) return;

            const id      = btn.dataset.id;
            const loading = SkilldoUtil.buttonLoading(btn);
            loading.start();

            request.post(window.CWData?.ajaxUrl || ajax, {
                action: 'CouponWheel\\Ajax\\Admin\\ProgramAjax::toggleStatus',
                id,
            }).then(res => {
                loading.stop();
                if (res.status === 'success') {
                    const newStatus = res.data?.status;
                    const isOn     = newStatus == 1;
                    const labelOn   = btn.dataset.labelOn  || 'Đang chạy';
                    const labelOff  = btn.dataset.labelOff || 'Tạm dừng';

                    // Cập nhật badge hiện tại
                    btn.dataset.status = newStatus;
                    btn.className      = 'badge border-0 cursor-pointer js-cw-toggle-status bg-' + (isOn ? 'success' : 'secondary');
                    btn.textContent    = isOn ? labelOn : labelOff;

                    // Nếu bật chương trình này → reset tất cả badge khác về tắt
                    if (isOn) {
                        document.querySelectorAll('.js-cw-toggle-status').forEach(b => {
                            if (b !== btn) {
                                b.dataset.status = 0;
                                b.className      = 'badge border-0 cursor-pointer js-cw-toggle-status bg-secondary';
                                b.textContent    = b.dataset.labelOff || 'Tạm dừng';
                            }
                        });
                    }

                    SkilldoMessage.response(res);
                } else {
                    SkilldoMessage.response(res);
                }
            }).catch(() => {
                loading.stop();
            });
        });
    }

    /* =========================================================
       CLONE PROGRAM (index page)
    ========================================================= */
    function initClone() {

        document.addEventListener('click', function (e) {

            const btn = e.target.closest('.js-cw-clone');

            if (!btn) return;

            const id   = btn.dataset.id;

            const name = btn.dataset.name || 'chương trình này';

            if (!confirm(`Nhân bản "${name}"?`)) return;

            const loading = SkilldoUtil.buttonLoading(btn);

            loading.start();

            request.post(window.CWData?.ajaxUrl || ajax, {
                action: 'CouponWheel\\Ajax\\Admin\\ProgramAjax::clone',
                id:     id,
            }).then(res => {

                loading.stop();

                SkilldoMessage.response(res);

                if (res.status === 'success' && res.data?.redirect)
                {
                    setTimeout(() => {
                        window.location.href = res.data.redirect;
                    }, 800);
                }
            }).catch(() => {
                loading.stop();
            });
        });
    }

    /* =========================================================
       INIT
    ========================================================= */
    document.addEventListener('DOMContentLoaded', () => {
        // Trang save (add/edit)
        if (document.getElementById('cw-program-form')) {
            SliceManager.init();
            initTriggerUI();
            initSave();
        }

        // Trang index — khởi tạo clone + toggle status
        initClone();
        initToggleStatus();
    });

})(jQuery);


