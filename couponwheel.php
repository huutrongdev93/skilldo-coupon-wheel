<?php
/**
Plugin name     : Vòng xoay may mắn
Plugin class    : CouponWheel
Plugin uri      : http://sikido.vn
Description     : Tạo một vòng quay may mắn kích thích nhu cầu mua sắm của khách hàng.
Author          : SKDSoftware Dev Team
Version         : 2.0.3
*/
const CP_WHEEL_NAME = 'couponwheel';

class CouponWheel {

    private string $name = 'CouponWheel';

    function __construct() {
        add_action('init', [$this, 'assets']);
        add_action('cle_footer', [$this, 'render']);
    }
    public function active(): void
    {
        CouponWheelMigration::created();
        CouponWheelRoles::active();
    }
    public function uninstall(): void
    {
        CouponWheelMigration::drop();
    }
    public function assets(): void
    {
        if(!Device::isGoogleSpeed()) {
            $path = Path::plugin(CP_WHEEL_NAME);
            Template::asset()->location('header')->add('couponWheel', $path . '/assets/css/style.client.css', ['minify' => false, 'path' => ['all' => $path . '/assets']]);
            Template::asset()->location('footer')->add('couponWheel', $path . '/assets/script/coupon-wheel.js', ['minify' => false, 'path' => ['all' => $path . '/assets']]);
            Template::asset()->location('footer')->add('couponWheel', $path . '/assets/script/dialog_trigger.js', ['minify' => false, 'path' => ['all' => $path . '/assets']]);
        }
    }
    public function render(): void
    {
        echo '<div id="couponWheelRoot" data-show="'. !Device::isGoogleSpeed() .'"></div>';
    }
}

include_once 'includes/migration.php';
include_once 'includes/roles.php';
include_once 'includes/function.php';
include_once 'ajax.php';

if(Admin::is()) {
    include_once 'admin/admin.php';
}

new CouponWheel();