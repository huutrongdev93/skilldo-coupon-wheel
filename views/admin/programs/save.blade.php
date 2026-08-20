@php
    use SkillDo\Cms\Support\Url;
    $isEdit = !empty($object);
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        {!! Admin::partial('resources/components/breadcrumb') !!}
        <h1 class="ui-title-bar__title text-2xl">{{ $isEdit ? trans('coupon-wheel::admin.program.edit_title') : trans('coupon-wheel::admin.program.add_title') }}</h1>
    </div>
    <div class="page-header-right">
        <a href="{!! Url::admin('coupon-wheel') !!}" class="btn btn-default">
            <i class="fa-solid fa-arrow-left"></i> {{ trans('coupon-wheel::admin.btn.back') }}
        </a>
        <button type="button" class="btn btn-blue js-cw-save">
            <i class="fa-solid fa-floppy-disk"></i> {{ trans('coupon-wheel::admin.btn.save') }}
        </button>
    </div>
</div>

<ul class="nav nav-tabs nav-tabs-horizontal mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" href="#base" data-bs-toggle="tab" role="tab" aria-controls="base" aria-selected="true">{{ trans('coupon-wheel::admin.program.tab.base') }}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#slices" role="tab" data-bs-toggle="tab" aria-controls="slices" aria-selected="false" tabindex="-1">{{ trans('coupon-wheel::admin.program.tab.slices') }}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#display" role="tab" data-bs-toggle="tab" aria-controls="display" aria-selected="false" tabindex="-1">{{ trans('coupon-wheel::admin.program.tab.display') }}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#text" role="tab" data-bs-toggle="tab" aria-controls="text" aria-selected="false" tabindex="-1">{{ trans('coupon-wheel::admin.program.tab.text') }}</a>
    </li>
</ul>

<div class="tab-content mt-3 cw-program-form" id="cw-program-form" data-id="{{ $isEdit ? $object->id : 0 }}">
    <div class="tab-pane fade active show cw-program-form-base" id="base" aria-labelledby="base" tabindex="0" role="tabpanel">
        {!! view('coupon-wheel::admin/programs/save-tab/tab-base', compact('formInfo', 'formCondition', 'formDisplay')) !!}
    </div>

    <div class="tab-pane fade cw-program-form-slices" id="slices" aria-labelledby="slices" tabindex="0" role="tabpanel">
        {!! view('coupon-wheel::admin/programs/save-tab/tab-slices') !!}
    </div>

    <div class="tab-pane fade cw-program-form-display" id="display" aria-labelledby="display" tabindex="0" role="tabpanel">
        {!! view('coupon-wheel::admin/programs/save-tab/tab-display', compact('settingDisplay', 'formTrigger')) !!}
    </div>

    <div class="tab-pane fade cw-program-form-text" id="text" aria-labelledby="text" tabindex="0" role="tabpanel">
        {!! view('coupon-wheel::admin/programs/save-tab/tab-text', compact('formDisplayText')) !!}
    </div>
</div>


<script>
    window.CWData = {
        slices:            {!! $slices_json !!},
        difficultyWeights: {!! json_encode($difficulty_weights) !!},
        ajaxUrl:           typeof ajax !== 'undefined' ? ajax : '/ajax',
        backUrl:           "{!! Url::admin('coupon-wheel') !!}",
        i18n: {
            triggerAtLeastOne: {!! json_encode(trans('coupon-wheel::admin.form.trigger_type.at_least_one')) !!},
        }
    };
</script>
