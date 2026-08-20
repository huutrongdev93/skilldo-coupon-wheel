<?php

use SkillDo\Cms\Support\Ajax;

// Admin AJAX
Ajax::admin('CouponWheel\Ajax\Admin\ProgramAjax::save', 'post');
Ajax::admin('CouponWheel\Ajax\Admin\ProgramAjax::delete', 'post');
Ajax::admin('CouponWheel\Ajax\Admin\ProgramAjax::clone', 'post');
Ajax::admin('CouponWheel\Ajax\Admin\ProgramAjax::productSearch', 'post');
Ajax::admin('CouponWheel\Ajax\Admin\ProgramAjax::toggleStatus', 'post');

// Web AJAX (public)
Ajax::client('CouponWheel\Ajax\Web\SpinAjax::spin', 'post');