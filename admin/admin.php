<?php
include_once 'roles.php';

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
        if(class_exists('MarketingOnline')) {
            if(Auth::hasCap('couponWheelList')) {
                AdminMenu::addSub('marketing', 'couponWheel', 'Chiến dịch vòng quay', 'plugins?page=couponWheel', ['callback' => 'AdminCouponWheel::page']);
            }
            if(Auth::hasCap('couponWheelLogList')) {
                $count = CacheHandler::get('coupon_wheel_log_is_read');
                if(empty($count)) {
                    $count = WheelLog::count(Qr::set('is_read', 0));
                }
                AdminMenu::addSub('marketing', 'couponWheelLog', 'Thống kê lượt quay', 'plugins?page=couponWheelLog', [
                    'callback' => 'AdminCouponWheelLog::page',
                    'count' => $count
                ]);
            }
        }
    }
    static function page(): void
    {
        $view = Request::get('view');
        $view = (empty($view)) ? 'index' : $view;
        if($view == 'index' ) {
            AdminCouponWheel::pageList();
        }
        if( $view == 'add' ) {
            AdminCouponWheel::pageAdd();
        }
        if( $view == 'edit' ) {
            AdminCouponWheel::pageEdit();
        }
    }
    static function pageList(): void
    {
        if (Auth::hasCap('couponWheelList')) {
            include 'views/coupon-wheel/index.php';
        }
        else {
            echo notice('error', 'Bạn không có quyền sử dụng chức năng này', false);
        }
    }
    static function pageAdd(): void
    {
        if (Auth::hasCap('couponWheelAdd')) {
            extract(self::formData());
            include 'views/coupon-wheel/add.php';
        }
        else {
            echo notice('error', 'Bạn không có quyền sử dụng chức năng này', false);
        }
    }
    static function pageEdit(): void
    {
        if (Auth::hasCap('couponWheelEdit')) {
            $id     = (int)Request::get('id');
            $wheel  = Wheel::get($id);
            if(have_posts($wheel)) {
                extract(self::formData($wheel));
                include 'views/coupon-wheel/edit.php';
            }
            else {
                echo notice('error', 'Chương trình vòng quay này chưa được tạo hoặc đã bị xóa.', false, 'chương trình vòng quay không tồn tại');
            }
        }
        else {
            echo notice('error', 'Bạn không có quyền sử dụng chức năng này', false);
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
        $formBase = new FormBuilder();

        $formBase->add('name', 'text', ['label' => 'Tên chiến dịch'], $wheel['name']);

        $formBase->add('is_live', 'radio', ['label' => 'Tùy chọn hiển thị', 'options' => [
            0 => 'Tất cả thiết bị',
            1 => 'Chỉ trên máy tính',
            2 => 'Chỉ trên di động',
        ], 'single' => true], $wheel['is_live']);

        $formBase->add('require_user', 'radio', ['label' => 'Đối tượng quay', 'options' => [
            0 => 'Tất cả khách hàng',
            1 => 'Bắt buộc đăng nhập',
        ], 'single' => true], $wheel['require_user']);

        $formBase->add('max_spins_per_user', 'text', [
            'label' => 'Số lần quay tối đa',
            'note' => 'Số lần quay tối đa trên mỗi lươt chơi của người dùng'
        ], $wheel['max_spins_per_user']);
        $formBase->add('reset_counter_days', 'number', [
            'label' => 'Thời gian lượt chơi tiếp theo (Ngày)',
            'note' => 'Thời gian khách hàng có thể chơi lượt chơi tiếp theo sau khi đã hoàn thành lượt chơi'
        ], $wheel['reset_counter_days']);
        $formBase->add('show_popup_after', 'number', [
            'label' => 'Thời gian tự hiển thị sau khi khách hàng truy cập (giây)',
            'note' => 'Chỉ có giá trị khi chức năng tự động hiển thị khi vào website được kích hoạt.'
        ], $wheel['show_popup_after']);
        $formBase->add('wheel_spin_time', 'number', [
            'label' => 'Thời gian quay của vòng quay (giây)',
        ], $wheel['wheel_spin_time']);

        //Phần thưởng
        $formAward = [];
        for($i = 1; $i <= 12; $i++) {
            $formAward[$i] = new FormBuilder();
            $formAward[$i]->add('award['.$i.'][label]', 'text', ['label' => 'Tên phần thưởng', 'start' => 6], $wheelAward[$i]['label']);
            $formAward[$i]->add('award['.$i.'][value]', 'text', ['label' => 'Giá trị', 'start' => 6], $wheelAward[$i]['value']);
            $formAward[$i]->add('award['.$i.'][infinite]', 'checkbox', ['label' => 'Không hạn chế số lần trúng'],
                ($wheelAward[$i]['infinite'] == 1) ? 'award_'.$i.'_infinite' : 0
            );
            $formAward[$i]->add('award['.$i.'][qty]', 'number', ['label' => 'Số lần trúng', 'start' => 6], $wheelAward[$i]['qty']);
            $formAward[$i]->add('award['.$i.'][percent]', 'select', ['label' => 'Tỷ lệ trúng', 'start' => 6, 'options' => [
                '100'   => 'Rất cao',
                '25'    => 'Cao',
                '12'    => 'Bình thường',
                '6'     => 'Thấp',
                '1'     => 'Rất thấp'
            ]], $wheelAward[$i]['percent']);
        }

        //Văn bản
        $formText['first'] = new FormBuilder();

        $formText['alertSuccess'] = new FormBuilder();

        $formText['alertFailed'] = new FormBuilder();

        $formText['alert'] = new FormBuilder();

        $formText['form'] = new FormBuilder();

        $formText['form']->add('require_email', 'switch', ['label' => 'Sử dụng ô nhập email'], $wheel['require_email']);

        $languages = Language::list();

        $hasMulti = Language::hasMulti();

        foreach ($languages as $langKey => $language) {
            $formText['first']->add('displayText['.$langKey.'][popup_heading_text]', 'text', [
                'label' => 'Tiêu đề lớn'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['popup_heading_text'])) ? $wheelText[$langKey]['popup_heading_text'] : '');

            $formText['first']->add('displayText['.$langKey.'][popup_rules_text]', 'textarea', [
                'label' => 'Mô tả luật quay'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['popup_rules_text'])) ? $wheelText[$langKey]['popup_rules_text'] : '');

            $formText['first']->add('displayText['.$langKey.'][popup_main_text]', 'textarea', [
                'label' => 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['popup_main_text'])) ? $wheelText[$langKey]['popup_main_text'] : '');

            $formText['alertSuccess']->add('displayText['.$langKey.'][popup_win_heading_text]', 'text', [
                'label' => 'Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : ''),
                'note' => 'khi khách quay được quà tặng'
            ], (!empty($wheelText[$langKey]['popup_win_heading_text'])) ? $wheelText[$langKey]['popup_win_heading_text'] : '');

            $formText['alertSuccess']->add('displayText['.$langKey.'][popup_win_main_text]', 'textarea', [
                'label' => 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : ''),
                'note' => 'khi khách quay được quà tặng'
            ], (!empty($wheelText[$langKey]['popup_win_main_text'])) ? $wheelText[$langKey]['popup_win_main_text'] : '');

            $formText['alertFailed']->add('displayText['.$langKey.'][popup_lose_heading_text]', 'text', [
                'label' => 'Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : ''),
                'note' => 'khi khách quay không nhận được quà tặng'
            ], (!empty($wheelText[$langKey]['popup_lose_heading_text'])) ? $wheelText[$langKey]['popup_lose_heading_text'] : '');

            $formText['alertFailed']->add('displayText['.$langKey.'][popup_lose_main_text]', 'textarea', [
                'label' => 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : ''),
                'note' => 'khi khách quay được quà tặng'
            ], (!empty($wheelText[$langKey]['popup_lose_main_text'])) ? $wheelText[$langKey]['popup_lose_main_text'] : '');


            $formText['alert']->add('displayText['.$langKey.'][lang_input_missing]', 'text', [
                'label' => 'Thông báo khi điền không đủ thông tin'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_input_missing'])) ? $wheelText[$langKey]['lang_input_missing'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_no_spins]', 'text', [
                'label' => 'Thông báo khi không còn quà'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_no_spins'])) ? $wheelText[$langKey]['lang_no_spins'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_ace_email_check]', 'text', [
                'label' => 'Thông báo khi trường email không hợp lệ'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_ace_email_check'])) ? $wheelText[$langKey]['lang_ace_email_check'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_ace_limit_reached]', 'text', [
                'label' => 'Thông báo khi hết lượt quay'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_ace_limit_reached'])) ? $wheelText[$langKey]['lang_ace_limit_reached'] : '');

            $formText['alert']->add('displayText['.$langKey.'][lang_close]', 'text', [
                'label' => 'Xác nhận khi đóng vòng quay'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_close'])) ? $wheelText[$langKey]['lang_close'] : '');


            $formText['form']->add('displayText['.$langKey.'][lang_enter_your_full_name]', 'text', [
                'label' => 'Ô nhập họ tên'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_enter_your_full_name'])) ? $wheelText[$langKey]['lang_enter_your_full_name'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_enter_your_email]', 'text', [
                'label' => 'Ô nhập email'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_enter_your_email'])) ? $wheelText[$langKey]['lang_enter_your_email'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_enter_phone_number]', 'text', [
                'label' => 'Ô nhập số điện thoại'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_enter_phone_number'])) ? $wheelText[$langKey]['lang_enter_phone_number'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_spin_button]', 'text', [
                'label' => 'Chữ nút (button) quay'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_spin_button'])) ? $wheelText[$langKey]['lang_spin_button'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_continue_button]', 'text', [
                'label' => 'Chữ nút (button) đóng'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_continue_button'])) ? $wheelText[$langKey]['lang_continue_button'] : '');

            $formText['form']->add('displayText['.$langKey.'][lang_spin_again]', 'text', [
                'label' => 'Chữ nút (button) quay tiếp'.($hasMulti ? ' ('.$language['label'].')' : '')
            ], (!empty($wheelText[$langKey]['lang_spin_again'])) ? $wheelText[$langKey]['lang_spin_again'] : '');
        }

        //trigger
        $formTrigger = new FormBuilder();
        $formTrigger->add('triggerOpen', 'switch', [
            'label' => 'Hiển thị Icon trigger',
            'start' => 12
        ], $wheelDisplay['triggerOpen']);
        $formTrigger->add('triggerIcon', 'image', [
            'label' => 'Icon tự chọn',
            'note' => 'Nếu bạn muốn tự tuỳ chỉnh icon, vui lòng tải lên hình ảnh có định dạng png kích thước 240x240',
            'start' => 4
        ], $wheelDisplay['triggerIcon']);
        $formTrigger->add('triggerEffect', 'select', [
            'label' => 'Hiệu ứng',
            'options' => [
                ''                      => 'Không hiệu ứng',
                'trigger-animate-swing' => 'Hiệu ứng 1',
                'trigger-animate-zoomin' => 'Hiệu ứng 2',
                'trigger-animate-wobble' => 'Hiệu ứng 3',
                'trigger-animate-bounce' => 'Hiệu ứng 4',
            ],
            'single' => true,
            'start' => 4
        ], $wheelDisplay['triggerEffect']);
        $formTrigger->add('triggerBg', 'color', [
            'label' => 'Màu nền',
            'start' => 4
        ], $wheelDisplay['triggerBg']);

        return [
            'formBase' => $formBase,
            'formText' => $formText,
            'formAward' => $formAward,
            'wheelText' => $wheelText,
            'wheelDisplay' => $wheelDisplay,
            'formTrigger' => $formTrigger,
        ];
    }
    static function buttonActionBar($module): void
    {
        $view   = (empty(Str::clear(Request::get('view')))) ? 'index' : Str::clear(Request::get('view'));
        $class  = Template::getClass();
        $page   = Request::get('page');

        if($class == 'plugins' && $page == 'couponWheel' && $view == 'index') {
            echo '<div class="pull-left">'; echo '</div>';
            if(Auth::hasCap('couponWheelAdd')) {
                echo '<div class="pull-right">';
                echo '<a href="'.Url::admin('plugins?page=couponWheel&view=add').'" class="btn btn-icon btn-green">'.Admin::icon('add').' Tạo chương trình</a>';
                echo '</div>';
            }
        }

        if($class == 'plugins' && $page == 'couponWheel' && $view == 'add') {
            echo '<div class="pull-left">'; echo '</div>';
            echo '<div class="pull-right">';
            echo '<button name="save" class="btn btn-icon btn-green" form="js_coupon_wheel_form">'.Admin::icon('save').' Lưu</button>';
            echo '<a href="'.Url::admin('plugins?page=couponWheel').'" class="btn btn-icon btn-blue">'.Admin::icon('back').' Quay lại</a>';
            echo '</div>';
        }

        if($class == 'plugins' && $page == 'couponWheel' && $view == 'edit') {
            echo '<div class="pull-left">'; echo '</div>';
            echo '<div class="pull-right">';
            echo '<button name="save" class="btn btn-icon btn-green" form="js_coupon_wheel_form">'.Admin::icon('save').' Lưu</button>';
            echo '<a href="'.Url::admin('plugins?page=couponWheel').'" class="btn btn-icon btn-blue">'.Admin::icon('back').' Quay lại</a>';
            echo '</div>';
        }
    }
}
add_action('action_bar_before', 'AdminCouponWheel::buttonActionBar');
add_action('admin_init', 'AdminCouponWheel::navigation', 50);
add_action('admin_init', 'AdminCouponWheel::assets', 50);

Class AdminCouponWheelLog {
    static function page(): void
    {
        if (Auth::hasCap('couponWheelLogList')) {
            include 'views/coupon-wheel-log/index.php';
        }
        else {
            echo notice('error', 'Bạn không có quyền sử dụng chức năng này', false);
        }
    }
}