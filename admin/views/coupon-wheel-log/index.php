<div class="ui-layout">
    <div class="col-md-12">
        <div class="ui-title-bar__group">
            <h1 class="ui-title-bar__title">Thống kê lượt chơi</h1>
        </div>

        <div class="box" style="overflow:inherit;">
	        <div class="box-heading">
		        <form id="js_wheel_log_search_form" class="order_search_form d-flex align-items-center gap-1" role="form" autocomplete="off">
			        <div class="form-group mb-0">
				        <input type="text" name="name" value="" id="name" class=" form-control" placeholder="Tên khách hàng" field="name"  />
			        </div>
			        <div class="form-group mb-0">
				        <input type="text" name="phone" value="" id="phone" class=" form-control" placeholder="Số điện thoại" field="phone"  />
			        </div>
			        <div class="form-group mb-0">
				        <input type="text" name="time" value="" id="time" class=" form-control daterange" placeholder="" field="time"  />
			        </div>
			        <button type="submit" class="btn btn-blue" style="width: 100px;"><i class="fad fa-search"></i> Tìm</button>
		        </form>
	        </div>
            <div class="box-content">
                <div class="table-responsive">
                    <table class="display table table-striped media-table table-wheel-log">
                        <thead>
                        <tr>
                            <th class="manage-column">Tên chiến dịch</th>
                            <th class="manage-column">Thông tin</th>
                            <th class="manage-column">Phần thưởng</th>
                            <th class="manage-column">Giá trị</th>
                            <th class="manage-column">Thời gian quay</th>
                            <th class="manage-column">#</th>
                        </tr>
                        </thead>
                        <tbody id="js_wheel_log_table_result"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="paging" id="js_wheel_log_pagination"></div>
    </div>
</div>

<script>
	$(function () {
		let couponWheelLogTable = new CouponWheelLogTableHandle();
		$(document)
			.on('click', '#js_wheel_log_pagination .pagination-item', function () {
				return couponWheelLogTable.pagination($(this))
			})
			.on('submit', '#js_wheel_log_search_form', function () {
				return couponWheelLogTable.search($(this));
			})
	})
</script>
