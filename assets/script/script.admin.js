class CouponWheelTableHandle {
	constructor() {
		this.pagingNumber = { 'limit': 20, 'page': 1 }
		this.tableRun = $('#js_coupon_wheel_run_table_result');
		this.table = $('#js_coupon_wheel_table_result');
		this.divPagination = $('#js_coupon_wheel_pagination');
		this.loadRun();
		this.loadOrder();
	}
	loadRun(element) {
		$('.loading').show();

		let data = {
			'action'    : 'AdminCouponWheelAjax::loadRun',
		}

		let self = this;

		request.post(ajax, data).then(function (response) {

			$('.loading').hide();

			if (response.status === 'error') SkilldoMessage.error(response.message);

			if (response.status === 'success') {

				response.data = decodeURIComponent(atob(response.data).split('').map(function (c) {
					return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
				}).join(''));

				self.tableRun.html(response.data);
			}
		});
	}
	loadOrder(element) {

		$('.loading').show();

		let data = {
			'action'    : 'AdminCouponWheelAjax::loadOrder',
			'limit'     : this.pagingNumber.limit,
			'page'      : this.pagingNumber.page,
		}

		let self = this;

		request.post(ajax, data).then(function (response) {

			$('.loading').hide();

			if (response.status === 'error') SkilldoMessage.error(response.message);

			if (response.status === 'success') {

				response.data = decodeURIComponent(atob(response.data).split('').map(function (c) {
					return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
				}).join(''));

				response.pagination = decodeURIComponent(atob(response.pagination).split('').map(function (c) {
					return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
				}).join(''));

				self.table.html(response.data);

				self.divPagination.html(response.pagination);
			}
		});
	}
	pagination(element) {

		this.pagingNumber.page = element.data('page-number');

		this.loadOrder();

		return false;
	}
	clickStatus(element) {
		let self = this;
		let data =  {
		    action: 'AdminCouponWheelAjax::status',
			status: element.attr('data-status'),
			id: element.attr('data-id')
		}

		request.post(ajax, data).then(function (response) {
		    if (response.status === 'success') {
			    self.loadRun();
			    self.loadOrder();
		    }
		});
	}
}

class CouponWheelHandle {
	constructor() {
	}

	clickItemImg(element) {
		$(element).closest('.display-items-img').find('.item-img').removeClass('active');
		element.addClass('active');
	}

	add(element) {
		let data = element.serializeJSON();

		data.action = 'AdminCouponWheelAjax::add';

		request.post(ajax, data).then(function(response) {
			SkilldoMessage.response(response);
		    if (response.status === 'success') {
				location.href = 'admin/plugins/coupon-wheel';
		    }
		});

		return false;
	}
	save(element) {
		let data = element.serializeJSON();

		data.action = 'AdminCouponWheelAjax::save';

		request.post(ajax, data).then(function(response) {
			SkilldoMessage.response(response);
		});

		return false;
	}
}

class CouponWheelLogTableHandle {
	constructor() {
		this.pagingNumber = { 'limit': 5, 'page': 1 }
		this.table = $('#js_wheel_log_table_result');
		this.divPagination = $('#js_wheel_log_pagination');
		this.load();
		this.isRead();
	}
	isRead(element) {

		let data = {
			action : 'AdminCouponWheelLogAjax::isRead'
		}

		request.post(ajax, data).then(function (response) {});
	}
	load(element) {

		$('.loading').show();

		let data = $(':input', $('#js_wheel_log_search_form')).serializeJSON();

		data.action = 'AdminCouponWheelLogAjax::load';

		data.limit = this.pagingNumber.limit;

		data.page = this.pagingNumber.page;

		let self = this;

		request.post(ajax, data).then(function (response) {

			$('.loading').hide();

			if (response.status === 'error') SkilldoMessage.error(response.message);

			if (response.status === 'success') {

				response.data = decodeURIComponent(atob(response.data).split('').map(function (c) {
					return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
				}).join(''));

				response.pagination = decodeURIComponent(atob(response.pagination).split('').map(function (c) {
					return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
				}).join(''));

				self.table.html(response.data);

				self.divPagination.html(response.pagination);
			}
		});
	}
	pagination(element) {

		this.pagingNumber.page = element.data('page-number');

		this.load();

		return false;
	}
	search(element) {
		this.pagingNumber.page = 1;
		this.load();
		return false;
	}
}