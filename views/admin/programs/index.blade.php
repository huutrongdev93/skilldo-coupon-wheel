{!! Admin::partial('resources/page-default/page-index', [
    'name'   => trans('coupon-wheel::admin.program.page_title'),
    'module' => $module,
    'table'  => $table,
]) !!}

<script>
window.CWData = window.CWData || {};
window.CWData.ajaxUrl = typeof ajax !== 'undefined' ? ajax : '/ajax';
</script>
