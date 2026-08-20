<?php
namespace CouponWheel\Models;

use SkillDo\Database\Eloquent\Model;

class SpinLog extends Model
{
    protected string $table = 'coupon_wheel_logs';

    protected string $primaryKey = 'id';

    protected array $columns = [
        'program_id'  => ['int', 0],
        'slice_index' => ['int', 0],
        'prize_name'  => ['string'],
        'product_id'  => ['int', 0],
        'prize_text'  => ['string'],
        'status'      => ['int', 0],  // 1 = trúng thưởng, 0 = không trúng
        'user_id'     => ['int', 0],
        'email'       => ['string'],
        'phone'       => ['string'],
        'ip'          => ['string'],
        'session_id'  => ['string'],
    ];
}

