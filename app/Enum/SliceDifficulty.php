<?php
namespace CouponWheel\Enum;

enum SliceDifficulty: string
{
    case VERY_EASY  = 'very_easy';
    case EASY       = 'easy';
    case NORMAL     = 'normal';
    case HARD       = 'hard';
    case VERY_HARD  = 'very_hard';
    case NEVER      = 'never';

    public function weight(): int
    {
        return match($this) {
            self::VERY_EASY => 50,
            self::EASY      => 30,
            self::NORMAL    => 15,
            self::HARD      => 5,
            self::VERY_HARD => 1,
            self::NEVER     => 0,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::VERY_EASY => trans('coupon-wheel::admin.difficulty.very_easy'),
            self::EASY      => trans('coupon-wheel::admin.difficulty.easy'),
            self::NORMAL    => trans('coupon-wheel::admin.difficulty.normal'),
            self::HARD      => trans('coupon-wheel::admin.difficulty.hard'),
            self::VERY_HARD => trans('coupon-wheel::admin.difficulty.very_hard'),
            self::NEVER     => trans('coupon-wheel::admin.difficulty.never'),
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    public static function weights(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->weight();
        }
        return $map;
    }
}

