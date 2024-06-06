<h1 class="ui-title-bar__title mb-3">{{trans('admin.wheel.edt.heading')}}</h1>

<form action="" id="js_coupon_wheel_form">
    <input type="hidden" name="id" value="{!! $wheel->id !!}">
    {!! Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel/form', [
        'formBase' => $formBase,
        'formAward' => $formAward,
        'formText' => $formText,
        'formTrigger' => $formTrigger,
        'formDisplay' => $formDisplay,
        'wheelDisplay' => $wheelDisplay
    ]); !!}
</form>

<script>
	$(function (){
		let couponWheel = new CouponWheelHandle();

		$(document)
			.on('click', '.item-img', function () {
				couponWheel.clickItemImg($(this))
			})
			.on('submit', '#js_coupon_wheel_form', function () {
				return couponWheel.save($(this))
			})
	})
</script>