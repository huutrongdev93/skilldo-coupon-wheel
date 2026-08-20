<?php
namespace CouponWheel\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class ActivatorService
{
    public static function activate(): void
    {
        if (!schema()->hasTable('coupon_wheel_programs')) {
            schema()->create('coupon_wheel_programs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->dateTime('start_at')->nullable();
                $table->dateTime('end_at')->nullable();
                $table->longText('slices')->nullable();
                $table->text('settings')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
            });
        }

        if (!schema()->hasTable('coupon_wheel_logs')) {
            schema()->create('coupon_wheel_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('program_id')->default(0);
                $table->integer('slice_index')->default(0);
                $table->string('prize_name', 255)->nullable();
                $table->integer('product_id')->default(0);
                $table->string('prize_text', 255)->nullable();
                $table->tinyInteger('status')->default(0)->comment('1=won, 0=lost');
                $table->integer('user_id')->default(0);
                $table->string('email', 255)->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('ip', 45)->nullable();
                $table->string('session_id', 100)->nullable();
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
            });
        }
        else if (!schema()->hasColumn('coupon_wheel_logs', 'status')) {
            schema()->table('coupon_wheel_logs', function (Blueprint $table) {
                $table->tinyInteger('status')->default(0)->comment('1=won, 0=lost')->after('prize_text');
            });
        }
    }
}

