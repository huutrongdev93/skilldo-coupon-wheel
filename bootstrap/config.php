<?php

use CouponWheel\Services\AdminService;
use CouponWheel\Services\AssetsService;
use CouponWheel\Modules\Web\WheelWidget;

/*
|--------------------------------------------------------------------------
| Admin Menu
|--------------------------------------------------------------------------
*/
add_action('admin_navigation', [AdminService::class, 'navigation']);

/*
|--------------------------------------------------------------------------
| Admin Breadcrumb
|--------------------------------------------------------------------------
*/
add_action('admin_breadcrumb', [AdminService::class, 'breadcrumb']);

/*
|--------------------------------------------------------------------------
| Admin System Tabs
|--------------------------------------------------------------------------
*/
add_filter('admin_system_tabs', [AdminService::class, 'system'], 10);

/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
*/
add_action('admin_assets', [AssetsService::class, 'admin']);
add_action('theme_custom_assets', [AssetsService::class, 'web'], 10, 2);

/*
|--------------------------------------------------------------------------
| Frontend: inject wheel vào footer
|--------------------------------------------------------------------------
*/
add_action('cle_footer', [WheelWidget::class, 'render']);

