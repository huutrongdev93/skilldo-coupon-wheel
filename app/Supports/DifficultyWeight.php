<?php
namespace CouponWheel\Supports;

use CouponWheel\Enum\SliceDifficulty;

class DifficultyWeight
{
    /**
     * Trả về weight của 1 difficulty value
     */
    public static function get(string $difficulty): int
    {
        $case = SliceDifficulty::tryFrom($difficulty);
        return $case ? $case->weight() : 0;
    }

    /**
     * Kiểm tra xem chương trình đã hết toàn bộ phần thưởng chưa.
     *
     * Logic:
     * - Nếu tất cả slice đều có quantity > 0 VÀ tất cả đều đã đạt giới hạn → true (hết giải)
     * - Nếu còn ít nhất 1 slice có quantity = 0 (không giới hạn) và weight > 0 → false (còn giải)
     *
     * @param array $slices     Danh sách slices
     * @param array $wonCounts  [slice_index => số lần đã trúng]
     */
    public static function isExhausted(array $slices, array $wonCounts = []): bool
    {
        foreach ($slices as $index => $slice)
        {
            $weight   = self::get($slice['difficulty'] ?? 'normal');

            if ($weight <= 0) continue; // difficulty = never → bỏ qua

            $quantity = (int) ($slice['quantity'] ?? 0);

            if ($quantity === 0)
            {
                // Còn ít nhất 1 slice không giới hạn số lượng → chưa hết
                return false;
            }

            $won = (int) ($wonCounts[$index] ?? 0);

            if ($won < $quantity)
            {
                // Còn ít nhất 1 slice chưa đạt giới hạn → chưa hết
                return false;
            }
        }

        // Tất cả slice có weight > 0 đều đã hết quota
        return true;
    }

    /**
     * Thực hiện weighted random trên danh sách slices
     * @param array $slices       Danh sách slices từ DB
     * @param array $wonCounts    [slice_index => số lần đã trúng] — để kiểm tra quantity limit
     * Trả về index của slice trúng thưởng, hoặc -1 nếu không có slice khả dụng
     */
    public static function roll(array $slices, array $wonCounts = []): int
    {
        $weights = [];

        foreach ($slices as $index => $slice)
        {
            $difficulty = $slice['difficulty'] ?? 'normal';

            $weight     = self::get($difficulty);

            if ($weight <= 0) continue;

            // Kiểm tra giới hạn số lượng
            $quantity = (int) ($slice['quantity'] ?? 0);

            if ($quantity > 0)
            {
                $won = (int) ($wonCounts[$index] ?? 0);

                if ($won >= $quantity) continue; // đã hết phần thưởng
            }

            $weights[$index] = $weight;
        }

        if (empty($weights))
        {
            return -1;
        }

        $total      = array_sum($weights);

        $rand       = mt_rand(1, $total);

        $cumulative = 0;

        foreach ($weights as $index => $weight)
        {
            $cumulative += $weight;

            if ($rand <= $cumulative)
            {
                return (int) $index;
            }
        }

        return (int) array_key_last($weights);
    }

    /**
     * Tính xác suất ước tính (%) cho mỗi slice — dùng cho preview admin
     * Trả về mảng [index => percent_string]
     */
    public static function estimatePercentages(array $slices): array
    {
        $weights = [];

        foreach ($slices as $index => $slice)
        {
            $difficulty = $slice['difficulty'] ?? 'normal';

            $weights[$index] = self::get($difficulty);
        }

        $total = array_sum($weights);

        $result = [];

        foreach ($weights as $index => $weight)
        {
            if ($total > 0 && $weight > 0)
            {
                $result[$index] = round($weight / $total * 100, 1);
            }
            else
            {
                $result[$index] = 0;
            }
        }

        return $result;
    }
}

