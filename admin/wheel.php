<?php
Class AdminCouponWheel {
    static function assets(): void
    {
        $asset = Path::plugin(CP_WHEEL_NAME).'/assets/';
        if(Admin::is()) {
            Admin::asset()->location('header')->add(CP_WHEEL_NAME, $asset.'css/style.admin.css');
            Admin::asset()->location('footer')->add(CP_WHEEL_NAME, $asset.'script/script.admin.js');
        }
    }

    static function navigation(): void
    {
        if(Auth::hasCap('couponWheelList')) {
            AdminMenu::addSub('marketing', 'coupon-wheel', trans('admin.wheel.menu'), 'plugins/coupon-wheel', ['callback' => 'AdminCouponWheel::page']);
        }
        if(Auth::hasCap('couponWheelLogList')) {
            $count = CacheHandler::get('coupon_wheel_log_is_read');
            if(empty($count)) {
                $count = WheelLog::count(Qr::set('is_read', 0));
                CacheHandler::save('coupon_wheel_log_is_read', $count);
            }
            AdminMenu::addSub('marketing', 'coupon-wheel-log', trans('admin.wheel.log.menu'), 'plugins/coupon-wheel-log', [
                'callback' => 'AdminCouponWheelLog::page',
                'count' => $count
            ]);
        }
    }

    static function page(\SkillDo\Http\Request $request, $params = []): void
    {

        $view = (empty($params[0])) ? 'index' : $params[0];

        if($view == 'index' ) {
            if (!Auth::hasCap('couponWheelList')) {
                echo Admin::alert('error', trans('admin.wheel.error.rule'));
                return;
            }
            AdminCouponWheel::pageList();
        }
        if( $view == 'add' ) {
            if (!Auth::hasCap('couponWheelAdd')) {
                echo Admin::alert('error', trans('admin.wheel.error.rule'));
                return;
            }
            AdminCouponWheel::pageAdd();
        }
        if( $view == 'edit' ) {
            if (!Auth::hasCap('couponWheelEdit')) {
                echo Admin::alert('error', trans('admin.wheel.error.rule'));
                return;
            }
            AdminCouponWheel::pageEdit($request, $params);
        }
    }

    static function pageList(): void
    {
        Plugin::view(CP_WHEEL_NAME, 'admin/views/coupon-wheel/index');
    }
    static function pageAdd(): void
    {
        Plugin::view(CP_WHEEL_NAME, 'admin/views/coupon-wheel/add', self::formData());
    }
    static function pageEdit(\SkillDo\Http\Request $request, $params): void
    {
        $id     = (int)($params[1] ?? 0);

        $wheel  = Wheel::get($id);

        if(have_posts($wheel)) {

            Plugin::view(CP_WHEEL_NAME, 'admin/views/coupon-wheel/edit', [
                ...self::formData($wheel),
                'wheel' => $wheel
            ]);
        }
        else {
            echo Admin::alert('error', trans('admin.wheel.error.edit.empty'), [
                'heading' => trans('admin.wheel.error.edit.empty.heading')
            ]);
        }
    }
    static function formData($object = null): array
    {
        $wheel = [
            'name'                  => '',
            'is_live'               => 0,
            'max_spins_per_user'    => 1,
            'reset_counter_days'    => 1,
            'show_popup_after'      => 5,
            'wheel_spin_time'       => 8,
            'require_email'         => 1,
            'require_user'         => 0,
        ];

        $wheelDisplay = WheelHelper::displayTemplate((have_posts($object)) ? $object->id : null);

        $wheelText = WheelHelper::displayText((have_posts($object)) ? $object->id : null);

        $wheelAward = [
            1 => [
                'label'     => 'MÃ GIẢM GIÁ 500K',
                'value'     => 'VCXNE48932NC',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 500.000đ, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 6,
                'infinite'  => 0
            ],
            2 => [
                'label'     => 'FREESHIP',
                'value'     => 'FREESHIP',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá miễn phí vận chuyển, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 12,
                'infinite'  => 0
            ],
            3 => [
                'label'     => 'May mắn lần sau',
                'value'     => '',
                'desc'      => 'Có vẻ hôm nay bạn không quá may mắn, hãy thử lại vào lần sau nhé. Xin cám ơn!',
                'qty'       => 0,
                'percent'   => 100,
                'infinite'  => 1
            ],
            4 => [
                'label'     => 'MÃ GIẢM GIÁ 50k',
                'value'     => 'NMVCXM4VNX',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 50.000đ, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 12,
                'infinite'  => 0
            ],
            5 => [
                'label'     => 'MÃ GIẢM GIÁ 100k',
                'value'     => 'MNCVMEI78',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 100.000đ, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 6,
                'infinite'  => 0
            ],
            6 => [
                'label'     => 'May mắn lần sau',
                'value'     => '',
                'desc'      => 'Có vẻ hôm nay bạn không quá may mắn, hãy thử lại vào lần sau nhé. Xin cám ơn!',
                'qty'       => 0,
                'percent'   => 100,
                'infinite'  => 1
            ],
            7 => [
                'label'     => 'FREESHIP',
                'value'     => 'FREESHIP',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá miễn phí vận chuyển, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 12,
                'infinite'  => 0
            ],
            8 => [
                'label'     => 'MÃ GIẢM GIÁ 50k',
                'value'     => 'NMVCXM4VNX',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 50.000đ, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 12,
                'infinite'  => 0
            ],
            9 => [
                'label'     => 'MÃ GIẢM GIÁ 5%',
                'value'     => 'MNVXOERWE',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 5% trên giá trị đơn hàng, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 6,
                'infinite'  => 0
            ],
            10 => [
                'label'     => 'MÃ GIẢM GIÁ 20%',
                'value'     => 'CVMNNCMVVN',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 20% trên giá trị đơn hàng, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 10,
                'percent'   => 6,
                'infinite'  => 0
            ],
            11 => [
                'label'     => 'MÃ GIẢM GIÁ 100%',
                'value'     => 'MMSDFKDV',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 100% trên giá trị đơn hàng, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 1,
                'percent'   => 1,
                'infinite'  => 0
            ],
            12 => [
                'label'     => 'MÃ GIẢM GIÁ 50k',
                'value'     => 'NMVCXM4VNX',
                'desc'      => 'Chúc mừng bạn đã trúng mã giảm giá 50.000đ, hãy sử dụng ngay. Xin cám ơn!',
                'qty'       => 1,
                'percent'   => 12,
                'infinite'  => 0
            ],
        ];

        if(have_posts($object)) {

            foreach ($wheel as $key => $value) {
                if(isset($object->{$key})) {
                    $wheel[$key] = $object->{$key};
                }
            }

            foreach ($wheelAward as $key => $value) {
                if(isset($object->{'slice'.$key.'_label'})) {
                    $wheelAward[$key]['label'] = $object->{'slice'.$key.'_label'};
                }
                if(isset($object->{'slice'.$key.'_value'})) {
                    $wheelAward[$key]['value'] = $object->{'slice'.$key.'_value'};
                }
                if(isset($object->{'slice'.$key.'_qty'})) {
                    $wheelAward[$key]['qty'] = $object->{'slice'.$key.'_qty'};
                }
                if(isset($object->{'slice'.$key.'_infinite'})) {
                    $wheelAward[$key]['infinite'] = $object->{'slice'.$key.'_infinite'};
                }
                if(isset($object->{'slice'.$key.'_percent'})) {
                    $wheelAward[$key]['percent'] = $object->{'slice'.$key.'_percent'};
                }
            }
        }

        //Cấu hình chung
        $formBase = form();

        $formBase->add('name', 'text', ['label' => trans('admin.wheel.base.name')], $wheel['name']);

        $formBase->add('is_live', 'radio', ['label' => trans('admin.wheel.base.isLive'), 'options' => [
            0 => trans('admin.wheel.base.isLive.op0'),
            1 => trans('admin.wheel.base.isLive.op1'),
            2 => trans('admin.wheel.base.isLive.op2'),
        ], 'single' => true], $wheel['is_live']);

        $formBase->add('require_user', 'radio', ['label' => trans('admin.wheel.base.require_user'), 'options' => [
            0 => trans('admin.wheel.base.require_user.op0'),
            1 => trans('admin.wheel.base.require_user.op1'),
        ], 'single' => true], $wheel['require_user']);

        $formBase->add('max_spins_per_user', 'text', [
            'label' => trans('admin.wheel.base.maxSpins'),
            'note' => trans('admin.wheel.base.maxSpins.note')
        ], $wheel['max_spins_per_user']);
        $formBase->add('reset_counter_days', 'number', [
            'label' => trans('admin.wheel.base.resetDay'),
            'note' => trans('admin.wheel.base.resetDay.note')
        ], $wheel['reset_counter_days']);
        $formBase->add('show_popup_after', 'number', [
            'label' => trans('admin.wheel.base.autoShow'),
            'note' => trans('admin.wheel.base.autoShow.note')
        ], $wheel['show_popup_after']);
        $formBase->add('wheel_spin_time', 'number', [
            'label' => trans('admin.wheel.base.timeSpin'),
        ], $wheel['wheel_spin_time']);

        //Phần thưởng
        $formAward = [];
        for($i = 1; $i <= 12; $i++) {
            $formAward[$i] = form();
            $formAward[$i]->add('award['.$i.'][label]', 'text', ['label' => trans('admin.wheel.award.label'), 'start' => 6], $wheelAward[$i]['label']);
            $formAward[$i]->add('award['.$i.'][value]', 'text', ['label' => trans('admin.wheel.award.value'), 'start' => 6], $wheelAward[$i]['value']);
            $formAward[$i]->add('award['.$i.'][infinite]', 'checkbox', ['label' => trans('admin.wheel.award.infinite')],
                ($wheelAward[$i]['infinite'] == 1) ? 'award_'.$i.'_infinite' : 0
            );
            $formAward[$i]->add('award['.$i.'][qty]', 'number', ['label' => trans('admin.wheel.award.qty'), 'start' => 6], $wheelAward[$i]['qty']);
            $formAward[$i]->add('award['.$i.'][percent]', 'select', ['label' => trans('admin.wheel.award.percent'), 'start' => 6, 'options' => [
                '100'   => trans('admin.wheel.award.percent.100'),
                '25'    => trans('admin.wheel.award.percent.25'),
                '12'    => trans('admin.wheel.award.percent.12'),
                '6'     => trans('admin.wheel.award.percent.6'),
                '1'     => trans('admin.wheel.award.percent.1'),
            ]], $wheelAward[$i]['percent']);
        }

        //Văn bản
        $formText['first'] = form();

        $formText['alertSuccess'] = form();

        $formText['alertFailed'] = form();

        $formText['alert'] = form();

        $formText['form'] = form();

        $formText['form']->add('require_email', 'switch', ['label' => 'Sử dụng ô nhập email'], $wheel['require_email']);

        $languages = Language::list();

        $hasMulti = Language::isMulti();

        foreach ($languages as $langKey => $language) {

            $languageLabel = ($hasMulti ? ' ('.$language['label'].')' : '');
            
            $formText['first']->add('displayText['.$langKey.'][popup_heading_text]', 'text', [
                'label' => trans('admin.wheel.text.popup_heading_text').$languageLabel
            ], (!empty($wheelText[$langKey]['popup_heading_text'])) ? $wheelText[$langKey]['popup_heading_text'] : '');

            $formText['first']->add('displayText['.$langKey.'][popup_rules_text]', 'textarea', [
                'label' => trans('admin.wheel.text.popup_rules_text').$languageLabel
            ], (!empty($wheelText[$langKey]['popup_rules_text'])) ? $wheelText[$langKey]['popup_rules_text'] : '');

            $formText['first']->add('displayText['.$langKey.'][popup_main_text]', 'textarea', [
                'label' => trans('admin.wheel.text.describe').$languageLabel
            ], (!empty($wheelText[$langKey]['popup_main_text'])) ? $wheelText[$langKey]['popup_main_text'] : '');

            $formText['alertSuccess']->add('displayText['.$langKey.'][popup_win_heading_text]', 'text', [
                'label' => trans('admin.wheel.text.title').$languageLabel,
                'note' => trans('admin.wheel.text.win.note')
            ], (!empty($wheelText[$langKey]['popup_win_heading_text'])) ? $wheelText[$langKey]['popup_win_heading_text'] : '');

            $formText['alertSuccess']->add('displayText['.$langKey.'][popup_win_main_text]', 'textarea', [
                'label' => trans('admin.wheel.text.describe').$languageLabel,
                'note' => trans('admin.wheel.text.win.note')
            ], (!empty($wheelText[$langKey]['popup_win_main_text'])) ? $wheelText[$langKey]['popup_win_main_text'] : '');

            $formText['alertFailed']->add('displayText['.$langKey.'][popup_lose_heading_text]', 'text', [
                'label' => trans('admin.wheel.text.title').$languageLabel,
                'note' => trans('admin.wheel.text.lose.note')
            ], (!empty($wheelText[$langKey]['popup_lose_heading_text'])) ? $wheelText[$langKey]['popup_lose_heading_text'] : '');

            $formText['alertFailed']->add('displayText['.$langKey.'][popup_lose_main_text]', 'textarea', [
                'label' => trans('admin.wheel.text.describe').$languageLabel,
                'note' => trans('admin.wheel.text.lose.note')
            ], (!empty($wheelText[$langKey]['popup_lose_main_text'])) ? $wheelText[$langKey]['popup_lose_main_text'] : '');


            $formText['alert']->add('displayText['.$langKey.'][lang_input_missing]', 'text', [
                'label' => trans('admin.wheel.text.missing').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_input_missing'])) ? $wheelText[$langKey]['lang_input_missing'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_no_spins]', 'text', [
                'label' => trans('admin.wheel.text.noSpins').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_no_spins'])) ? $wheelText[$langKey]['lang_no_spins'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_ace_email_check]', 'text', [
                'label' => trans('admin.wheel.text.emailCheck').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_ace_email_check'])) ? $wheelText[$langKey]['lang_ace_email_check'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_ace_limit_reached]', 'text', [
                'label' => trans('admin.wheel.text.limit_reached').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_ace_limit_reached'])) ? $wheelText[$langKey]['lang_ace_limit_reached'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_close]', 'text', [
                'label' => trans('admin.wheel.text.close').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_close'])) ? $wheelText[$langKey]['lang_close'] : '');


            $formText['form']->add('displayText['.$langKey.'][lang_enter_your_full_name]', 'text', [
                'label' => trans('admin.wheel.text.fullname').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_enter_your_full_name'])) ? $wheelText[$langKey]['lang_enter_your_full_name'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_enter_your_email]', 'text', [
                'label' => trans('admin.wheel.text.email').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_enter_your_email'])) ? $wheelText[$langKey]['lang_enter_your_email'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_enter_phone_number]', 'text', [
                'label' => trans('admin.wheel.text.phone').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_enter_phone_number'])) ? $wheelText[$langKey]['lang_enter_phone_number'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_spin_button]', 'text', [
                'label' => trans('admin.wheel.text.button.spin').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_spin_button'])) ? $wheelText[$langKey]['lang_spin_button'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_continue_button]', 'text', [
                'label' => trans('admin.wheel.text.button.close').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_continue_button'])) ? $wheelText[$langKey]['lang_continue_button'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_spin_again]', 'text', [
                'label' => trans('admin.wheel.text.button.again').$languageLabel
            ], (!empty($wheelText[$langKey]['lang_spin_again'])) ? $wheelText[$langKey]['lang_spin_again'] : '');
        }

        //trigger
        $formTrigger = form();
        $formTrigger->add('triggerOpen', 'switch', [
            'label' => trans('admin.wheel.trigger.open'),
            'start' => 12
        ], $wheelDisplay['triggerOpen']);
        $formTrigger->add('triggerIcon', 'image', [
            'label' => trans('admin.wheel.trigger.icon'),
            'note' => trans('admin.wheel.trigger.icon.note'),
            'start' => 4
        ], $wheelDisplay['triggerIcon']);
        $formTrigger->add('triggerEffect', 'select', [
            'label' => trans('admin.wheel.trigger.effect'),
            'options' => [
                ''                      => trans('admin.wheel.trigger.effect.none'),
                'trigger-animate-swing' => trans('admin.wheel.trigger.effect').' 1',
                'trigger-animate-zoomin' => trans('admin.wheel.trigger.effect').' 2',
                'trigger-animate-wobble' => trans('admin.wheel.trigger.effect').' 3',
                'trigger-animate-bounce' => trans('admin.wheel.trigger.effect').' 4',
            ],
            'single' => true,
            'start' => 4
        ], $wheelDisplay['triggerEffect']);
        $formTrigger->add('triggerBg', 'color', [
            'label' => trans('admin.wheel.trigger.bg'),
            'start' => 4
        ], $wheelDisplay['triggerBg']);

        //Mau
        $formDisplay = form();
        $formDisplay->color('textColor', [
            'label' => trans('admin.wheel.display.textColor'),
            'start' => 3
        ], $wheelDisplay['textColor']);
        $formDisplay->color('btnRunBgColor', [
            'label' => trans('admin.wheel.display.btnRunBgColor'),
            'start' => 3
        ], $wheelDisplay['btnRunBgColor']);
        $formDisplay->color('btnRunTxtColor', [
            'label' => trans('admin.wheel.display.btnRunTxtColor'),
            'start' => 3
        ], $wheelDisplay['btnRunTxtColor']);

        return [
            'formBase' => $formBase,
            'formText' => $formText,
            'formAward' => $formAward,
            'wheelText' => $wheelText,
            'wheelDisplay' => $wheelDisplay,
            'formTrigger' => $formTrigger,
            'formDisplay' => $formDisplay,
        ];
    }

    static function breadcrumb($breadcrumb, $pageIndex, \SkillDo\Http\Request $request): array
    {
        if($pageIndex == 'plugins_page') {

            $page = Url::segment(3);

            if($page == 'coupon-wheel') {

                $view = Url::segment(4);

                $view = (empty($view)) ? 'index' : $view;

                $breadcrumb['couponWheel'] = [
                    'active' => true,
                    'url'    => Url::admin('plugins/coupon-wheel'),
                    'label'  => trans('admin.wheel.breadcrumb.index')
                ];

                if( $view == 'add' ) {
                    $breadcrumb['couponWheel']['active'] = false;
                    $breadcrumb['couponWheel_add'] = [
                        'active' => true,
                        'label' => trans('admin.wheel.breadcrumb.add')
                    ];
                }
                if( $view == 'edit' ) {
                    $breadcrumb['couponWheel']['active'] = false;
                    $breadcrumb['couponWheel_edit'] = [
                        'active' => true,
                        'label' => trans('admin.wheel.breadcrumb.edit')
                    ];
                }
            }
        }

        return $breadcrumb;
    }
}
add_action('admin_init', 'AdminCouponWheel::navigation', 50);
add_action('admin_init', 'AdminCouponWheel::assets', 50);
add_filter('admin_breadcrumb', 'AdminCouponWheel::breadcrumb', 50, 3);