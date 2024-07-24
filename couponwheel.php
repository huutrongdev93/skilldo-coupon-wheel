<?php
const CP_WHEEL_NAME = 'couponwheel';

class CouponWheel {

    private string $name = 'CouponWheel';

    function __construct() {
        add_action('theme_custom_assets', [$this, 'assets'], 10, 2);
        add_action('cle_footer', [$this, 'render']);
    }
    public function active(): void
    {
        (include 'database/database.php')->up();
        CouponWheelRoles::active();
    }
    public function uninstall(): void
    {
        (include 'database/database.php')->down();
    }
    public function assets(AssetPosition $header, AssetPosition $footer): void
    {
        if(!Device::isGoogleSpeed()) {
            $path = Path::plugin(CP_WHEEL_NAME);
            $header->add('couponWheel', $path . '/assets/css/style.client.css', ['minify' => false, 'path' => ['all' => $path . '/assets']]);
            $footer->add('couponWheel', $path . '/assets/script/coupon-wheel.js', ['minify' => false, 'path' => ['all' => $path . '/assets']]);
            $footer->add('couponWheel', $path . '/assets/script/dialog_trigger.js', ['minify' => false, 'path' => ['all' => $path . '/assets']]);
        }
    }
    public function render(): void
    {
        if(!Theme::isReviewWidget()) {
            echo '<div id="couponWheelRoot" data-show="'. !Device::isGoogleSpeed() .'"></div>';
        }
    }
}

include_once 'includes/roles.php';
include_once 'includes/function.php';
include_once 'ajax.php';

if(Admin::is()) {
    include_once 'admin/admin.php';
}

new CouponWheel();