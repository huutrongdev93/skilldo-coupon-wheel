@php
    use SkillDo\Cms\Support\Url;
@endphp

@if(!empty($filterProgram))
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" style="border-radius:10px;">
    <div>
        <i class="fa-solid fa-filter me-2"></i>
        <strong>{{ trans('coupon-wheel::admin.log.filter.label') }}:</strong>
        {{ $filterProgram->name }}
    </div>
    <a href="{{ Url::admin('coupon-wheel/logs') }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-xmark me-1"></i>{{ trans('coupon-wheel::admin.log.filter.clear') }}
    </a>
</div>
@endif

{!! Admin::partial('resources/page-default/page-index', [
    'name'   => !empty($filterProgram)
                    ? trans('coupon-wheel::admin.log.page_title') . ' — ' . html_escape($filterProgram->name)
                    : trans('coupon-wheel::admin.log.page_title'),
    'module' => $module,
    'table'  => $table,
]) !!}
