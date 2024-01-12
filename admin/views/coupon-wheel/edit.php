<h1 class="ui-title-bar__title mb-3">Cập nhật Vòng Xoay</h1>

<form action="" id="js_coupon_wheel_form">
    <input type="hidden" name="id" value="<?php echo $wheel->id;?>">
    <?php include_once 'form.php';?>
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