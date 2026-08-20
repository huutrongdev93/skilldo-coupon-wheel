<?php

class CouponWheel
{
    public function active(): void
    {
        \CouponWheel\Services\ActivatorService::activate();
    }

    public function uninstall(): void
    {
        \CouponWheel\Services\DeactivatorService::uninstall();
    }
}

