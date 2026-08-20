<?php
namespace CouponWheel\Services;

class DeactivatorService
{
    public static function uninstall(): void
    {
        schema()->dropIfExists('coupon_wheel_programs');
        schema()->dropIfExists('coupon_wheel_logs');
    }
}

