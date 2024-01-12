<?php
Class AdminCouponWheelRoles {
    static function group($group) {
        $group['CouponWheel'] = [
            'label' => __('Vòng quay mai mắn'),
            'capabilities' => array_keys(AdminCouponWheelRoles::capabilities())
        ];
        return $group;
    }
    static function label($label): array
    {
        return array_merge($label, AdminCouponWheelRoles::capabilities() );
    }
    static function capabilities(): array
    {
        $label['couponWheelList']     = 'Xem chiến dịch vòng quay';
        $label['couponWheelAdd']      = 'Thêm chiến dịch vòng quay';
        $label['couponWheelEdit']     = 'Cập nhật chiến dịch vòng quay';
        $label['couponWheelDelete']   = 'Xóa chiến dịch vòng quay';
        $label['couponWheelLogList']   = 'Xem thống kê lượt vòng quay';
        $label['couponWheelLogDelete']   = 'Xóa thống kê lượt vòng quay';
        return $label;
    }
}

add_filter('user_role_editor_group', 'AdminCouponWheelRoles::group' );
add_filter('user_role_editor_label', 'AdminCouponWheelRoles::label' );