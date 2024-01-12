<div id="couponWheel<?php echo $wheel->id?>" class="couponWheel_main" data-wheel="<?php echo htmlentities(json_encode($wheel));?>" style="
	--couponWheel-fontSize: 14px;
    --couponWheel-txtColor: <?php echo $wheelDisplay['textColor'];?>;
    --couponWheel-btnBgColor: <?php echo $wheelDisplay['btnRunBgColor'];?>;
    --couponWheel-btnTxtColor: <?php echo $wheelDisplay['btnRunTxtColor'];?>;">
	<div class="couponWheel_popup_shadow"></div>
	<div class="couponWheel_popup">
		<div class="couponWheel_popup_background" style="<?php echo WheelHelper::displayBackground($wheelDisplay['background']);?>">
			<div class="couponWheel_popup_wheel_container">
				<div class="couponWheel_wheel_container">
					<?php if (!Option::get('couponWheel_reduce_requests')) { ?>
						<div class="couponWheel_wheel_crop" style="overflow: visible; position: absolute; z-index: 1000;">
							<img class="couponWheel_wheel_img" style="width: 101.15%; max-width: none;" src="<?php echo WheelHelper::displayFrame($wheelDisplay['frame']);?>" alt="">
						</div>
					<?php } ?>
					<div class="couponWheel_wheel_crop">
						<div class="couponWheel_wheel">
							<img class="couponWheel_wheel_img" src="<?php echo WheelHelper::displayWheel($wheelDisplay['style']);?>" alt="vòng quay may mắn">
							<img class="couponWheel_wheel_img_center" src="<?php echo WheelHelper::displayImgCenter($wheelDisplay['center']);?>" alt="vòng quay may mắn">
							<div class="couponWheel_slice_labels <?php echo $wheelDisplay['style'];?>">
								<?php foreach(range(1,12) as $i) { ?>
								<div class="couponWheel_slice_label"><?php echo $wheel->{"slice$i"."_label"} ?></div>
								<?php } ?>
							</div>
						</div>
					</div>
					<img src="<?php echo $wheelPath ?>/assets/images/marker.png" class="couponWheel_marker" alt="coupon Wheel marker">
				</div>
			</div>
			<div class="couponWheel_popup_form_container">
				<div class="couponWheel_form">
					<div class="couponWheel_popup_close_container"><div class="couponWheel_popup_close_btn">×</div></div>
                    <?php if (!empty($wheel->popup_header_image)) { ?><img class="couponWheel_popup_header_image" src="<?php echo $wheel->popup_header_image?>"><?php } ?>
					<p></p>
					<form class="couponWheel_form_stage1">
						<div class="couponWheel_popup_heading_text"><?php echo $wheelText['popup_heading_text'];?></div>
						<div class="couponWheel_popup_main_text"><?php echo do_shortcode($wheelText['popup_main_text']) ?></div>
                        <?php if($wheel->require_fullname) { ?>
	                        <div class="form-group">
		                        <label for="" class="mb-2">Họ Tên</label>
		                        <input
			                        name="fullname"
			                        value="<?php echo (have_posts($currentUser)) ? $currentUser->firstname.' '.$currentUser->lastname : '' ; ?>"
			                        type="text"
			                        placeholder="<?php echo $wheelText['lang_enter_your_full_name'];?>"
			                        required>
	                        </div>
                        <?php } ?>
                        <?php if($wheel->require_email) { ?>
	                        <div class="form-group mt-4">
		                        <label for="" class="mb-2">Email</label>
		                        <input
			                        name="email"
			                        value="<?php echo (have_posts($currentUser)) ? $currentUser->email : '' ; ?>"
			                        type="email"
			                        placeholder="<?php echo $wheelText['lang_enter_your_email'];?>"
			                        required>
	                        </div>

                        <?php } ?>
                        <?php if($wheel->require_phone) { ?>
	                        <div class="form-group mt-4">
		                        <label for="" class="mb-2">Số Điện Thoại</label>
		                        <input
			                        name="phone"
			                        value="<?php echo (have_posts($currentUser)) ? $currentUser->phone : '' ; ?>"
			                        type="text"
			                        placeholder="<?php echo $wheelText['lang_enter_phone_number']; ?>"
			                        required>
	                        </div>
                        <?php } ?>
                        <div class="couponWheel_ajax_loader"><div></div><div></div><div></div></div>
                        <div class="couponWheel_popup_form_error_text"></div>
                        <button class="couponWheel_stage1_submit_btn mb-1 mt-1" type="submit" disabled><?php echo $wheelText['lang_spin_button'];?></button>
						<div class="couponWheel_popup_rules_text">
                            <?php echo do_shortcode($wheelText['popup_rules_text']) ?>
						</div>
						<input type="hidden" name="wheel_hash" value="<?php echo $wheel->id ?>">
					</form>
					<div class="couponWheel_form_stage2 couponWheel_hidden">
						<div class="couponWheel_popup_heading_text"></div>
						<div class="couponWheel_popup_main_text"></div>
						<button class="couponWheel_stage2_continue_btn mb-1"><?php echo $wheelText['lang_continue_button']?></button>
                        <a class="couponWheel_spin_again_btn btn btn-effect-default"><?php echo $wheelText['lang_spin_again']?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if($wheelDisplay['triggerOpen'] == 1) { ?>
	<div class="couponWheel_trigger" style="
		--couponWheel-trigger-effect:<?php echo $wheelDisplay['triggerEffect'];?>;
		--couponWheel-trigger-color:<?php echo $wheelDisplay['triggerBg'];?>;
	">
        <?php $giftIcon = (empty($wheelDisplay['triggerIcon'])) ? WheelHelper::displayGift($wheelDisplay['triggerStyle']) : $wheelDisplay['triggerIcon']; ?>
		<a style="cursor: pointer" class="couponWheel_trigger_box">
			<?php Template::img($giftIcon, 'gift icon',['class' => 'animated']);?>
		</a>
	</div>
<?php } ?>

<script data-cfasync="false">
    <?php echo sprintf("// %s \n",Option::get('couponWheel_version')) ?>
    var wheel = $('.couponWheel_main').data('wheel');

    var autoShow = getCookie('couponWheel_auto_show_'+wheel.id);

	var couponWheel = new CouponWheel({
		wheel_hash: wheel.id,
		wheel_dom:'#couponWheel'+wheel.id,
		timed_trigger: wheel.timed_trigger == 1,
		exit_trigger: wheel.exit_trigger == 1,
		auto_show: (autoShow == true || autoShow == undefined || autoShow == ''),
		show_popup_after: wheel.show_popup_after,
		preview_key: false,
		prevent_triggers_on_mobile: wheel.prevent_triggers_on_mobile,
		kiosk_mode: wheel.kiosk_mode,
		confirm_close_text: wheel.lang_close
	});
	setCookie('couponWheel_auto_show_'+wheel.id, false, 1);
</script>
