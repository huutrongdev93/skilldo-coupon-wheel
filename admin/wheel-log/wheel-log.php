<?php
include 'table.php';

Class AdminCouponWheelLog {

    static function page(\SkillDo\Http\Request $request): void
    {
        if (Auth::hasCap('couponWheelLogList')) {

            $table = new AdminWheelLogTable([
                'items' => [],
                'table' => 'wheelLog',
                'model' => model('wheels_log'),
                'module'=> 'wheelLog',
            ]);

            Admin::view('components/page-default/page-index', [
                'module'    => 'wheelLog',
                'name'      => trans('Thống kê lượt chơi'),
                'table'     => $table,
                'tableId'     => 'admin_table_wheelLog_list',
                'limitKey'    => 'admin_wheelLog_limit',
                'ajax'        => 'AdminCouponWheelLogAjax::load',
            ]);
        }
        else {
            Admin::alert('error', 'Bạn không có quyền sử dụng chức năng này');
        }
    }

    static function breadcrumb($breadcrumb, $pageIndex, \SkillDo\Http\Request $request): array
    {
        if($pageIndex == 'plugins_page') {

            $page = Url::segment(3);

            if($page == 'coupon-wheel-log') {

                $breadcrumb['couponWheelLog'] = [
                    'active' => true,
                    'url'    => Url::admin('plugins/coupon-wheel-log'),
                    'label'  => 'Kết quả chiến dịch'
                ];
            }
        }

        return $breadcrumb;
    }
}

add_filter('admin_breadcrumb', 'AdminCouponWheelLog::breadcrumb', 50, 3);