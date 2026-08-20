/**
 * CouponWheel Frontend JS — v3
 * Pattern: quay vô tận ngay khi bấm → AJAX về → phanh dừng vào đúng ô
 */
;(() => {
    'use strict';

    const $ = (sel, root = document) => root.querySelector(sel);

    const store = {
        get(k)    { try { return JSON.parse(localStorage.getItem(k) || 'null'); } catch { return null; } },
        set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch {} },
    };

    function toast(type, msg, ms = 4000) {
        const el = $('#cw-toast');
        if (!el) return;
        el.className = 'cw-toast ' + (type === 'ok' ? 'ok' : 'err');
        el.innerHTML = msg;
        setTimeout(() => { el.textContent = ''; el.className = 'cw-toast'; }, ms);
    }

    /* =========================================================
       PRIZE WHEEL CLASS
       - draw()         : vẽ tĩnh 1 lần
       - startSpin()    : quay vô tận bằng rAF (phase 1 — chờ AJAX)
       - stopAt(index)  : phanh dừng vào đúng ô (phase 2 — sau AJAX)
    ========================================================= */
    class PrizeWheel {
        constructor(canvas) {
            this.canvas          = canvas;
            this.ctx             = canvas.getContext('2d');
            this.slices          = [];
            this.currentRotation = 0;
            this.animating       = false;
            this._rafId          = null;
            this._speed          = 0;      // deg/frame khi phase 1
            this._phase          = 'idle'; // idle | spinning | stopping
            this.center = {
                x: canvas.width  / 2,
                y: canvas.height / 2,
                r: canvas.width  / 2 - 10,
            };
        }

        setSlices(slices) { this.slices = slices; }

        draw() {
            const { ctx, center: { x, y, r } } = this;
            ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            const n = this.slices.length;
            if (!n) return;
            const arc = (Math.PI * 2) / n;
            const startOffset = -Math.PI / 2;
            const defaultColors = ['#ff595e','#ffca3a','#8ac926','#1982c4','#6a4c93','#ff924c','#00b4d8','#e9c46a'];

            // ---- Vẽ từng ô ----
            for (let i = 0; i < n; i++) {
                const s     = this.slices[i];
                const start = startOffset + i * arc;
                const end   = start + arc;

                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.arc(x, y, r, start, end);
                ctx.closePath();
                ctx.fillStyle = s.bg_color || defaultColors[i % defaultColors.length];
                ctx.fill();

                // --- Border ánh kim giữa các ô ---
                // Tạo gradient tuyến tính dọc theo cạnh phân chia
                const gx1 = x + Math.cos(start) * r * 0.15;
                const gy1 = y + Math.sin(start) * r * 0.15;
                const gx2 = x + Math.cos(start) * r;
                const gy2 = y + Math.sin(start) * r;
                const goldGrad = ctx.createLinearGradient(gx1, gy1, gx2, gy2);
                goldGrad.addColorStop(0,   'rgba(255,215,0,0.15)');
                goldGrad.addColorStop(0.3, 'rgba(255,236,139,0.85)');
                goldGrad.addColorStop(0.5, 'rgba(255,255,220,1)');
                goldGrad.addColorStop(0.7, 'rgba(255,200,50,0.85)');
                goldGrad.addColorStop(1,   'rgba(184,134,11,0.5)');

                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x + Math.cos(start) * r, y + Math.sin(start) * r);
                ctx.strokeStyle = goldGrad;
                ctx.lineWidth   = 2.5;
                ctx.stroke();

                // ---- Text ----
                ctx.save();
                ctx.translate(x, y);
                ctx.rotate(start + arc / 2);
                ctx.textAlign     = 'right';
                ctx.fillStyle     = s.text_color || '#ffffff';
                ctx.shadowColor   = 'rgba(0,0,0,0.6)';
                ctx.shadowBlur    = 4;
                ctx.shadowOffsetX = 1;
                ctx.shadowOffsetY = 1;

                const textMaxW = r - 60;
                let fontSize = Math.max(14, Math.min(24, Math.floor(r * 0.08 - n * 0.8)));
                ctx.font = `bold ${fontSize}px system-ui, sans-serif`;

                const words = (s.name || '').split(' ');
                const lines = [];
                let line = '';
                for (const word of words) {
                    const test = line ? line + ' ' + word : word;
                    if (ctx.measureText(test).width > textMaxW && line) {
                        lines.push(line);
                        line = word;
                    } else {
                        line = test;
                    }
                }
                if (line) lines.push(line);

                const maxLines = 3;
                if (lines.length > maxLines) {
                    lines.splice(maxLines - 1);
                    lines[maxLines - 1] = lines[maxLines - 1].slice(0, -1) + '…';
                }

                const lineH = fontSize * 1.25;
                const totalH = lines.length * lineH;
                const startY = -totalH / 2 + fontSize * 0.8;
                lines.forEach((ln, li) => {
                    ctx.fillText(ln, r - 28, startY + li * lineH);
                });
                ctx.restore();
            }

            // ---- Viền ngoài cùng ánh kim ----
            const rimGrad = ctx.createConicGradient
                ? ctx.createConicGradient(0, x, y)
                : null;

            if (rimGrad) {
                // Conic gradient cho viền xoay ánh kim
                rimGrad.addColorStop(0,      '#b8860b');
                rimGrad.addColorStop(0.125,  '#ffd700');
                rimGrad.addColorStop(0.25,   '#fffacd');
                rimGrad.addColorStop(0.375,  '#ffd700');
                rimGrad.addColorStop(0.5,    '#b8860b');
                rimGrad.addColorStop(0.625,  '#ffd700');
                rimGrad.addColorStop(0.75,   '#fffacd');
                rimGrad.addColorStop(0.875,  '#ffd700');
                rimGrad.addColorStop(1,      '#b8860b');
                ctx.beginPath();
                ctx.arc(x, y, r, 0, Math.PI * 2);
                ctx.strokeStyle = rimGrad;
                ctx.lineWidth   = 6;
                ctx.stroke();
            } else {
                // fallback
                ctx.beginPath();
                ctx.arc(x, y, r, 0, Math.PI * 2);
                ctx.strokeStyle = '#ffd700';
                ctx.lineWidth   = 6;
                ctx.stroke();
            }
        }

        /**
         * Phase 1: Quay vô tận bằng requestAnimationFrame
         * Tăng tốc dần đến tốc độ cruising rồi giữ ổn định
         */
        startSpin() {
            if (this._phase !== 'idle') return;
            this._phase = 'spinning';
            this.animating = true;
            this._speed = 0;

            // Reset CSS transition để dùng rAF
            this.canvas.style.transition = 'none';

            const targetSpeed = 8; // deg/frame khi full speed
            const accelFrames = 30; // số frame để đạt full speed
            let frame = 0;

            const loop = () => {
                if (this._phase !== 'spinning') return;

                // Tăng tốc dần
                if (frame < accelFrames) {
                    this._speed = targetSpeed * (frame / accelFrames);
                    frame++;
                } else {
                    this._speed = targetSpeed;
                }

                this.currentRotation += this._speed;
                this.canvas.style.transform = `rotate(${this.currentRotation}deg)`;
                this._rafId = requestAnimationFrame(loop);
            };

            this._rafId = requestAnimationFrame(loop);
        }

        /**
         * Phase 2: Phanh dừng vào đúng ô index
         * Gọi sau khi AJAX trả về kết quả
         */
        stopAt(index, callback) {
            // Nếu không đang spinning (ví dụ AJAX về quá nhanh trước khi startSpin kịp chạy)
            // vẫn phải gọi callback để không bị kẹt UI
            if (this._phase !== 'spinning') {
                this._phase    = 'idle';
                this.animating = false;
                if (callback) callback();
                return;
            }
            this._phase = 'stopping';

            // Hủy rAF loop
            if (this._rafId) cancelAnimationFrame(this._rafId);

            const n          = this.slices.length;
            const sliceDeg   = 360 / n;

            // Góc tâm ô trúng tính từ 12h
            const targetOffset  = (index * sliceDeg) + (sliceDeg / 2);
            const randomOffset  = (Math.random() - 0.5) * (sliceDeg * 0.7);

            // Đảm bảo quay ít nhất thêm 3 vòng nữa trước khi phanh (cho đẹp)
            const minExtraSpins = 360 * 3;
            const currentMod    = this.currentRotation % 360;
            const targetMod     = (360 - targetOffset + randomOffset + 360) % 360;
            let   rotationToAdd = targetMod - currentMod;
            if (rotationToAdd < 0) rotationToAdd += 360;

            this.currentRotation += rotationToAdd + minExtraSpins;

            // Áp dụng CSS transition để phanh mượt
            this.canvas.style.transition = 'transform 4s cubic-bezier(0.15, 0.85, 0.15, 1)';
            this.canvas.style.transform  = `rotate(${this.currentRotation}deg)`;

            setTimeout(() => {
                this._phase    = 'idle';
                this.animating = false;
                if (callback) callback();
            }, 4100);
        }
    }

    /* =========================================================
       APP
    ========================================================= */
    let wheel   = null;
    let overlay = null;

    function init() {
        const canvas = $('#cw-canvas');
        if (!canvas) return;

        overlay = $('#cw-overlay');

        const cfg    = window.CWWheel || {};
        const slices = Array.isArray(cfg.slices) ? cfg.slices : [];
        if (!slices.length) return;

        wheel = new PrizeWheel(canvas);
        wheel.setSlices(slices);

        // Restore saved info
        const savedEmail = store.get('cw_email');
        const savedPhone = store.get('cw_phone');
        const emailEl = $('#cw-email'), phoneEl = $('#cw-phone');
        if (savedEmail && emailEl) emailEl.value = savedEmail;
        if (savedPhone && phoneEl) phoneEl.value = savedPhone;

        $('#cw-close')?.addEventListener('click', closeOverlay);
        $('#cw-spin-btn')?.addEventListener('click', doSpin);

        // Lưu label gốc của spin button để dùng khi reset
        const spinBtnEl = $('#cw-spin-btn');
        if (spinBtnEl && window.CWWheel) {
            window.CWWheel._spinBtnLabel = spinBtnEl.textContent.trim();
        }

        initCopyBtn();
        initLights();

    /* ---- Copy to clipboard (safe: clipboard API + execCommand fallback) ---- */
    function copyToClipboard(text, onSuccess) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(onSuccess).catch(() => _execCopy(text, onSuccess));
        } else {
            _execCopy(text, onSuccess);
        }
    }
    function _execCopy(text, onSuccess) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0;';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try { document.execCommand('copy'); onSuccess && onSuccess(); } catch (_) {}
        document.body.removeChild(ta);
    }

    /* ---- Copy btn — event delegation ---- */
    function initCopyBtn() {
        const overlayEl = document.getElementById('cw-overlay');
        if (!overlayEl) return;
        overlayEl.addEventListener('click', function (e) {
            const btn = e.target.closest('#cw-copy-btn');
            if (!btn) return;
            const code = btn.dataset.code || '';
            if (!code) return;
            copyToClipboard(code, () => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã sao chép!';
                setTimeout(() => { btn.innerHTML = orig; }, 2000);
            });
        });
    }

        if (cfg.showAutoTrigger) {
            setTimeout(openOverlay, (cfg.triggerDelay || 3) * 1000);
        }

        if (cfg.showButtonTrigger) {
            $('#cw-fab-btn')?.addEventListener('click', openOverlay);
        }
    }

    function openOverlay() {
        if (!overlay) return;
        overlay.classList.add('open');
        requestAnimationFrame(() => requestAnimationFrame(() => {
            wheel?.draw();
            refreshBadge();
        }));
    }

    function closeOverlay() {
        overlay?.classList.remove('open');
        stopConfetti();
    }

    function refreshBadge() {
        const badge = $('#cw-chances');
        if (!badge) return;
        const cfg        = window.CWWheel || {};
        const storageKey = cfg.storageKey || 'cw_spun_0';
        badge.innerHTML  = store.get(storageKey)
            ? 'Bạn đã tham gia quay thưởng'
            : 'Bạn có 1 lượt quay hôm nay';
    }

    /* ---- Audio ---- */
    function playAudio(id) {
        try {
            const el = document.getElementById(id);
            if (!el) return;
            el.currentTime = 0;
            el.play().catch(() => {});
        } catch (_) {}
    }
    function stopAudio(id) {
        try {
            const el = document.getElementById(id);
            if (el) { el.pause(); el.currentTime = 0; }
        } catch (_) {}
    }

    /* ---- Confetti ---- */
    let confettiRAF = null;
    const COLORS = ['#ff595e','#ffca3a','#8ac926','#1982c4','#6a4c93','#ff924c','#00b4d8'];

    function launchConfetti() {
        const canvas  = document.getElementById('cw-confetti');
        if (!canvas) return;
        canvas.style.display = 'block';
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight;
        const ctx = canvas.getContext('2d');
        const particles = Array.from({ length: 120 }, () => ({
            x:    Math.random() * canvas.width,
            y:    Math.random() * canvas.height - canvas.height,
            r:    Math.random() * 8 + 4,
            d:    Math.random() * 120 + 60,
            color: COLORS[Math.floor(Math.random() * COLORS.length)],
            tilt: Math.random() * 10 - 5,
            tiltSpeed: Math.random() * 0.1 + 0.05,
            angle: 0,
        }));
        let tick = 0;
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            tick++;
            particles.forEach(p => {
                p.angle    += p.tiltSpeed;
                p.tilt      = Math.sin(p.angle) * 12;
                p.y        += (Math.cos(p.d) + 2 + p.r / 2) * 0.8;
                p.x        += Math.sin(tick / 30) * 1.5;
                ctx.beginPath();
                ctx.lineWidth = p.r / 2;
                ctx.strokeStyle = p.color;
                ctx.moveTo(p.x + p.tilt + p.r / 4, p.y);
                ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 4);
                ctx.stroke();
                if (p.y > canvas.height) {
                    p.y = -10;
                    p.x = Math.random() * canvas.width;
                }
            });
            confettiRAF = requestAnimationFrame(draw);
        }
        draw();
        setTimeout(stopConfetti, 4500);
    }

    function stopConfetti() {
        if (confettiRAF) { cancelAnimationFrame(confettiRAF); confettiRAF = null; }
        const canvas = document.getElementById('cw-confetti');
        if (canvas) canvas.style.display = 'none';
    }

    function resetSpinBtn(spinBtn) {
        spinBtn.disabled        = false;
        spinBtn.textContent     = window.CWWheel?._spinBtnLabel || spinBtn.dataset.label || 'QUAY';
        spinBtn.style.animation = '';
    }

    function doSpin() {
        if (!wheel || wheel.animating) return;

        const cfg        = window.CWWheel || {};
        const storageKey = cfg.storageKey || 'cw_spun_0';

        const email    = ($('#cw-email')?.value    || '').trim();
        const phone    = ($('#cw-phone')?.value    || '').trim();
        const fullname = ($('#cw-fullname')?.value || '').trim();
        const antibot  = ($('.cw-antibot')?.value  || '');

        // Validate contact theo contactLimit
        const contactLimit = cfg.contactLimit || 'none';
        const i18nV = cfg.i18n || {};

        if (contactLimit === 'email') {
            if (!email) {
                toast('err', '⚠ ' + (i18nV.emailRequired || 'Vui lòng nhập email.'));
                return;
            }
        }

        if (contactLimit === 'phone') {
            if (!phone) {
                toast('err', '⚠ ' + (i18nV.phoneRequired || 'Vui lòng nhập số điện thoại.'));
                return;
            }
        }

        if (contactLimit === 'both') {
            if (!email && !phone) {
                toast('err', '⚠ ' + (i18nV.bothRequired || 'Vui lòng nhập email và số điện thoại.'));
                return;
            }
            if (!email) {
                toast('err', '⚠ ' + (i18nV.emailRequired || 'Vui lòng nhập email.'));
                return;
            }
            if (!phone) {
                toast('err', '⚠ ' + (i18nV.phoneRequired || 'Vui lòng nhập số điện thoại.'));
                return;
            }
        }

        store.set('cw_email', email);
        store.set('cw_phone', phone);

        const spinBtn = $('#cw-spin-btn');
        spinBtn.disabled        = true;
        spinBtn.textContent     = '...';
        spinBtn.style.animation = 'none';
        $('#cw-result')?.classList.remove('show');

        // PHASE 1: Quay ngay + phát âm thanh
        wheel.startSpin();
        playAudio('cw-audio-spin');

        const fallback = () => Math.floor(Math.random() * (cfg.slices?.length || 1));

        request.post(cfg.ajaxUrl, {
            action:     'CouponWheel\\Ajax\\Web\\SpinAjax::spin',
            antibot,
            program_id: cfg.programId,
            fullname,
            email,
            phone,
        }).then(res => {
            const data = res.data;
            if (res.status === 'success') {
                wheel.stopAt(data.slice_index, () => {
                    stopAudio('cw-audio-spin');
                    store.set(storageKey, 1);
                    showResult(data);
                    refreshBadge();
                    resetSpinBtn(spinBtn);
                    if (data.won) {
                        setTimeout(() => { playAudio('cw-audio-win'); launchConfetti(); }, 200);
                    } else {
                        setTimeout(() => playAudio('cw-audio-lose'), 200);
                    }
                });
            }
            else
            {
                wheel.stopAt(fallback(), () =>
                {
                    stopAudio('cw-audio-spin');

                    resetSpinBtn(spinBtn);

                    showEnded(res.message);
                });
            }
        }).catch(() => {
            wheel.stopAt(fallback(), () => {
                stopAudio('cw-audio-spin');
                toast('err', '⚠ Không thể kết nối máy chủ. Vui lòng thử lại.');
                resetSpinBtn(spinBtn);
            });
        });
    }

    function showEnded(message) {
        // Ẩn form + spin btn
        const formSection = $('#cw-form-section');
        const spinBtn     = $('#cw-spin-btn');
        const toastEl     = $('#cw-toast');
        const resultBox   = $('#cw-result');
        const inner       = $('#cw-result-inner');
        const copyBtn     = $('#cw-copy-btn');

        if (formSection) formSection.style.display = 'none';
        if (toastEl)     toastEl.style.display     = 'none';
        if (spinBtn)     { spinBtn.disabled = true; spinBtn.style.opacity = '0.4'; }
        if (copyBtn)     copyBtn.style.display = 'none';

        if (resultBox && inner) {
            resultBox.classList.remove('cw-result--won');
            resultBox.classList.add('cw-result--ended');
            inner.innerHTML = `
                <div class="cw-result-icon" style="font-size:52px;margin-bottom:10px;">🏁</div>
                <h4 class="cw-result-title--ended">Chương trình đã kết thúc</h4>
                <p class="cw-result-subtitle--lost">${message.replace(/^🎁\s*/, '')}</p>`;
            resultBox.classList.add('show');
        }
    }

    function showResult(data) {
        const resultBox   = $('#cw-result');
        const formSection = $('#cw-form-section');
        const toastEl     = $('#cw-toast');
        const inner       = $('#cw-result-inner');
        const copyBtn     = $('#cw-copy-btn');
        if (!resultBox) return;

        const i18n = window.CWWheel?.i18n || {};

        // Ẩn form
        if (formSection) formSection.style.display = 'none';
        if (toastEl)     toastEl.style.display     = 'none';

        const won = !!data.won;

        if (won) {
            resultBox.classList.remove('cw-result--lost');
            resultBox.classList.add('cw-result--won');

            let prizeHtml = '';
            if (data.prize_type === 'product' && data.product_name) {
                prizeHtml = `<div class="cw-prize-box"><span class="cw-prize-icon">🛍️</span><span class="cw-prize-name">${data.product_name}</span></div>`;
                if (data.prize_url) prizeHtml += `<a href="${data.prize_url}" target="_blank" class="cw-result-action-btn">${i18n.viewProduct || 'Xem sản phẩm'} <i class="fa-solid fa-arrow-right"></i></a>`;
                if (copyBtn) copyBtn.style.display = 'none';
            } else {
                prizeHtml = `<div class="cw-prize-box"><span class="cw-prize-icon">🎟️</span><span class="cw-prize-code">${data.prize_text}</span></div>`;
                if (copyBtn) { copyBtn.dataset.code = data.prize_text; copyBtn.style.display = ''; }
            }

            if (inner) inner.innerHTML = `
                <div class="cw-result-icon cw-result-icon--won">🎉</div>
                <h4 class="cw-result-title--won">${i18n.winHeading || 'CHÚC MỪNG!'}</h4>
                <p class="cw-result-subtitle">${i18n.winDescription || 'Phần thưởng của bạn là'}: <strong>${data.prize_name || ''}</strong></p>
                ${prizeHtml}`;
        } else {
            resultBox.classList.add('cw-result--lost');
            resultBox.classList.remove('cw-result--won');
            if (copyBtn) copyBtn.style.display = 'none';
            if (inner) inner.innerHTML = `
                <div class="cw-result-icon cw-result-icon--lost">😔</div>
                <h4 class="cw-result-title--lost">${i18n.loseHeading || 'Chúc bạn may mắn lần sau!'}</h4>
                <p class="cw-result-subtitle--lost">${i18n.loseDescription || ''}</p>`;
        }

        resultBox.classList.add('show');
    }

    /* =========================================================
       LIGHTS RING — vòng đèn nhấp nháy bao quanh wheel
    ========================================================= */
    function initLights() {
        const wheelEl = document.querySelector('.cw-wheel');
        if (!wheelEl) return;

        const COUNT  = 24;
        const COLORS = ['#ffd700', '#ff4444', '#ffffff', '#ffd700', '#ff9900', '#ffffff'];

        const ring = document.createElement('div');
        ring.className = 'cw-lights-ring';

        const dots = [];
        for (let i = 0; i < COUNT; i++) {
            const dot = document.createElement('span');
            dot.className = 'cw-light-dot';
            dot.style.setProperty('--color', COLORS[i % COLORS.length]);
            dot.style.animationDelay = `${(i / COUNT) * 1.2}s`;
            ring.appendChild(dot);
            dots.push(dot);
        }

        wheelEl.appendChild(ring);

        // Tính và cập nhật vị trí các dot theo sin/cos — responsive
        function positionDots() {
            const r = wheelEl.offsetWidth / 2 + 14; // bán kính = nửa wheel + khoảng cách
            const cx = wheelEl.offsetWidth / 2;
            const cy = wheelEl.offsetHeight / 2;
            dots.forEach((dot, i) => {
                const angle = ((Math.PI * 2) / COUNT) * i - Math.PI / 2; // bắt đầu từ 12h
                const x = cx + r * Math.cos(angle);
                const y = cy + r * Math.sin(angle);
                dot.style.left = `${x}px`;
                dot.style.top  = `${y}px`;
            });
        }

        positionDots();

        if (window.ResizeObserver) {
            new ResizeObserver(positionDots).observe(wheelEl);
        } else {
            window.addEventListener('resize', positionDots);
        }
    }

    document.addEventListener('DOMContentLoaded', init);

})();


