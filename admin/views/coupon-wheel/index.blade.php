<div class="ui-title-bar__group">
    <h1 class="ui-title-bar__title mb-2">{{trans('admin.wheel.index.heading')}}</h1>
    <div class="text-right">
        <a href="{!! Url::admin('plugins/coupon-wheel/add') !!}" class="btn btn btn-green">{!! Admin::icon('add') !!} {{trans('admin.wheel.index.button.add')}}</a>
    </div>
</div>
<div class="box mb-4" style="overflow:inherit;">
    <div class="box-header"><h4 class="box-title">{{trans('admin.wheel.index.running.heading')}}</h4></div>
    <div class="box-content p-0">
        <div class="table-responsive">
            <table class="display table table-striped media-table">
                <thead>
                    <tr>
                        <th class="manage-column">{{trans('admin.wheel.index.table.title')}}</th>
                        <th class="manage-column">{{trans('admin.wheel.index.table.status')}}</th>
                        <th class="manage-column text-center">{{trans('admin.wheel.index.table.number')}}</th>
                        <th class="manage-column">{{trans('admin.wheel.index.table.created')}}</th>
                        <th class="manage-column">#</th>
                    </tr>
                </thead>
                <tbody id="js_coupon_wheel_run_table_result"></tbody>
            </table>
        </div>
    </div>
</div>
<div class="box" style="overflow:inherit;">
    <div class="box-header"><h4 class="box-title">{{trans('admin.wheel.index.stop.heading')}}</h4></div>
    <div class="box-content p-0">
        <div class="table-responsive">
            <table class="display table table-striped media-table">
                <thead>
                    <tr>
                        <th class="manage-column">{{trans('admin.wheel.index.table.title')}}</th>
                        <th class="manage-column">{{trans('admin.wheel.index.table.status')}}</th>
                        <th class="manage-column text-center">{{trans('admin.wheel.index.table.number')}}</th>
                        <th class="manage-column">{{trans('admin.wheel.index.table.created')}}</th>
                        <th class="manage-column">#</th>
                    </tr>
                </thead>
                <tbody id="js_coupon_wheel_table_result"></tbody>
            </table>
        </div>
    </div>
</div>
<div class="paging" id="js_coupon_wheel_pagination"></div>

<script>
	$(function () {
		let couponWheelTable = new CouponWheelTableHandle();
		$(document)
			.on('click', '.js_coupon_wheel_btn_status', function () {
				return couponWheelTable.clickStatus($(this))
			})
			.on('click', '#js_coupon_wheel_pagination .pagination-item', function () {
				return couponWheelTable.pagination($(this))
			})
	})
</script>
