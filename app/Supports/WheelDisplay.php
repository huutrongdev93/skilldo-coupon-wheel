<?php
namespace CouponWheel\Supports;

class WheelDisplay
{
    static function background($key = null): array|string
    {
        $background = [
            'style1' => 'background: linear-gradient(to right, rgb(239, 68, 68), rgb(220, 38, 38));',
            'style2' => 'background: linear-gradient(to right, rgb(245, 158, 11), rgb(217, 119, 6));',
            'style3' => 'background: linear-gradient(to right, rgb(236, 72, 153), rgb(219, 39, 119));',
            'style4' => 'background: linear-gradient(to right, rgb(124, 58, 237), rgb(109, 40, 217));',
            'style5' => 'background: linear-gradient(to right, rgb(255, 65, 108), rgb(255, 75, 43));',
            'style6' => 'background: linear-gradient(to right, rgb(249, 83, 198), rgb(185, 29, 115));',
            'style7' => 'background: linear-gradient(90deg, rgb(255, 15, 123) 0%, rgb(248, 156, 42) 100%);',
            'style8' => 'background: linear-gradient(90deg, rgb(239, 113, 155) 0%, rgb(250, 147, 112) 100%);',
            'style9' => 'background: linear-gradient(90deg, rgb(247, 186, 43) 0%, rgb(234, 83, 88) 100%);',
            'style10' => 'background: linear-gradient(90deg, rgb(238, 184, 109) 0%, rgb(154, 70, 180) 100%);',
            'style11' => 'background: linear-gradient(90deg, rgb(255, 203, 168) 0%, rgb(248, 99, 146) 100%);',
            'style12' => 'background: linear-gradient(90deg, rgb(56, 44, 104) 0%, rgb(181, 124, 238) 100%);',
            'style13' => 'background: linear-gradient(90deg, rgb(242, 145, 237) 0%, rgb(243, 98, 98) 100%);',
            'style14' => 'background: linear-gradient(90deg, rgb(134, 17, 192) 0%, rgb(34, 114, 252) 100%);',
            'style15' => 'background: linear-gradient(90deg, rgb(134, 162, 162) 0%, rgb(171, 140, 153) 100%);',
            'style16' => 'background: linear-gradient(90deg, rgb(41, 82, 112) 0%, rgb(83, 65, 118) 100%);',
            'style17' => 'background: linear-gradient(90deg, rgb(249, 231, 185) 0%, rgb(233, 124, 188) 50%, rgb(63, 74, 217) 100%);',
            'style18' => 'background: linear-gradient(90deg, rgb(185, 75, 152) 0%, rgb(237, 7, 57) 100%);',
            'style19' => 'background: linear-gradient(90deg, rgb(98, 244, 222) 0%, rgb(112, 122, 255) 100%);',
            'style20' => 'background: linear-gradient(90deg, rgb(249, 165, 113) 0%, rgb(188, 87, 112) 100%);',
            'style21' => 'background: url(\''.asset('coupon-wheel::images/bg-1.jpg').'\');',
            'style22' => 'background: url(\''.asset('coupon-wheel::images/bg-2.jpg').'\');',
            'style23' => 'background: url(\''.asset('coupon-wheel::images/bg-3.jpg').'\');',
            'style24' => 'background: url(\''.asset('coupon-wheel::images/bg-4.jpg').'\');',
            'style25' => 'background: url(\''.asset('coupon-wheel::images/bg-5.jpg').'\');',
            'style26' => 'background: url(\''.asset('coupon-wheel::images/bg-6.jpg').'\');',
        ];

        if(!empty($key)) return (isset($background[$key])) ? $background[$key] : [];

        return $background;
    }

    static function imgCenter($key = null): array|string
    {
        $styles = [
            'style1' => asset('coupon-wheel::images/center-1.png'),
            'style2' => asset('coupon-wheel::images/center-2.png'),
            'style3' => asset('coupon-wheel::images/center-3.png'),
            'style4' => asset('coupon-wheel::images/center-4.png'),
            'style5' => asset('coupon-wheel::images/center-5.png'),
        ];

        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];

        return $styles;
    }

    static function frameCenter($key = null): array|string
    {
        $styles = [
            'style1' => asset('coupon-wheel::images/frame-0.png'),
            'style2' => asset('coupon-wheel::images/frame-1.png'),
            'style3' => asset('coupon-wheel::images/frame-2.png'),
            'style4' => asset('coupon-wheel::images/frame-3.png'),
            'style5' => asset('coupon-wheel::images/frame-4.png'),
            'style6' => asset('coupon-wheel::images/frame-5.png'),
            'style7' => asset('coupon-wheel::images/frame-6.png'),
            'style8' => asset('coupon-wheel::images/frame-7.png'),
        ];

        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];

        return $styles;
    }

    static function gift($key = null): array|string
    {
        $styles = [
            'style1' => asset('coupon-wheel::images/trigger-0.png'),
            'style2' => asset('coupon-wheel::images/trigger-1.png'),
            'style3' => asset('coupon-wheel::images/trigger-2.png'),
            'style4' => asset('coupon-wheel::images/trigger-3.png'),
            'style5' => asset('coupon-wheel::images/trigger-4.png'),
            'style6' => asset('coupon-wheel::images/trigger-5.png'),
            'style7' => asset('coupon-wheel::images/trigger-6.png'),
        ];

        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];

        return $styles;
    }

    static function headingStyle(): array
    {
        return [
            "typography" => [
                "fontFamily" => "0",
                "fontSize" => [
                    "desktop" => "30",
                    "tablet" => "",
                    "mobile" => ""
                ],
            ],
            "color" => [
                "active" => "gradient",
                "gradientColor1" => "#ffffff",
                "gradientColor2" => "#fff536",
                "gradientPosition" => "right",
                "gradientPositionStart" => "0",
                "gradientPositionEnd" => "100"
            ],
        ];
    }

    static function defaults(): array
    {
        return [
            'background'      => 'style1',
            'frame'           => 'style1',
            'center'          => 'style1',
            'triggerStyle'    => 'style1',
            'triggerIcon'     => '',
            'triggerEffect'   => '',
            'triggerBg'       => '#ffe2e2',
            'triggerPosition' => 'bottom-right',
            'headingStyle'    => self::headingStyle(),
        ];
    }

    static function normalize(array $display): array
    {
        return array_merge(self::defaults(), $display);
    }

    /**
     * Trả về URL ảnh background từ key (extract từ css string)
     */
    static function backgroundCss(string $key): string
    {
        return self::background($key) ?: self::background('style1');
    }

    /**
     * URL trigger icon: ưu tiên custom upload, rồi triggerStyle
     */
    static function triggerIconUrl(array $display): string
    {
        if (!empty($display['triggerIcon'])) {
            return $display['triggerIcon'];
        }
        $key = $display['triggerStyle'] ?? 'style1';
        return self::gift($key) ?: self::gift('style1');
    }
}
