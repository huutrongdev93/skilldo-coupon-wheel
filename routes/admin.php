<?php

use SkillDo\Support\Facades\Route;

Route::middleware('auth:admin')->prefix('admin/coupon-wheel')->group(function () {

    Route::match(['get', 'post'], '/', [\CouponWheel\Controllers\Admin\ProgramController::class, 'index'])->name('admin.coupon_wheel.index');
    Route::get('/add',            [\CouponWheel\Controllers\Admin\ProgramController::class, 'add'])->name('admin.coupon_wheel.add');
    Route::get('/edit/{id}',      [\CouponWheel\Controllers\Admin\ProgramController::class, 'edit'])->where('id', '[0-9]+')->name('admin.coupon_wheel.edit');
    Route::get('/logs',           [\CouponWheel\Controllers\Admin\LogController::class,     'index'])->name('admin.coupon_wheel.logs');
});
