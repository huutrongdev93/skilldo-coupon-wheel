<?php
class CouponWheelRoles {
    static function active(): void
    {
        $roles = ['root', 'administrator'];
        foreach ($roles as $roleName) {
            $role = get_role($roleName);
            $role->add_cap('couponWheelList');
            $role->add_cap('couponWheelAdd');
            $role->add_cap('couponWheelEdit');
            $role->add_cap('couponWheelDelete');
            $role->add_cap('couponWheelLogList');
            $role->add_cap('couponWheelLogDelete');
        }
    }
}