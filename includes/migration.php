<?php
class CouponWheelMigration {
    static function created(): void
    {
        if(!schema()->hasTable('wheels')) {
            schema()->create('wheels', function ($table) {
                $table->increments('id');
                $table->string('name', 255)->collate('utf8mb4_unicode_ci')->nullable();
                $table->char('seen_key', 6)->collate('utf8mb4_unicode_ci');
                $table->tinyInteger('is_live')->default(0);
                $table->tinyInteger('kiosk_mode')->default(0);
                $table->tinyInteger('require_user')->default(1);
                $table->tinyInteger('require_email')->default(1);
                $table->tinyInteger('require_fullname')->default(1);
                $table->tinyInteger('require_phone')->default(0);
                $table->tinyInteger('timed_trigger')->default(1);
                $table->tinyInteger('exit_trigger')->default(1);
                $table->tinyInteger('prevent_triggers_on_mobile')->default(0);
                $table->integer('max_spins_per_user')->default(1);
                $table->integer('reset_counter_days')->default(0);
                $table->tinyInteger('wheel_spin_time')->default(10);
                $table->smallInteger('show_popup_after')->default(20);
                $table->integer('coupon_urgency_timer')->default(30);
                $table->bigInteger('popup_impressions')->default(0); //số lần bật lên của popup
                $table->bigInteger('popup_spin')->default(0); //số lần bật quay của popup

                for($i = 1; $i <= 12; $i++) {
                    $table->text('slice'.$i.'_label')->collate('utf8mb4_unicode_ci')->nullable();
                    $table->string('slice'.$i.'_value', 255)->collate('utf8mb4_unicode_ci')->default('');
                    $table->integer('slice'.$i.'_qty')->default(0);
                    $table->tinyInteger('slice'.$i.'_infinite')->default(0);
                    $table->tinyInteger('slice'.$i.'_percent')->default(12);
                }

                $table->string('status', 50)->collate('utf8mb4_unicode_ci')->default('run');
                $table->dateTime('created');
                $table->dateTime('updated')->nullable();
                $table->integer('order')->default(0);
            });
        }
        if(!schema()->hasTable('wheels_metadata')) {
            schema()->create('wheels_metadata', function ($table) {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->string('meta_key', 255)->nullable();
                $table->text('meta_value')->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created');
                $table->dateTime('updated')->nullable();
                $table->index('object_id');
            });
        }
        if(!schema()->hasTable('wheels_log')) {
            schema()->create('wheels_log', function ($table) {
                $table->increments('id');
                $table->integer('wheel_id')->default(0);
                $table->string('wheel_name', 255)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('wheel_deg_end', 6)->collate('utf8mb4_unicode_ci');
                $table->string('wheel_time_end', 6)->collate('utf8mb4_unicode_ci');
                $table->string('popup_rules_text', 255)->collate('utf8mb4_unicode_ci');
                $table->tinyInteger('slice_number');
                $table->text('slice_label')->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('coupon_code', 255)->collate('utf8mb4_unicode_ci');
                $table->string('email', 50)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('fullname', 50)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('phone', 50)->collate('utf8mb4_unicode_ci')->nullable();
                $table->tinyInteger('rules_checked')->default(0);
                $table->string('ip', 50)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('device_id', 255)->nullable();
                $table->char('user_cookie');
                $table->text('referer');
                $table->integer('timestamp')->default(0);
                $table->tinyInteger('is_read')->default(0);
                $table->dateTime('created');
                $table->dateTime('updated')->nullable();
                $table->integer('order')->default(0);
                $table->integer('user_id')->default(0);
            });
        }
    }

    static function drop(): void
    {
        schema()->drop('wheels');
        schema()->drop('wheels_metadata');
        schema()->drop('wheels_log');
    }
}