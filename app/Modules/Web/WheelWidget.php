<?php
namespace CouponWheel\Modules\Web;

use CouponWheel\Models\WheelProgram;
use CouponWheel\Supports\WheelDisplay;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Template\Template;

class WheelWidget
{
    public static function render(): void
    {
        $now = date('Y-m-d H:i:s');

        $program = WheelProgram::where('status', 1)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->first();

        if (empty($program)) return;

        $slices   = $program->slices ?: [];

        $settings = $program->settings ?: [];

        if (empty($slices)) return;

        $display = WheelDisplay::normalize($settings['display'] ?? []);

        // Merge defaults từ trans() để đảm bảo luôn có giá trị
        $displayText = array_merge([
            'popupHeading'     => trans('coupon-wheel::admin.text.default.popupHeading'),
            'popupDescription' => trans('coupon-wheel::admin.text.default.popupDescription'),
            'winHeading'       => trans('coupon-wheel::admin.text.default.winHeading'),
            'winDescription'   => trans('coupon-wheel::admin.text.default.winDescription'),
            'loseHeading'      => trans('coupon-wheel::admin.text.default.loseHeading'),
            'loseDescription'  => trans('coupon-wheel::admin.text.default.loseDescription'),
        ], array_filter($settings['displayText'] ?? [], fn($v) => $v !== ''));

        // Áp dụng ngôn ngữ hiện tại nếu multi-language
        if (Language::isMulti()) {
            $currentLang = Language::current();
            $defaultLang = Language::default();

            if ($currentLang !== $defaultLang) {
                $baseKeys = ['popupHeading', 'popupDescription', 'winHeading', 'winDescription', 'loseHeading', 'loseDescription'];

                foreach ($baseKeys as $key) {
                    $langKey = $key . '_' . $currentLang;
                    if (!empty($displayText[$langKey])) {
                        $displayText[$key] = $displayText[$langKey];
                    }
                }
            }
        }

        $triggerTypes = $settings['trigger_type'] ?? ['auto'];
        if (!is_array($triggerTypes)) {
            // tương thích ngược với dữ liệu cũ dạng string
            $triggerTypes = [$triggerTypes];
        }
        $showAutoTrigger   = in_array('auto',   $triggerTypes);
        $showButtonTrigger = in_array('button', $triggerTypes);

        $triggerDelay = (int)($settings['trigger_delay'] ?? 3);

        $requireLogin = !empty($settings['require_login']);

        // Cài đặt hiển thị form
        $showEmail    = (int)($settings['show_email'] ?? 1) === 1;
        $showPhone    = (int)($settings['show_phone'] ?? 1) === 1;
        $contactLimit = $settings['contact_limit'] ?? 'none';

        $bgStyle         = WheelDisplay::backgroundCss($display['background'] ?? 'style1');

        $frameUrl        = WheelDisplay::frameCenter($display['frame'] ?? 'style1');

        $centerUrl       = WheelDisplay::imgCenter($display['center'] ?? 'style1');

        $triggerUrl      = WheelDisplay::triggerIconUrl($display);

        $triggerBg       = $display['triggerBg'] ?? '#ffe2e2';

        $triggerEffect   = $display['triggerEffect'] ?? '';

        $triggerPosition = $display['triggerPosition'] ?? 'bottom-right';

        // Hint text động theo contact_limit
        $hintKey = match($contactLimit) {
            'email' => 'form.hint.email_required',
            'phone' => 'form.hint.phone_required',
            'both'  => 'form.hint.both_required',
            default => 'form.hint',
        };

        $headingStyle = Template::cssText($display['headingStyle'] ?? '')['css'] ?? '';

        echo view('coupon-wheel::web/wheel', compact(
            'program',
            'slices',
            'settings',
            'display',
            'displayText',
            'showAutoTrigger',
            'showButtonTrigger',
            'triggerDelay',
            'requireLogin',
            'showEmail',
            'showPhone',
            'contactLimit',
            'bgStyle',
            'frameUrl',
            'centerUrl',
            'triggerUrl',
            'triggerBg',
            'triggerEffect',
            'triggerPosition',
            'hintKey',
            'headingStyle'
        ));
    }
}
