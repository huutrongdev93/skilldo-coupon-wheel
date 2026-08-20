<?php
namespace CouponWheel\Ajax\Web;

use CouponWheel\Models\SpinLog;
use CouponWheel\Models\WheelProgram;
use CouponWheel\Notifications\SpinNotification;
use CouponWheel\Supports\DifficultyWeight;
use SkillDo\Http\Request;
use SkillDo\Support\Auth;

class SpinAjax
{
    public static function spin(Request $request): void
    {
        // 1. Honeypot anti-bot
        if ($request->has('antibot') && $request->input('antibot') != '')
        {
            response()->error(trans('coupon-wheel::web.spin.error.antibot'));
        }

        // 2. Lấy chương trình đang chạy
        $now     = date('Y-m-d H:i:s');

        $program = WheelProgram::where('status', 1)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->first();

        if (empty($program))
        {
            response()->error(trans('coupon-wheel::web.spin.error.no_program'));
        }

        $settings = $program->settings ?: [];

        // 3. Kiểm tra require_login
        $requireLogin = !empty($settings['require_login']);

        if ($requireLogin && !Auth::check())
        {
            response()->error(trans('coupon-wheel::web.spin.error.require_login'));
        }

        $ip        = $request->ip();

        $sessionId = session()->getId();

        $email     = trim($request->input('email', ''));

        $phone     = trim($request->input('phone', ''));

        $userId    = Auth::check() ? Auth::id() : 0;

        $spinLimit = $settings['spin_limit'] ?? 'forever';

        // 4. Rate-limit — chỉ kiểm tra khi spin_limit KHÔNG phải 'none'
        if ($spinLimit !== 'none')
        {
            $logQuery = SpinLog::where('program_id', $program->id);

            if ($spinLimit === 'daily')
            {
                $logQuery->whereDate('created', date('Y-m-d'));
            }

            // Kiểm tra theo IP + session
            $existsBySession = (clone $logQuery)
                ->where(function ($q) use ($ip, $sessionId) {
                    $q->where('ip', $ip)->orWhere('session_id', $sessionId);
                })->count() > 0;

            if ($existsBySession)
            {
                response()->error(trans('coupon-wheel::web.spin.error.already_spun'));
            }
        }

        // Kiểm tra trùng lặp theo contact (email / phone / both)
        $contactLimit = $settings['contact_limit'] ?? 'none';

        if ($contactLimit !== 'none')
        {
            $logQuery2 = SpinLog::where('program_id', $program->id);

            if (($contactLimit === 'email' || $contactLimit === 'both') && !empty($email))
            {
                $existsByEmail = (clone $logQuery2)->where('email', $email)->count() > 0;

                if ($existsByEmail)
                {
                    response()->error(trans('coupon-wheel::web.spin.error.email_used'));
                }
            }

            if (($contactLimit === 'phone' || $contactLimit === 'both') && !empty($phone))
            {
                $existsByPhone = (clone $logQuery2)->where('phone', $phone)->count() > 0;

                if ($existsByPhone)
                {
                    response()->error(trans('coupon-wheel::web.spin.error.phone_used'));
                }
            }
        }

        // 5. Weighted random — có tính số lượng đã trúng
        $slices = $program->slices ?: [];

        if (empty($slices))
        {
            response()->error(trans('coupon-wheel::web.spin.error.no_slices'));
        }

        // Đếm số lần đã trúng cho từng slice_index (chỉ các slice có quantity > 0)
        $wonCounts  = [];
        $hasLimited = false;

        foreach ($slices as $slice) {
            if ((int)($slice['quantity'] ?? 0) > 0) {
                $hasLimited = true;
                break;
            }
        }

        if ($hasLimited) {
            $rows = SpinLog::where('program_id', $program->id)
                ->where('status', 1)
                ->select('slice_index')
                ->get();
            foreach ($rows as $row) {
                $wonCounts[(int)$row->slice_index] = ($wonCounts[(int)$row->slice_index] ?? 0) + 1;
            }
        }

        // Kiểm tra toàn bộ phần thưởng đã hết chưa — báo trước khi cho quay
        if (DifficultyWeight::isExhausted($slices, $wonCounts))
        {
            response()->error(trans('coupon-wheel::web.spin.error.exhausted'));
        }

        $winIndex = DifficultyWeight::roll($slices, $wonCounts);

        if ($winIndex < 0)
        {
            response()->error(trans('coupon-wheel::web.spin.error.no_prize'));
        }

        $winSlice = $slices[$winIndex];

        // 6. Resolve prize
        $prizeType   = $winSlice['prize_type'] ?? 'text';
        $prizeName   = $winSlice['name'] ?? 'Giải thưởng';
        $prizeText   = $winSlice['prize_text'] ?? '';
        $productId   = (int) ($winSlice['product_id'] ?? 0);
        $prizeUrl    = '';
        $productName = '';

        if ($prizeType === 'product' && $productId > 0 && class_exists('Ecommerce\Models\Product'))
        {
            $product = \Ecommerce\Models\Product::find($productId);

            if (!empty($product))
            {
                $productName = $product->title;
                $prizeUrl    = method_exists($product, 'url') ? $product->url() : '';
            }
        }

        // Status: trúng khi là product (có productId) HOẶC text có nội dung
        $won = ($prizeType === 'product' && $productId > 0)
            || ($prizeType === 'text' && $prizeText !== '');

        // Tính stop_angle
        $total    = count($slices);
        $sliceDeg = 360 / $total;
        $midAngle = $sliceDeg * $winIndex + $sliceDeg / 2;
        $stopAngle = 360 * 5 + (360 - $midAngle);

        // 7. Lưu log
        SpinLog::insert([
            'program_id'  => $program->id,
            'slice_index' => $winIndex,
            'prize_name'  => $prizeName,
            'product_id'  => $productId,
            'prize_text'  => $prizeText,
            'status'      => $won ? 1 : 0,
            'user_id'     => $userId,
            'email'       => $email,
            'phone'       => $phone,
            'ip'          => $ip,
            'session_id'  => $sessionId,
            'created'     => date('Y-m-d H:i:s'),
        ]);

        // 8. Gửi thông báo Telegram nếu plugin Telegram được kích hoạt
        SpinNotification::send([
            'won'          => $won,
            'prize_name'   => $prizeName,
            'prize_type'   => $prizeType,
            'prize_text'   => $prizeText,
            'product_name' => $productName,
            'prize_url'    => $prizeUrl,
            'email'        => $email,
            'phone'        => $phone,
            'user_id'      => $userId,
            'ip'           => $ip,
            'program_name' => $program->name ?? '',
            'stop_angle'   => $stopAngle,
        ]);

        response()->success($won ? trans('coupon-wheel::web.spin.win.message') : trans('coupon-wheel::web.spin.lose.message'), [
            'stop_angle'   => $stopAngle,
            'slice_index'  => $winIndex,
            'prize_name'   => $prizeName,
            'prize_type'   => $prizeType,
            'prize_text'   => $prizeText,
            'product_name' => $productName,
            'prize_url'    => $prizeUrl,
            'won'          => $won,
        ]);
    }
}

