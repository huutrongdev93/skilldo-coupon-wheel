/*
	Plugin Name: Coupon Wheel For WooCommerce and WordPress
	Description: One nice gamified exit-intent popup plugin :-)
	Author: Jure Hajdinjak / Copyright (c) 2017-2019 Jure Hajdinjak
*/
const coupon_wheel_notice_translations = {"h": "h", "m": "m", "s": "s"};

if(window.couponWheel_AnimFrame === undefined) {
	window.couponWheel_AnimFrame = (function(){
		return window.requestAnimationFrame		||
		window.webkitRequestAnimationFrame		||
		window.mozRequestAnimationFrame			||
		function(){
			alert('UPGRADE BROWSER');
		};
	})();
}

function CouponWheel(init_params)
{
	//init vars
	this.wheel_hash = init_params.wheel_hash;
	this.wheel_dom = init_params.wheel_dom;
	this.timed_trigger = init_params.timed_trigger;
	this.exit_trigger = init_params.exit_trigger;
	this.show_popup_after = init_params.show_popup_after;
	this.preview_key = init_params.preview_key;
	this.prevent_triggers_on_mobile = init_params.prevent_triggers_on_mobile;
	this.kiosk_mode = init_params.kiosk_mode;
	this.confirm_close_text = init_params.confirm_close_text;
	this.auto_show = init_params.auto_show;
	this.on_win_url = '';
	this.exit_triggered = false;
	this.can_show = true;
	this.can_close = true;
	this.reload_page = false;
	this.window_scroll_value = 0;

	this.ios_input_workaround_enable = function()
	{
		// iOS fixed input render bug workaround
		if (this.is_ios() === false) return;
		this.window_scroll_value = jQuery(window).scrollTop();
		$(window).scrollTop(0);
		$('.couponWheel_popup').css({'position':'absolute', top:0});
		$('body').addClass('couponWheel_ios_stop_scrolling');
		// 
	};
	
	this.ios_input_workaround_disable = function()
	{
		if (this.is_ios() === false) return;
		$('body').removeClass('couponWheel_ios_stop_scrolling');
		$(window).scrollTop(this.window_scroll_value);
	};
	
	this.is_ios = function()
	{
		if (/iPad|iPhone|iPod/.test(navigator.userAgent)) return true;
		return false;
	};
	
	this.is_embed = function()
	{
		const i = $(this.wheel_dom).parent()[0].className.indexOf('couponWheel_embed');
		return i !== -1;
	};

	this.show_popup = function(exit_trigger_check)
	{

		if (exit_trigger_check === undefined)
		{
			exit_trigger_check = true;
		}
		
		if (this.can_show === false) return;

		if (exit_trigger_check && this.exit_triggered) return;

		this.exit_triggered = true;

		this.can_show = false;

		if (!this.is_embed()) this.ios_input_workaround_enable();

		$(this.wheel_dom).css('pointer-events','auto');

		$(this.wheel_dom + ' .couponWheel_popup').show();

		if (!this.is_embed()) {
			$(this.wheel_dom+' .couponWheel_popup_shadow').show();
			$(this.wheel_dom+' .couponWheel_popup').css({'left':'-100%'});
			$(this.wheel_dom+' .couponWheel_popup').animate({left: '0px'},500,'easeOutExpo');
		}

		$.post(ajax, {
			action: 'CouponWheelAjax::event',
			code: 'show_popup',
			id: this.wheel_hash,
		}, function () {}, 'json');

	};

	this.close_popup = function()
	{
		if(this.can_close === false) return;
		this.can_close = false;
		$(this.wheel_dom).css('pointer-events','none');
		$(this.wheel_dom+' .couponWheel_popup').css({'left':'0%'});
		$(this.wheel_dom+' .couponWheel_popup').animate({left: '-100%'},600,'easeInExpo',function(){
			$(this.wheel_dom+' .couponWheel_popup_shadow').hide();
			$(this.wheel_dom+' .couponWheel_popup').hide();
			this.can_close = true;
			this.can_show = true;
			
			if (this.kiosk_mode)
			{
				this.reset_popup();
				return;
			}
			
			if (this.reload_page) location.reload();
			this.ios_input_workaround_disable();
		}.bind(this));
	};
	
	this.reset_popup = function()
	{
		const is_embed = this.is_embed();
		$(this.wheel_dom).remove();
		window['couponWheel'] = undefined;
		window['wheel'] = undefined;
		window['autoShow'] = undefined;
		window.couponWheel_manual_trigger(this.wheel_hash,is_embed);
	};
	
	this.hide_popup = function()
	{
		$(this.wheel_dom+' .couponWheel_popup_shadow').hide();
		$(this.wheel_dom+' .couponWheel_popup').hide();
	};
	
	this.go_to_stage2 = function()
	{
		$(this.wheel_dom+' .couponWheel_popup').animate({'scrollTop':   $(this.wheel_dom+' .couponWheel_form_stage2').offset().top}, 650,'swing');
		$(this.wheel_dom+' .couponWheel_form_stage1').hide();
		$(this.wheel_dom+' .couponWheel_form_stage2').removeClass('couponWheel_hidden');
		$(this.wheel_dom+' .couponWheel_form_stage2').addClass('animated bounceIn');
		this.can_close = true;
	};
	
	this.submit_form_done = function(response)
	{
		$(this.wheel_dom+' .couponWheel_ajax_loader').hide();
		
		if (response.hide_popup === true)
		{
			this.hide_popup();
			return;
		}
		
		if (response.status == 'error')
		{
			this.can_close = true;
			$(this.wheel_dom+' .couponWheel_popup_form_error_text').html(response.message);
			$(this.wheel_dom+' .couponWheel_form_stage1 *').attr('disabled',false);
			return;
		}
		
		if (response.status == 'success')
		{
			$(this.wheel_dom+' .couponWheel_popup').animate({'scrollTop':   $(this.wheel_dom+' .couponWheel_wheel_crop').offset().top}, 650,'swing');
			$(this.wheel_dom+' .couponWheel_form_stage2 .couponWheel_popup_heading_text').html(response.stage2_heading_text);
			$(this.wheel_dom+' .couponWheel_form_stage2 .couponWheel_popup_main_text').html(response.stage2_main_text);
			this.start_wheel_animation(response.wheel_deg_end, response.wheel_time_end);
			this.reload_page = response.reload_page;
			if (response.on_win_url !== undefined) this.on_win_url = response.on_win_url;
			return;
		}
	
		this.can_close = true;
	};
	
	this.submit_form = function(formData)
	{
		$(this.wheel_dom+' .couponWheel_ajax_loader').show();
		$(this.wheel_dom+' .couponWheel_form_stage1 *').attr('disabled',true);
		$(this.wheel_dom+' .couponWheel_popup_form_error_text').html('');

		formData.action  = 'CouponWheelAjax::run';

		let self = this;

		$.post(ajax, formData, function () {}, 'json').done(function (response) {
			self.submit_form_done(response);
		});
	};
	
	this.start_wheel_animation = function(wheel_deg_end,wheel_time_end)
	{
		this.wheel_deg_end = wheel_deg_end;
		this.wheel_time_end = wheel_time_end;

		this.wheel_time = 0;
		this.wheel_deg = 0;
		
		const parent = this;
		this.animation_start_time = null;
		couponWheel_AnimFrame(parent.animate.bind(parent));
	};
	
	//animations
	this.wheel_time = 0;
	this.wheel_deg = 0;
	this.wheel_deg_end = 0;
	this.wheel_time_end = 0;
	this.wheel_deg_ease = 0;
	this.animation_start_time = null;
	
	this.wheel_ease = function(x)
	{
		return 1 - Math.pow( 1 - x, 5 );
	};
	
	this.marker_ease = function(x)
	{
		var n = (- Math.pow((1-(x*2)),2)+1);
		if (n < 0) n = 0;
		return n;
	};

	this.animate = function(timestamp)
	{
		if (!this.animation_start_time) this.animation_start_time = timestamp;
		this.wheel_time = timestamp - this.animation_start_time;
		if(this.wheel_time > this.wheel_time_end) this.wheel_time = this.wheel_time_end;
		this.wheel_deg_ease = this.wheel_ease( (( this.wheel_deg_end / this.wheel_time_end ) * this.wheel_time) / this.wheel_deg_end );
		this.wheel_deg = this.wheel_deg_ease * this.wheel_deg_end;
		
		if(this.wheel_deg_ease > 0.99){
			$(this.wheel_dom+' .couponWheel_marker').css({'transform' : 'translateY(-50%) rotate3d(0,0,1,0deg)','-webkit-transform' : 'translateY(-50%) rotate3d(0,0,1,0deg)'});
		}

		const ticker_calc = this.wheel_deg - Math.floor(this.wheel_deg / 360) * 360;

		let i;

		for (i = 1; i <= 12; i++) {
			if ((ticker_calc >= (i*30)-18) && (ticker_calc <= (i*30)))
			{
				let aa = 0.2;
				if(this.wheel_deg_ease > aa) aa=this.wheel_deg_ease;

				const bb = this.marker_ease(-(((i * 30) - 18) - ticker_calc) / 10) * (30 * aa);

				$(this.wheel_dom+' .couponWheel_marker').css({'transform' : 'translateY(-50%)  rotate3d(0,0,1,'+ (0-bb) + 'deg)','-webkit-transform' : 'translateY(-50%)  rotate3d(0,0,1,'+ (0-bb) + 'deg)'});
			}
		}

		$(this.wheel_dom+' .couponWheel_wheel').css({'transform' : 'rotate3d(0,0,1,'+ this.wheel_deg +'deg)','-webkit-transform' : 'rotate3d(0,0,1,'+ this.wheel_deg +'deg)'});
		if(timestamp - this.animation_start_time > this.wheel_time_end)
		{
			this.go_to_stage2();
			return;
		}

		couponWheel_AnimFrame(this.animate.bind(this));
	};

	//main init
	$(this.wheel_dom+' .couponWheel_stage1_submit_btn').attr('disabled',false);

	$(this.wheel_dom+' .couponWheel_stage2_continue_btn').click(function()
	{
		if (this.on_win_url.length > 0) {
			window.location = this.on_win_url;
			return;
		}
			
		this.close_popup();
		
	}.bind(this));
	
	$(this.wheel_dom+' .couponWheel_spin_again_btn').click(function(){
		this.kiosk_mode = true;
		this.close_popup();
	}.bind(this));
	$(this.wheel_dom+' .couponWheel_popup_close_btn').click(function(){this.close_popup(); }.bind(this));
	$(this.wheel_dom+' .couponWheel_popup_shadow').click(function(){ if (confirm(this.confirm_close_text+'?') == true ) { this.close_popup(); } }.bind(this));
	$(this.wheel_dom+' .couponWheel_form_stage1').on('submit',function(event){
		event.preventDefault();
		this.can_close = false;
		this.submit_form($(this.wheel_dom+' .couponWheel_form_stage1').serializeJSON());
	}.bind(this));
	$(document).on('click', '.couponWheel_trigger_box', function(event){
		this.show_popup(0);
	}.bind(this))



	const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

	if (this.prevent_triggers_on_mobile === undefined) this.prevent_triggers_on_mobile = false;
	
	if (((isMobile) && (this.prevent_triggers_on_mobile)) === false)
	{
		if (this.timed_trigger)
		{
			if (this.auto_show === true) {

				new CouponWheel_DialogTrigger(function(){
					this.show_popup();
				}.bind(this), {
					trigger: 'timeout',
					timeout: this.show_popup_after*1000
				});
			}
		}
		
		if (this.exit_trigger)
		{
			// new couponwheel_DialogTrigger(function(){this.show_popup();}.bind(this), { trigger: 'exitIntent' });
			// new couponwheel_DialogTrigger(function(){this.show_popup();}.bind(this), { trigger: 'scrollUp', percent: 10, scrollInterval: 150 });
		}
	}

}

function couponWheel_manual_trigger(wheel_hash, embed)
{
	if (typeof embed == 'undefined') embed = false;

	if(typeof (window['couponWheel']) !== 'object')
	{
		$.ajax({
			url: ajax,
			method: 'POST',
			data: {
				action: 'CouponWheelAjax::renderPopup',
			},
			context: this,
		}).done(function(html){

			if (embed){
				$('.couponWheel_embed_'+wheel_hash).html(html);
			} else {
				$('#couponWheelRoot').html(html);
			}

			if(window['couponWheel'] !== undefined)
			{
				window['couponWheel'].show_popup(0);
			} else {
				console.log('Coupon Wheel with hash '+wheel_hash+' does not exist or is not LIVE');
			}
		});
	}
	else {
		window['couponWheel'].show_popup(0);
	}
}

window.addEventListener('load',function(){
	if($('#couponWheelRoot').data('show') == true) {
		$.post(ajax, {action: 'CouponWheelAjax::renderPopup'}, function () {
		}, 'html').done(function (response) {
			$('#couponWheelRoot').html(response);
		});
	}
});