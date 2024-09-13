<?php
class Wheel extends SkillDo\Model\Model {

    protected string $table = 'wheels';

    protected array $columns = [
        'name'                  => ['string'],
        'seen_key'              => ['string'],
        'is_live'               => ['int', 0],
        'max_spins_per_user'    => ['int', 1],
        'reset_counter_days'    => ['int', 0],
        'show_popup_after'      => ['int', 20],
        'wheel_spin_time'       => ['int', 10],
        'require_user'          => ['int', 0],
        'require_email'         => ['int', 0],
        'require_fullname'      => ['int', 1],
        'require_phone'         => ['int', 1],
        'kiosk_mode'            => ['int', 0],
        'timed_trigger'         => ['int', 1],
        'exit_trigger'          => ['int', 1],
        'prevent_triggers_on_mobile'    => ['int', 0],
        'coupon_urgency_timer'  => ['int', 30],
        'popup_impressions'     => ['int', 0],
        'popup_spin'            => ['int', 0],
        'status'                => ['string', 'run'],
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::columnsCreated(function (Wheel $wheel) {
            foreach (range(1,12) as $i) {
                $wheel->setColumn('slice'.$i.'_label', ['string']);
                $wheel->setColumn('slice'.$i.'_value', ['string']);
                $wheel->setColumn('slice'.$i.'_qty', ['int', 0]);
                $wheel->setColumn('slice'.$i.'_infinite', ['int', 0]);
                $wheel->setColumn('slice'.$i.'_percent', ['int', 12]);
            }
        });

        static::saving(function (Wheel $wheel) {
            if (empty($wheel->id)) {
                if(empty($wheel->seen_key)) {
                    $wheel->seen_key = substr(md5(uniqid('',true)).'seen_key',0,6);
                }
            }
        });
    }
}

class WheelLog extends SkillDo\Model\Model {

    protected string $table = 'wheels_log';

    protected array $columns = [
        'wheel_id'          => ['int', 0],
        'wheel_name'        => ['string'],
        'wheel_deg_end'     => ['string'],
        'wheel_time_end'    => ['string'],
        'popup_rules_text'  => ['string'],
        'slice_number'      => ['int', 0],
        'slice_label'       => ['string'],
        'coupon_code'       => ['string'],
        'email'             => ['string'],
        'fullname'          => ['string'],
        'phone'             => ['string'],
        'rules_checked'     => ['int', 1],
        'ip'                => ['string'],
        'device_id'         => ['string'],
        'user_cookie'       => ['string'],
        'referer'           => ['string'],
        'timestamp'         => ['int', 0],
        'is_read'           => ['int', 0],
        'user_id'           => ['int', 0],
    ];
}

class WheelHelper {
    static function status($key = null) {

        $status  = [
            'pending' => [
                'label' => 'Đợi chạy'
            ],
            'run'=> [
                'label' => 'Đang chạy'
            ],
            'stop'=> [
                'label' => 'Tạm dừng'
            ],
        ];

        if(!empty($key)) return Arr::get($status, $key);

        return $status;
    }
    static function displayWheel($key = null): array|string
    {
        $asset = Path::plugin(CP_WHEEL_NAME).'/assets/images/';
        $styles = [
            'style1' => Url::base().$asset.'wheel1.png',
            'style2' => Url::base().$asset.'wheel2.png',
            'style3' => Url::base().$asset.'wheel3.png',
            'style4' => Url::base().$asset.'wheel4.png',
            'style5' => Url::base().$asset.'wheel5.png',
            'style6' => Url::base().$asset.'wheel6.png',
            'style7' => Url::base().$asset.'wheel7.png',
            'style8' => Url::base().$asset.'wheel8.png',
            'style9' => Url::base().$asset.'wheel9.png',
            'style10' => Url::base().$asset.'wheel10.png',
        ];
        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];
        return $styles;
    }
    static function displayBackground($key = null): array|string
    {
        $asset = Path::plugin(CP_WHEEL_NAME).'/assets/images/';
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
            'style21' => 'background: url(\''.Url::base().$asset.'bg-1.jpg\');',
            'style22' => 'background: url(\''.Url::base().$asset.'bg-2.jpg\');',
            'style23' => 'background: url(\''.Url::base().$asset.'bg-3.jpg\');',
            'style24' => 'background: url(\''.Url::base().$asset.'bg-4.jpg\');',
            'style25' => 'background: url(\''.Url::base().$asset.'bg-5.jpg\');',
            'style26' => 'background: url(\''.Url::base().$asset.'bg-6.jpg\');',
        ];
        if(!empty($key)) return (isset($background[$key])) ? $background[$key] : [];
        return $background;
    }
    static function displayText($id = 0) {

        $displayText = [
            'vi' => [
                'popup_heading_text'        => 'ƯU ĐÃI DÀNH CHO BẠN',
                'popup_main_text'           => 'Đừng bỏ qua cơ hội nhận được nhiều ưu đãi hấp dẫn từ vòng xoay may mắn. Bạn có may mắn hôm nay? Hãy thử ngay!',
                'popup_rules_text'          => "<strong>Luật chơi</strong>\n- Bạn chỉ có thể quay một lần với một Email.\n- Email đăng ký phải trùng với email lúc đặt hàng.\n- Không áp dụng với các sản phẩm đã giảm giá",
                'popup_win_heading_text'    => 'TUYỆT VỜI! BẠN ĐÃ NHẬN ĐƯỢC {slice}',
                'popup_win_main_text'       => "Hãy lưu lại mã coupon giảm giá và sử dụng trước ngày hết hạn\n\n{couponcode}",
                'popup_lose_heading_text'   => 'ÔI KHÔNG!',
                'popup_lose_main_text'      => 'Một chút xíu nữa thôi, chúc bạn may mắn lần sau.',
                'email_win_subject'         => 'Your coupon code',
                'email_win_message'         => "{firstname}, here is you coupon for {slice}:\n\n{couponcode}\n\nVisit us at {siteurl}",
                'lang_enter_your_email'     => 'Nhập địa chỉ email của bạn',
                'lang_enter_your_full_name' => 'Nhập họ tên của bạn',
                'lang_enter_phone_number'   => 'Nhập số điện thoại của bạn',
                'lang_i_agree'              => 'I agree with rules and privacy policy',
                'lang_spin_button'          => 'QUAY NGAY',
                'lang_continue_button'      => 'ĐÓNG',
                'lang_spin_again'           => 'QUAY TIẾP',
                'lang_input_missing'        => 'Vui lòng điền đầy đủ vào mẫu',
                'lang_no_spins'             => 'Xin lỗi! chương trình đã kết thúc hoặc số phần thưởng đã hết.',
                'lang_ace_email_check'      => 'Vui lòng cung cấp địa chỉ email hợp lệ',
                'lang_ace_limit_reached'    => 'Bạn đã dùng hết lượt quay.',
                'lang_coupon_notice'        => 'Mã khuyến mãi của bạn: {couponcode} thời gian sử dụng còn {timer}. Bạn có thể sử dụng khi thanh toán!',
                'lang_close'                => 'Bạn muốn đóng vòng xoay?',
                'lang_days'                 => 'days',
            ]
        ];

        if(!empty($id)) {

            $wheelText = Wheel::getMeta($id, 'displayText', true);

            if(!have_posts($wheelText)) $wheelText = ['vi' => []];

            $wheelText['vi'] = array_merge($displayText['vi'], $wheelText['vi']);

            return $wheelText;
        }

        return $displayText;
    }
    static function displayTemplate($id = 0): array
    {
        $displayTemplate = [
            'background'        => 'style1',
            'style'             => 'style2',
            'frame'             => 'style1',
            'center'            => 'style1',
            'textColor'         => '#fff',
            'btnRunBgColor'     => '#416dea',
            'btnRunTxtColor'    => '#fff',
            'triggerStyle'      => 'style1',
            'triggerIcon'       => '',
            'triggerEffect'     => 'trigger-animate-swing',
            'triggerBg'         => '#ffe2e2',
            'triggerOpen'       => 1,
        ];;

        if(!empty($id)) {

            $wheelDisplay = Wheel::getMeta($id, 'displayTemplate', true);

            if(!have_posts($wheelDisplay)) $wheelDisplay = [];

            return array_merge($displayTemplate, $wheelDisplay);
        }

        return $displayTemplate;
    }
    static function displayGift($key = null): array|string
    {
        $asset = Path::plugin(CP_WHEEL_NAME).'/assets/images/';
        $styles = [
            'style1' => Url::base().$asset.'trigger-0.png',
            'style2' => Url::base().$asset.'trigger-1.png',
            'style3' => Url::base().$asset.'trigger-2.png',
            'style4' => Url::base().$asset.'trigger-3.png',
            'style5' => Url::base().$asset.'trigger-4.png',
            'style6' => Url::base().$asset.'trigger-5.png',
            'style7' => Url::base().$asset.'trigger-6.png',
        ];
        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];
        return $styles;
    }
    static function displayImgCenter($key = null): array|string
    {
        $asset = Path::plugin(CP_WHEEL_NAME).'/assets/images/';
        $styles = [
            'style1' => Url::base().$asset.'center-1.png',
            'style2' => Url::base().$asset.'center-2.png',
            'style3' => Url::base().$asset.'center-3.png',
            'style4' => Url::base().$asset.'center-4.png',
            'style5' => Url::base().$asset.'center-5.png',
        ];
        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];
        return $styles;
    }
    static function displayFrame($key = null): array|string
    {
        $asset = Path::plugin(CP_WHEEL_NAME).'/assets/images/';
        $styles = [
            'style1' => Url::base().$asset.'frame-0.png',
            'style2' => Url::base().$asset.'frame-1.png',
            'style3' => Url::base().$asset.'frame-2.png',
            'style4' => Url::base().$asset.'frame-3.png',
            'style5' => Url::base().$asset.'frame-4.png',
            'style6' => Url::base().$asset.'frame-5.png',
            'style7' => Url::base().$asset.'frame-6.png',
            'style8' => Url::base().$asset.'frame-7.png',
        ];
        if(!empty($key)) return (isset($styles[$key])) ? $styles[$key] : [];
        return $styles;
    }
    static function cookies() {
        // have a cookie :-)
        $cookie_hash = md5(uniqid('',true));
        if (isset($_COOKIE['couponWheel_session']) && strlen($_COOKIE['couponWheel_session']) == 32) $cookie_hash = $_COOKIE['couponWheel_session'];
        setcookie('couponWheel_session',$cookie_hash,strtotime('+365 days'),'/');
        return $cookie_hash;
    }
    static function getIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
    static function validateEmail($email): bool
    {
        $is_valid_mail = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (str_contains($email, '+')) return false;

        if ($is_valid_mail) return true;

        return false;
    }
    static function validateLimit($dataLimit, $wheel): bool
    {
        if(!empty($dataLimit['email'])) {
            $dataLimit['email'] = email_dot_duplicate_check($dataLimit['email']);
        }

        $reset_counter_days = time() - ($wheel->reset_counter_days*86400);

        if ($wheel->reset_counter_days == 0) $reset_counter_days = 1;

        $ace_num_rows = WheelLog::count(Qr::set('wheel_id', $wheel->id)->where('timestamp', '>', $reset_counter_days)->where(function($query) use ($dataLimit) {
            $query->where('user_cookie', $dataLimit['cookie'])
                ->orWhere('device_id', $dataLimit['deviceId'])
                ->orWhere('phone', $dataLimit['phone']);
            if(!empty($dataLimit['email'])) {
                $query->orWhere('email', $dataLimit['email']);
            }
            if(!empty($dataLimit['user_id'])) {
                $query->orWhere('user_id', $dataLimit['user_id']);
            }
        }));

        if($ace_num_rows >= $wheel->max_spins_per_user) return false;

        return true;
    }
    static function renderPopup($wheel): void
    {
        $cacheId = 'coupon_wheel_popup_'.md5($wheel->id);

        if(Auth::check()) {
            $userData = serialize(Auth::user());
            $cacheId .= '_'.md5($userData);
        }

        $html = \SkillDo\Cache::get($cacheId);

        if(empty($html)) {

            $wheelPath = Path::plugin(CP_WHEEL_NAME);

            $wheelDisplay = WheelHelper::displayTemplate($wheel->id);

            $wheelText =WheelHelper::displayText($wheel->id);

            $wheelText = $wheelText[Language::current()];

            $currentUser = Auth::user();

            $wheel->lang_close = $wheelText['lang_close'];

            $html = Plugin::partial(CP_WHEEL_NAME, 'popup', [
                'wheel'         => $wheel,
                'wheelPath'     => $wheelPath,
                'wheelDisplay'  => $wheelDisplay,
                'wheelText'     => $wheelText,
                'currentUser'   => $currentUser,
            ]);

            \SkillDo\Cache::save($cacheId, $html);
        }

        echo $html;
    }
}

function email_dot_duplicate_check($email) {

    //additional anti-cheat checker for gmail addresses
    if (!str_contains($email, '@gmail.com')) return $email;

    $sql ="SELECT * FROM (SELECT REPLACE(email,'.','') as check_alias, `email` FROM ".CLE_PREFIX."wheels_log) as result WHERE `check_alias` = '".str_replace('.','',$email)."'";

    $model = model('wheels_log');

    $result = $model->query($sql);

    $row = [];

    foreach ($result as $data)
    {
        $row = $data;
    }

    if (have_posts($row)) return $row->email;

    return $email;
}

function template_parser($template,$template_vars) {

    $output = $template;

    $template_vars['siteurl'] = Url::base();

    foreach($template_vars as $key => $value)
    {
        if(!empty($template_vars["$key"])) $output = str_replace('{'.$key.'}',$template_vars["$key"],$output);
    }

    return $output;
}