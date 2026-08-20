<?php
namespace CouponWheel\Services;

use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Template\Assets\AssetPosition;

class AssetsService
{
    public static function admin(): void
    {
        Admin::asset()->location('header')
            ->add('coupon-wheel-admin-css', asset('coupon-wheel::css/admin.css'));

        Admin::asset()->location('footer')
            ->add('coupon-wheel-admin-js', asset('coupon-wheel::js/admin.js'));
    }

    public static function web(AssetPosition $header, AssetPosition $footer): void
    {
        $header->add('coupon-wheel-css', asset('coupon-wheel::css/wheel.css'));
        $footer->add('coupon-wheel-js', asset('coupon-wheel::js/wheel.js'));
    }
}

