<?php
namespace CouponWheel\Services;

use SkillDo\Cms\Menu\AdminMenu;

class AdminService
{
    public static function navigation(): void
    {
        AdminMenu::addSub('marketing', 'coupon-wheel-programs', trans('coupon-wheel::admin.program.page_title'), 'coupon-wheel', []);
        AdminMenu::addSub('marketing', 'coupon-wheel-logs',     trans('coupon-wheel::admin.log.page_title'),     'coupon-wheel/logs', []);
    }

    public static function breadcrumb(): void
    {
        app('breadcrumb.admin')->add('admin.coupon_wheel.index', [
            ['label' => trans('coupon-wheel::admin.title')],
        ]);

        app('breadcrumb.admin')->add('admin.coupon_wheel.add', [
            ['label' => trans('coupon-wheel::admin.title'), 'url' => route('admin.coupon_wheel.index')],
            ['label' => trans('admin::general.add')]
        ]);

        app('breadcrumb.admin')->add('admin.coupon_wheel.edit', [
            ['label' => trans('coupon-wheel::admin.title'), 'url' => route('admin.coupon_wheel.index')],
            ['label' => trans('admin::general.update')]
        ]);
    }

    public static function system($tabs)
    {
        $tabs['coupon-wheel'] = [
            'group'       => 'marketing',
            'label'       => trans('coupon-wheel::admin.title'),
            'description' => 'Quản lý danh sách chương trình vòng quay may mắn',
            'icon'        => '<i class="fa-duotone fa-dharmachakra"></i>',
            'href'        => route('admin.coupon_wheel.index'),
        ];

        return $tabs;
    }
}


