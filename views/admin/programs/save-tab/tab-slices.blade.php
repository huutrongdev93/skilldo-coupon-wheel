<div class="row">
    <div class="col-md-9">
        {{-- Danh sách ô vòng quay --}}
        <div class="box cw-slices-box">
            <div class="box-header">
                <h3 class="box-title mb-0">{{ trans('coupon-wheel::admin.slice.box_title') }}</h3>
                <button type="button" class="btn btn-sm btn-green js-cw-add-slice">
                    <i class="fa-solid fa-plus"></i> {{ trans('coupon-wheel::admin.slice.btn_add') }}
                </button>
            </div>
            <div class="box-content p-2">
                <div class="row" id="cw-slices-container"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box mb-3">
            <div class="box-header"><h3 class="box-title">{{ trans('coupon-wheel::admin.slice.prob.title') }}</h3></div>
            <div class="box-content">
                <p class="text-muted mb-0" id="cw-prob-empty">{{ trans('coupon-wheel::admin.slice.prob.empty') }}</p>
                <div id="cw-prob-list"></div>
                <div id="cw-prob-warning" class="alert alert-danger mt-2" style="display:none;">{{ trans('coupon-wheel::admin.slice.prob.warning') }}</div>
                <br />
                {!! \SkillDo\Cms\Support\Admin::alert('info', trans('coupon-wheel::admin.slice.quantity.note'), ['icon' => false, 'heading' => false]) !!}
            </div>
        </div>
    </div>
</div>

{{-- ===== TEMPLATE: một slice row ===== --}}
<template id="cw-slice-template">
    <div class="cw-slice-row col-md-6 mb-3" data-index="${index}">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                <span class="cw-slice-title fw-semibold">{{ trans('coupon-wheel::admin.slice.title_prefix') }}${num}</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary cw-prob-badge">~%</span>
                    <button type="button" class="btn btn-sm btn-danger js-cw-remove-slice">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body px-3 py-2">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.name') }}</label>
                        <input type="text" class="form-control form-control-sm cw-field-name" placeholder="{{ trans('coupon-wheel::admin.slice.field.name_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.difficulty') }}</label>
                        <select class="form-select cw-field-difficulty">
                            @foreach($difficulty_options as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="alert alert-primary" role="alert">
                    <div class="row">
                        <div class="col-md-5 mb-2">
                            <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.prize_type') }}</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cw-prize-type" type="radio" name="prize_type_${index}" value="text" checked> {{ trans('coupon-wheel::admin.slice.prize_type.text') }}
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cw-prize-type" type="radio" name="prize_type_${index}" value="product"> {{ trans('coupon-wheel::admin.slice.prize_type.product') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 mb-2 cw-prize-text-wrap">
                            <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.prize_text') }}</label>
                            <input type="text" class="form-control form-control-sm cw-field-prize-text" placeholder="{{ trans('coupon-wheel::admin.slice.field.prize_text_placeholder') }}">
                        </div>
                        <div class="col-md-7 mb-2 cw-prize-product-wrap" style="display:none;">
                            <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.product') }}</label>
                            <div class="cw-popover-product-placeholder"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-4">
                        <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.quantity') }}</label>
                        <input type="number" class="form-control form-control-sm cw-field-quantity" min="0" value="0" placeholder="0">
                    </div>
                    <div class="col-4">
                        <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.bg_color') }}</label>
                        <input type="text" value="#4e73df" data-color-picker="1" class="form-control cw-field-bg-color">
                    </div>
                    <div class="col-4">
                        <label class="form-label form-label-sm">{{ trans('coupon-wheel::admin.slice.field.text_color') }}</label>
                        <input type="text" value="#ffffff" data-color-picker="1" class="form-control cw-field-text-color">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- ===== HTML template cho popoverAdvance sản phẩm (dùng placeholder để JS thay thế) ===== --}}
@php
    $popoverTplHtml = \SkillDo\Cms\Form\Form::popoverAdvance('__CW_FIELD_NAME__', [
        'label'    => 'Sản phẩm',
        'search'   => 'products',
        'multiple' => false,
        'id'       => '__CW_IDX__',
    ])->render();
@endphp
<script>
    window.CWProductPopoverTpl = @json($popoverTplHtml);
</script>
