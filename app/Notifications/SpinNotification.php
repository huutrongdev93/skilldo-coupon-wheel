<?php
namespace CouponWheel\Notifications;

use SkillDo\Log\Log;

/**
 * Gửi thông báo Telegram khi có lượt quay thưởng mới.
 * Chỉ hoạt động khi plugin Telegram được kích hoạt.
 */
class SpinNotification
{
    /**
     * Gửi kết quả quay thưởng qua Telegram cho admin.
     *
     * @param array $data Dữ liệu kết quả quay
     */
    public static function send(array $data): void
    {
        // Kiểm tra plugin Telegram có được kích hoạt không
        if (!class_exists(\Telegram\Services\TelegramNotification::class))
        {
            return;
        }

        $won         = !empty($data['won']);
        $prizeName   = $data['prize_name']   ?? '';
        $prizeType   = $data['prize_type']   ?? 'text';
        $prizeText   = $data['prize_text']   ?? '';
        $productName = $data['product_name'] ?? '';
        $prizeUrl    = $data['prize_url']    ?? '';
        $email       = $data['email']        ?? '';
        $phone       = $data['phone']        ?? '';
        $userId      = $data['user_id']      ?? 0;
        $ip          = $data['ip']           ?? '';
        $programName = $data['program_name'] ?? '';
        $stopAngle   = $data['stop_angle']   ?? '';

        $statusIcon = $won ? '🎉' : '😢';
        $statusText = $won ? 'TRÚNG THƯỞNG' : 'KHÔNG TRÚNG';

        $message  = "::: {$statusIcon} KẾT QUẢ VÒNG QUAY MAY MẮN:\n";
        $message .= "Kết quả: {$statusText}\n";

        if (!empty($programName)) {
            $message .= "Chương trình: {$programName}\n";
        }

        $message .= "-------------------\n";
        $message .= "Ô thưởng: {$prizeName}\n";

        if ($prizeType === 'product' && !empty($productName)) {
            $message .= "Sản phẩm: {$productName}\n";
            if (!empty($prizeUrl)) {
                $message .= "Link: {$prizeUrl}\n";
            }
        } elseif ($prizeType === 'text' && !empty($prizeText)) {
            $message .= "Nội dung: {$prizeText}\n";
        }

        $message .= "-------------------\n";

        if (!empty($email)) {
            $message .= "Email: {$email}\n";
        }

        if (!empty($phone)) {
            $message .= "SĐT: {$phone}\n";
        }

        if (!empty($userId)) {
            $message .= "User ID: {$userId}\n";
        }

        if (!empty($ip)) {
            $message .= "IP: {$ip}\n";
        }

        $message .= "Thời gian: " . date('d/m/Y H:i:s') . "\n";
        $message .= "-------------------\n";
        $message .= "Gửi từ website " . request()->getBaseUrl() . "\n";

        try
        {
            \Telegram\Services\TelegramNotification::make()->message($message)->send();
        }
        catch (\Exception $e)
        {
            Log::error('CouponWheel SpinNotification Error: ' . $e->getMessage());
        }
    }
}

