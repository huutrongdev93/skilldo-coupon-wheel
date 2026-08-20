<?php
namespace CouponWheel\Models;

use SkillDo\Database\Eloquent\Model;

class WheelProgram extends Model
{
    protected string $table = 'coupon_wheel_programs';

    protected string $primaryKey = 'id';

    protected array $columns = [
        'name'          => ['string'],
        'start_at'      => ['string'],
        'end_at'        => ['string'],
        'slices'        => ['array', []],
        'settings'      => ['array', []],
        'status'        => ['int', 0],
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::retrieved(function (WheelProgram $program)
        {
            if (!empty($program->slices) && is_string($program->slices))
            {
                $decoded = unserialize($program->slices);

                $program->slices = is_array($decoded) ? $decoded : [];
            }

            if (!empty($program->settings) && is_string($program->settings))
            {
                $decoded = unserialize($program->settings);

                $program->settings = is_array($decoded) ? $decoded : [];
            }
        });

        static::saving(function (WheelProgram $program)
        {
            if (is_array($program->slices))
            {
                $program->slices = serialize($program->slices);
            }

            if (is_array($program->settings))
            {
                $program->settings = serialize($program->settings);
            }
        });
    }
}

