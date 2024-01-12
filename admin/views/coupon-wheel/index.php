<div class="ui-layout">
    <div class="col-md-12">
        <div class="ui-title-bar__group">
            <h1 class="ui-title-bar__title">Danh sách chiến dịch vòng quay</h1>
        </div>
        <div class="box" style="overflow:inherit;">
            <div class="box-content">
                <p class="heading p-2 pb-0">Chiến dịch đang chạy</p>
                <div class="table-responsive">
                    <table class="display table table-striped media-table">
                        <thead>
                            <tr>
                                <th class="manage-column">Tên chiến dịch</th>
                                <th class="manage-column">Trạng thái</th>
                                <th class="manage-column text-center">Số lượt quay</th>
                                <th class="manage-column">Ngày tạo</th>
                                <th class="manage-column">#</th>
                            </tr>
                        </thead>
                        <tbody id="js_coupon_wheel_run_table_result"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box" style="overflow:inherit;">
            <div class="box-content">
                <p class="heading p-2 pb-0">Chiến dịch khác</p>
                <div class="table-responsive">
                    <table class="display table table-striped media-table">
                        <thead>
                            <tr>
                                <th class="manage-column">Tên chiến dịch</th>
                                <th class="manage-column">Trạng thái</th>
                                <th class="manage-column text-center">Số lượt quay</th>
                                <th class="manage-column">Ngày tạo</th>
                                <th class="manage-column">#</th>
                            </tr>
                        </thead>
                        <tbody id="js_coupon_wheel_table_result"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="paging" id="js_coupon_wheel_pagination"></div>
    </div>
</div>

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
