<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

class AdminCouponWheelAjax {
    #[NoReturn]
    static function loadRun(Request $request, $model): void
    {
        if($request->isMethod('post')) {
            
            $args   = Qr::set('status', 'run');
            
            $object = Wheel::get($args);
            
            $result =  '';
            
            if(have_posts($object)) {
                $result = Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel/item', ['item' => $object]);
            }
            
            $result = base64_encode($result);

            response()->success(trans('ajax.add.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }
    #[NoReturn]
    static function loadOrder(Request $request, $model): void
    {

        if($request->isMethod('post')) {

            $page   = $request->input('page');

            $limit  = $request->input('limit');

            $args   = Qr::set('status', '<>', 'run');

            $total  = Wheel::count($args);

            # [Pagination]
            $url = '#{page}';

            $pagination = pagination($total, $url, $limit, $page);

            # [Data]
            $args->limit($limit)->offset($pagination->offset())->orderByDesc('created');

            $objects = Wheel::gets($args);

            $result['data'] = '';

            if(have_posts($objects)) {
                foreach ($objects as $object) {
                    $result['data'] .= Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel/item', ['item' => $object]);
                }
            }

            $result['data']         = base64_encode($result['data']);

            $result['pagination']   = base64_encode($pagination->frontend());

            response()->success(trans('ajax.load.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }
    #[NoReturn]
    static function add(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $wheel = [];

            $validate = $request->validate([
                'name' => Rule::make('Tên chương trình')->notEmpty(),
                'is_live' => Rule::make('Tên chương trình')->notEmpty()->integer(),
                'max_spins_per_user' => Rule::make('Số lần quay tối đa')->notEmpty()->integer(),
                'reset_counter_days' => Rule::make('Thời gian lượt chơi tiếp theo')->notEmpty()->integer(),
                'show_popup_after' => Rule::make('Thời gian tự hiển thị sau khi khách hàng truy cập')->notEmpty()->integer(),
                'wheel_spin_time' => Rule::make('Thời gian quay của vòng quay')->notEmpty()->integer(),
                'background' => Rule::make('Màu nền cho popup')->notEmpty(),
                'style' => Rule::make('Màu nền cho popup')->notEmpty(),
                'frame' => Rule::make('Kiểu khung vòng quay')->notEmpty(),
                'center' => Rule::make('Kiểu tâm vòng quay')->notEmpty(),
                'textColor' => Rule::make('Màu chữ popup')->notEmpty()->color(),
                'btnRunBgColor' => Rule::make('màu nền nút quay')->notEmpty(),
                'btnRunTxtColor' => Rule::make('màu chữ nút quay')->notEmpty(),
            ]);

            if ($validate->fails()) {
                response()->error($validate->errors());
            }

            $wheel['name'] = $request->input('name');

            $wheel['is_live'] = $request->input('is_live');

            $wheel['max_spins_per_user'] = $request->input('max_spins_per_user');

            $wheel['reset_counter_days'] = $request->input('reset_counter_days');

            $wheel['show_popup_after'] = $request->input('show_popup_after');

            $wheel['wheel_spin_time'] = $request->input('wheel_spin_time');

            $award = $request->input('award');

            foreach (range(1,12) as $i) {
                if(empty($award[$i]['label'])) {
                    response()->error(trans('Tên phần thưởng số '.$i.' không được để trống'));
                }
                if(!isset($award[$i]['value'])) {
                    response()->error(trans('Giá trị phần thưởng số '.$i.' không được để trống'));
                }
                if(!is_numeric($award[$i]['qty'])) {
                    response()->error(trans('Số lần trúng phần thưởng số '.$i.' không được để trống'));
                }
                if(!is_numeric($award[$i]['percent'])) {
                    response()->error(trans('Tỷ lệ trúng phần thưởng số '.$i.' không đúng định dạng'));
                }
                $wheel['slice'.$i.'_label']     = $award[$i]['label'];
                $wheel['slice'.$i.'_value']     = $award[$i]['value'];
                $wheel['slice'.$i.'_qty']       = $award[$i]['qty'];
                $wheel['slice'.$i.'_percent']   = $award[$i]['percent'];
                $wheel['slice'.$i.'_infinite']  = 0;
                if(!empty($award[$i]['infinite'])) $wheel['slice'.$i.'_infinite'] = 1;
            }

            //Cấu hình giao diện
            $wheelTemplate = [];

            $wheelTemplate['background'] = $request->input('background');

            $wheelTemplate['style'] = $request->input('style');

            $wheelTemplate['frame'] = $request->input('frame');

            $wheelTemplate['center'] = $request->input('center');

            $wheelTemplate['textColor'] = $request->input('textColor');

            $wheelTemplate['btnRunBgColor'] = $request->input('btnRunBgColor');

            $wheelTemplate['btnRunTxtColor'] = $request->input('btnRunTxtColor');

            $wheelTemplate['triggerOpen'] = (empty($request->input('triggerOpen')))  ? 0 : 1;

            if($request->input('triggerStyle') !== null) {
                $wheelTemplate['triggerStyle'] = $request->input('triggerStyle');
            }
            if($request->input('triggerEffect') !== null) {
                $wheelTemplate['triggerEffect'] = $request->input('triggerEffect');
            }
            if($request->input('triggerBg') !== null) {
                $wheelTemplate['triggerBg'] = $request->input('triggerBg');
            }
            if($request->input('triggerIcon') !== null) {
                $wheelTemplate['triggerIcon'] = FileHandler::handlingUrl($request->input('triggerIcon'));
            }

            //Cấu hình văn bản
            $wheelText = [];

            $displayText = $request->input('displayText');

            $languages = Language::list();

            $hasMulti = Language::isMulti();

            foreach ($languages as $langKey => $language) {

                if(empty($displayText[$langKey]['popup_heading_text'])) {
                    response()->error(trans('Tiêu đề lớn'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_heading_text'] = $displayText[$langKey]['popup_heading_text'];

                if(empty($displayText[$langKey]['popup_rules_text'])) {
                    response()->error(trans('Mô tả luật quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_rules_text'] = $displayText[$langKey]['popup_rules_text'];

                if(empty($displayText[$langKey]['popup_main_text'])) {
                    response()->error(trans('Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_main_text'] = $displayText[$langKey]['popup_main_text'];

                if(empty($displayText[$langKey]['popup_win_heading_text'])) {
                    response()->error(trans('Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_win_heading_text'] = $displayText[$langKey]['popup_win_heading_text'];

                if(empty($displayText[$langKey]['popup_win_main_text'])) {
                    response()->error(trans('Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_win_main_text'] = $displayText[$langKey]['popup_win_main_text'];

                if(empty($displayText[$langKey]['popup_lose_heading_text'])) {
                    response()->error(trans('Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_lose_heading_text'] = $displayText[$langKey]['popup_lose_heading_text'];

                if(empty($displayText[$langKey]['popup_lose_main_text'])) {
                    response()->error(trans('Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_lose_main_text'] = $displayText[$langKey]['popup_lose_main_text'];

                if(empty($displayText[$langKey]['lang_enter_your_full_name'])) {
                    response()->error(trans('Ô nhập họ tên'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_enter_your_full_name'] = $displayText[$langKey]['lang_enter_your_full_name'];

                if(empty($displayText[$langKey]['lang_enter_your_email'])) {
                    response()->error(trans('Ô nhập email'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_enter_your_email'] = $displayText[$langKey]['lang_enter_your_email'];

                if(empty($displayText[$langKey]['lang_enter_phone_number'])) {
                    response()->error(trans('Ô nhập số điện thoại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_enter_phone_number'] = $displayText[$langKey]['lang_enter_phone_number'];

                if(empty($displayText[$langKey]['lang_spin_button'])) {
                    response()->error(trans('Chữ nút quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_spin_button'] = $displayText[$langKey]['lang_spin_button'];

                if(empty($displayText[$langKey]['lang_continue_button'])) {
                    response()->error(trans('Chữ nút tiếp tục'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_continue_button'] = $displayText[$langKey]['lang_continue_button'];

                if(empty($displayText[$langKey]['lang_spin_again'])) {
                    response()->error(trans('Chữ nút thử lại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_spin_again'] = $displayText[$langKey]['lang_spin_again'];

                if(empty($displayText[$langKey]['lang_input_missing'])) {
                    response()->error(trans('Thông báo khi điền không đủ thông tin'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_input_missing'] = $displayText[$langKey]['lang_input_missing'];

                if(empty($displayText[$langKey]['lang_no_spins'])) {
                    response()->error(trans('Thông báo khi không còn quà'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_no_spins'] = $displayText[$langKey]['lang_no_spins'];

                if(empty($displayText[$langKey]['lang_ace_email_check'])) {
                    response()->error(trans('Thông báo khi trường email không hợp lệ'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_ace_email_check'] = $displayText[$langKey]['lang_ace_email_check'];

                if(empty($displayText[$langKey]['lang_ace_limit_reached'])) {
                    response()->error(trans('Thông báo khi hết lượt quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_ace_limit_reached'] = $displayText[$langKey]['lang_ace_limit_reached'];

                if(empty($displayText[$langKey]['lang_close'])) {
                    response()->error(trans('Xác nhận khi đóng vòng quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_close'] = $displayText[$langKey]['lang_close'];
            }

            $wheel['require_email'] = (empty($request->input('require_email')))  ? 0 : 1;

            $wheel['require_user'] = (empty($request->input('require_user')))  ? 0 : 1;

            $wheel['status'] = 'pending';

            $id = Wheel::insert($wheel);

            if(!is_skd_error($id)) {

                Wheel::updateMeta($id, 'displayTemplate', $wheelTemplate);

                Wheel::updateMeta($id, 'displayText', $wheelText);

                response()->success(trans('ajax.save.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }
    #[NoReturn]
    static function save(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $id         = (int)$request->input('id');

            $object  = Wheel::get($id);

            if(!have_posts($object)) {
                response()->error(trans('Chương trình vòng quay này không tồn tại trên hệ thống'));
            }

            $validate = $request->validate([
                'name' => Rule::make('Tên chương trình')->notEmpty(),
                'is_live' => Rule::make('Tên chương trình')->notEmpty()->integer(),
                'max_spins_per_user' => Rule::make('Số lần quay tối đa')->notEmpty()->integer(),
                'reset_counter_days' => Rule::make('Thời gian lượt chơi tiếp theo')->notEmpty()->integer(),
                'show_popup_after' => Rule::make('Thời gian tự hiển thị sau khi khách hàng truy cập')->notEmpty()->integer(),
                'wheel_spin_time' => Rule::make('Thời gian quay của vòng quay')->notEmpty()->integer(),
                'background' => Rule::make('Màu nền cho popup')->notEmpty(),
                'style' => Rule::make('Màu nền cho popup')->notEmpty(),
                'frame' => Rule::make('Kiểu khung vòng quay')->notEmpty(),
                'center' => Rule::make('Kiểu tâm vòng quay')->notEmpty(),
                'textColor' => Rule::make('Màu chữ popup')->notEmpty()->color(),
                'btnRunBgColor' => Rule::make('màu nền nút quay')->notEmpty(),
                'btnRunTxtColor' => Rule::make('màu chữ nút quay')->notEmpty(),
            ]);

            if ($validate->fails()) {
                response()->error($validate->errors());
            }

            $wheel = [];

            $wheel['name'] = $request->input('name');

            $wheel['is_live'] = $request->input('is_live');

            $wheel['max_spins_per_user'] = $request->input('max_spins_per_user');

            $wheel['reset_counter_days'] = $request->input('reset_counter_days');

            $wheel['show_popup_after'] = $request->input('show_popup_after');

            $wheel['wheel_spin_time'] = $request->input('wheel_spin_time');

            $award = $request->input('award');

            foreach (range(1,12) as $i) {
                if(empty($award[$i]['label'])) {
                    response()->error(trans('Tên phần thưởng số '.$i.' không được để trống'));
                }
                if(!isset($award[$i]['value'])) {
                    response()->error(trans('Giá trị phần thưởng số '.$i.' không được để trống'));
                }
                if(!is_numeric($award[$i]['qty'])) {
                    response()->error(trans('Số lần trúng phần thưởng số '.$i.' không được để trống'));
                }
                if(!is_numeric($award[$i]['percent'])) {
                    response()->error(trans('Tỷ lệ trúng phần thưởng số '.$i.' không đúng định dạng'));
                }
                $wheel['slice'.$i.'_label']     = $award[$i]['label'];
                $wheel['slice'.$i.'_value']     = $award[$i]['value'];
                $wheel['slice'.$i.'_qty']       = $award[$i]['qty'];
                $wheel['slice'.$i.'_percent']   = $award[$i]['percent'];
                $wheel['slice'.$i.'_infinite']  = 0;
                if(!empty($award[$i]['infinite'])) $wheel['slice'.$i.'_infinite'] = 1;
            }

            //Cấu hình giao diện
            $wheelTemplate = [];

            $wheelTemplate['background'] = $request->input('background');

            $wheelTemplate['style'] = $request->input('style');

            $wheelTemplate['frame'] = $request->input('frame');

            $wheelTemplate['center'] = $request->input('center');

            $wheelTemplate['textColor'] = $request->input('textColor');

            $wheelTemplate['btnRunBgColor'] = $request->input('btnRunBgColor');

            $wheelTemplate['btnRunTxtColor'] = $request->input('btnRunTxtColor');

            $wheelTemplate['triggerOpen'] = (empty($request->input('triggerOpen')))  ? 0 : 1;

            if($request->input('triggerStyle') !== null) {
                $wheelTemplate['triggerStyle'] = $request->input('triggerStyle');
            }
            if($request->input('triggerEffect') !== null) {
                $wheelTemplate['triggerEffect'] = $request->input('triggerEffect');
            }
            if($request->input('triggerBg') !== null) {
                $wheelTemplate['triggerBg'] = $request->input('triggerBg');
            }
            if($request->input('triggerIcon') !== null) {
                $wheelTemplate['triggerIcon'] = FileHandler::handlingUrl($request->input('triggerIcon'));
            }

            //Cấu hình văn bản
            $wheelText = [];

            $displayText = $request->input('displayText');

            $languages = Language::list();

            $hasMulti = Language::isMulti();

            foreach ($languages as $langKey => $language) {

                if(empty($displayText[$langKey]['popup_heading_text'])) {
                    response()->error(trans('Tiêu đề lớn'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_heading_text'] = $displayText[$langKey]['popup_heading_text'];

                if(empty($displayText[$langKey]['popup_rules_text'])) {
                    response()->error(trans('Mô tả luật quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_rules_text'] = $displayText[$langKey]['popup_rules_text'];

                if(empty($displayText[$langKey]['popup_main_text'])) {
                    response()->error(trans('Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_main_text'] = $displayText[$langKey]['popup_main_text'];

                if(empty($displayText[$langKey]['popup_win_heading_text'])) {
                    response()->error(trans('Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_win_heading_text'] = $displayText[$langKey]['popup_win_heading_text'];

                if(empty($displayText[$langKey]['popup_win_main_text'])) {
                    response()->error(trans('Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_win_main_text'] = $displayText[$langKey]['popup_win_main_text'];

                if(empty($displayText[$langKey]['popup_lose_heading_text'])) {
                    response()->error(trans('Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_lose_heading_text'] = $displayText[$langKey]['popup_lose_heading_text'];

                if(empty($displayText[$langKey]['popup_lose_main_text'])) {
                    response()->error(trans('Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống'));
                }
                $wheelText[$langKey]['popup_lose_main_text'] = $displayText[$langKey]['popup_lose_main_text'];

                if(empty($displayText[$langKey]['lang_enter_your_full_name'])) {
                    response()->error(trans('Ô nhập họ tên'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_enter_your_full_name'] = $displayText[$langKey]['lang_enter_your_full_name'];

                if(empty($displayText[$langKey]['lang_enter_your_email'])) {
                    response()->error(trans('Ô nhập email'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_enter_your_email'] = $displayText[$langKey]['lang_enter_your_email'];

                if(empty($displayText[$langKey]['lang_enter_phone_number'])) {
                    response()->error(trans('Ô nhập số điện thoại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_enter_phone_number'] = $displayText[$langKey]['lang_enter_phone_number'];

                if(empty($displayText[$langKey]['lang_spin_button'])) {
                    response()->error(trans('Chữ nút quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_spin_button'] = $displayText[$langKey]['lang_spin_button'];

                if(empty($displayText[$langKey]['lang_continue_button'])) {
                    response()->error(trans('Chữ nút tiếp tục'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_continue_button'] = $displayText[$langKey]['lang_continue_button'];

                if(empty($displayText[$langKey]['lang_spin_again'])) {
                    response()->error(trans('Chữ nút thử lại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống'));
                }
                $wheelText[$langKey]['lang_spin_again'] = $displayText[$langKey]['lang_spin_again'];
            }

            $wheel['require_email'] = (empty($request->input('require_email')))  ? 0 : 1;

            $wheel['require_user'] = (empty($request->input('require_user')))  ? 0 : 1;

            $wheel['id'] = $object->id;

            $id = Wheel::insert($wheel, $object);

            if(!is_skd_error($id)) {

                CacheHandler::delete('coupon_wheel_popup_'.md5($object->id));

                CacheHandler::delete('coupon_wheel_popup_run');

                Wheel::updateMeta($id, 'displayTemplate', $wheelTemplate);

                Wheel::updateMeta($id, 'displayText', $wheelText);

                response()->success(trans('ajax.save.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }
    #[NoReturn]
    static function status(Request $request, $model): void {

        if($request->isMethod('post')) {

            $id     = (int)$request->input('id');

            $wheel  = Wheel::get($id);

            if(!have_posts($wheel)) {
                response()->error(trans('Chương trình vòng quay này không tồn tại trên hệ thống'));
            }

            $status = trim((string)$request->input('status'));

            if(empty($status)) {
                response()->error(trans('Trạng thái không được để trống'));
            }

            if(empty(WheelHelper::status($status))) {
                response()->error(trans('Trạng thái không đúng định dạng'));
            }

            if($wheel->status == $status) {
                response()->error(trans('Trạng thái chương trình không thay đổi'));
            }

            $id = Wheel::insert([
                'id' => $wheel->id,
                'status' => $status
            ], $wheel);

            if(!is_skd_error($id)) {

                CacheHandler::delete('coupon_wheel_popup_'.md5($wheel->id));

                CacheHandler::delete('coupon_wheel_popup_run');

                response()->success(trans('ajax.save.success'));
            }
        }

        response()->error(trans('ajax.save.error'));
    }
    #[NoReturn]
    static function delete(Request $request, $model): void {

        if($request->isMethod('post')) {

            $id     = (int)$request->input('data');

            $wheel  = Wheel::get($id);

            if(!have_posts($wheel)) {

                response()->error(trans('vòng quay này không tồn tại trên hệ thống'));
            }

            if(Wheel::delete($id)) {

                CacheHandler::delete('coupon_wheel_popup_'.md5($id));

                CacheHandler::delete('coupon_wheel_popup_run');

                response()->success(trans('ajax.delete.success'));
            }
        }

        response()->error(trans('ajax.delete.error'));
    }
}
Ajax::admin('AdminCouponWheelAjax::loadRun');
Ajax::admin('AdminCouponWheelAjax::loadOrder');
Ajax::admin('AdminCouponWheelAjax::add');
Ajax::admin('AdminCouponWheelAjax::save');
Ajax::admin('AdminCouponWheelAjax::status');
Ajax::admin('AdminCouponWheelAjax::delete');

class CouponWheelAjax {
    static function renderPopup(Request $request, $model): void
    {
        $cacheId = 'coupon_wheel_popup_run';

        $wheel = CacheHandler::get($cacheId);

        if(empty($wheel)) {
            $wheel = Wheel::get(Qr::set('status', 'run'));
            CacheHandler::save($cacheId, $wheel);
        }

        if(!have_posts($wheel)) {
            return;
        }
        if($wheel->is_live == 1 && Device::isMobile()) {
            return;
        }
        if($wheel->is_live == 2 && !Device::isMobile()) {
            return;
        }

        WheelHelper::renderPopup($wheel);
    }
    #[NoReturn]
    static function run(Request $request, $model): void {

        if($request->isMethod('post')) {

            $_COOKIE['couponWheel_session'] = WheelHelper::cookies();

            $id = (int)$request->input('wheel_hash');

            $wheel = Wheel::get(Qr::set($id)->where('status', 'run'));

            if(!have_posts($wheel)) {
                response()->error(trans('wheel.ajax.run.error.notFound'));
            }

            if($wheel->is_live == 1 && Device::isMobile()) {
                response()->error(trans('wheel.ajax.run.error.exits'));
            }

            if($wheel->is_live == 2 && !Device::isMobile()) {
                response()->error(trans('wheel.ajax.run.error.exits'));
            }

            if($wheel->require_user == 1 && !Auth::check()) {
                response()->error(trans('wheel.ajax.run.error.user'));
            }

            $displayText = WheelHelper::displayText($wheel->id);

            $displayText = $displayText[Language::current()];

            $ip = WheelHelper::getIp();

            $platform    = Device::getPlatform();

            $browser     = Device::getBrowser();

            $device      = (Device::isMobile()) ? Device::getMobile() : 'desktop';

            $deviceId    = md5($ip.$browser.$device.$platform);

            $limitData = [
                'cookie'    => $_COOKIE['couponWheel_session'],
                'deviceId'  => $deviceId,
            ];

            $wheelLog = [];

            if(Auth::check()) {
                $limitData['user_id'] = Auth::userID();
                $wheelLog['user_id'] = Auth::userID();
            }

            if (!isset($_COOKIE['couponWheel_session'])) {
                response()->error(trans('Spin error, cannot set cookies. Please reload webpage.'));
            }

            if($wheel->require_email) {
                if(empty($request->input('email'))) {
                    response()->error($displayText['lang_input_missing']);
                }
                if (!WheelHelper::validateEmail($request->input('email'))) {
                    response()->error($displayText['lang_ace_email_check']);
                }
                $limitData['email'] = $request->input('email');

                $wheelLog['email'] = $request->input('email');
            }

            if(empty($request->input('fullname'))) {
                response()->error($displayText['lang_input_missing']);
            }
            $wheelLog['fullname'] = $request->input('fullname');

            if(empty($request->input('phone'))) {
                response()->error($displayText['lang_input_missing']);
            }

            $limitData['phone'] = $request->input('phone');

            $wheelLog['phone'] = $request->input('phone');

            if(!WheelHelper::validateLimit($limitData, $wheel)) {
                response()->error($displayText['lang_ace_limit_reached']);
            }

            $available_slices = array();

            foreach(range(1,12) as $i) {

                if($wheel->{"slice$i"."_infinite"})
                {
                    foreach(range(1,$wheel->{"slice$i".'_percent'}) as $i_win_m)
                    {
                        $available_slices[] = $i;
                    }
                }
                else if ($wheel->{"slice$i"."_qty"} > 0) {

                    if ($wheel->{"slice$i"."_qty"} > WheelLog::count(Qr::set('wheel_id', $wheel->id)->where('slice_number', $i)))
                    {
                        foreach(range(1, $wheel->{"slice$i".'_percent'}) as $i_win_m)
                        {
                            $available_slices[] = $i;
                        }
                    }
                }

            }

            if(empty($available_slices)) {
                response()->error($displayText['lang_no_spins']);
            }

            $wheel_slice_number = $available_slices[array_rand($available_slices)];

            $result['wheel_deg_end'] = (360*(ceil($wheel->wheel_spin_time/3))) + (360 - (($wheel_slice_number * 30) - 30)) + rand(-5,5);

            $result['wheel_time_end'] = $wheel->wheel_spin_time * 1000;

            $couponCode = $wheel->{"slice$wheel_slice_number"."_value"};

            //Ghi lại kết quả quay
            $wheelLog['wheel_id']       = $wheel->id;
            $wheelLog['wheel_name']     = $wheel->name;
            $wheelLog['coupon_code']    = $couponCode;
            $wheelLog['ip']             = $ip;
            $wheelLog['device_id']       = $deviceId;
            $wheelLog['slice_number']   = $wheel_slice_number;
            $wheelLog['slice_label']    = $wheel->{'slice'.$wheel_slice_number.'_label'};
            $wheelLog['user_cookie']    = $_COOKIE['couponWheel_session'];
            $wheelLog['referer']        = $_SERVER['HTTP_REFERER'];
            $wheelLog['timestamp']      = time();
            $wheelLog['wheel_deg_end']    = $result['wheel_deg_end'];
            $wheelLog['wheel_time_end']   = $result['wheel_time_end'];

            $wheel_run_id = WheelLog::insert($wheelLog);

            if(is_skd_error($wheel_run_id)) {
                response()->error($wheel_run_id);
            }

            CacheHandler::delete('coupon_wheel_log_is_read');

            model('wheels')->increment('popup_spin');

            setcookie("couponWheel$wheel->id"."_seen", $wheel->seen_key, strtotime("+$wheel->reset_counter_days days"),'/');

            $template_vars['fullname']          = $wheelLog['fullname'];

            $template_vars['phone']             = $wheelLog['phone'];

            if(isset($wheelLog['email'])) {

                $template_vars['email']         = $wheelLog['email'];
            }

            $template_vars['slice']             = strip_tags($wheel->{"slice$wheel_slice_number"."_label"});

            $template_vars['couponcode']        = '<span class="couponwheel_coupon_code">'.$couponCode.'</span>';

            if (empty($couponCode))
            {
                $result['stage2_heading_text']    = $displayText['popup_lose_heading_text'];

                $result['stage2_main_text']       = do_shortcode(template_parser($displayText['popup_lose_main_text'], $template_vars));
            }
            else {

                $result['stage2_heading_text'] = do_shortcode(template_parser($displayText['popup_win_heading_text'],$template_vars));

                $result['stage2_main_text'] = do_shortcode(template_parser($displayText['popup_win_main_text'],$template_vars));
            }

            $result['data'] = '';

            response()->success(trans('ajax.save.success'), $result);
        }

        response()->error(trans('ajax.save.error'));
    }
    static function event(Request $request, $model): void
    {
        $id     = (int)$request->input('id');

        $wheel  = Wheel::get(Qr::set($id));

        if (!have_posts($wheel)) return;

        if ($request->input('code') == 'show_popup') {
            model('wheels')->increment('popup_impressions');
        }
    }
}
Ajax::client('CouponWheelAjax::renderPopup');
Ajax::client('CouponWheelAjax::run');
Ajax::client('CouponWheelAjax::event');

class AdminCouponWheelLogAjax {
    #[NoReturn]
    static function load(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $page    = $request->input('page');

            $page   = (is_null($page) || empty($page)) ? 1 : (int)$page;

            $limit  = $request->input('limit');

            $limit   = (is_null($limit) || empty($limit)) ? 10 : (int)$limit;

            $recordsTotal   = $request->input('recordsTotal');

            $args = Qr::set();

            $keyword = Str::clear($request->input('name'));
            if(!empty($keyword)) {
                $args->where('fullname', 'like', '%'.$keyword.'%');
            }

            $phone = Str::clear($request->input('phone'));
            if(!empty($phone)) {
                $args->where('phone', 'like', '%'.$phone.'%');
            }

            $time = Str::clear($request->input('time'));
            if(!empty($time)) {
                $time = explode(' - ', $time);
                if(have_posts($time) && count($time) == 2) {
                    $timeStart = date('Y-m-d', strtotime($time[0])).' 00:00:00';
                    $timeEnd   = date('Y-m-d', strtotime($time[1])).' 23:59:59';
                    $args->where('created', '>=', $timeStart);
                    $args->where('created', '<=', $timeEnd);
                }
            }
            /**
             * @since 7.0.0
             */
            $args = apply_filters('admin_wheelLog_controllers_index_args_before_count', $args);

            if(!is_numeric($recordsTotal)) {
                $recordsTotal = apply_filters('admin_wheelLog_controllers_index_count', WheelLog::count($args), $args);
            }


            # [List data]
            $args->limit($limit)
                ->offset(($page - 1)*$limit)
                ->orderBy('order')
                ->orderBy('created', 'desc');

            $args = apply_filters('admin_wheelLog_controllers_index_args', $args);

            $objects = apply_filters('admin_wheelLog_controllers_index_objects', WheelLog::gets($args), $args);

            $args = [
                'items' => $objects,
                'table' => 'wheelLog',
                'model' => model('wheels_log'),
                'module'=> 'wheelLog',
            ];

            $table = new AdminWheelLogTable($args);
            $table->get_columns();
            ob_start();
            $table->display_rows_or_message();
            $html = ob_get_contents();
            ob_end_clean();

            $buttonsBulkAction = apply_filters('table_wheelLog_bulk_action_buttons', []);

            $bulkAction = Admin::partial('include/table/header/bulk-action-buttons', [
                'actionList' => $buttonsBulkAction
            ]);

            $result['data'] = [
                'html'          => base64_encode($html),
                'bulkAction'    => base64_encode($bulkAction),
            ];
            $result['pagination']   = [
                'limit' => $limit,
                'total' => $recordsTotal,
                'page'  => (int)$page,
            ];

            response()->success(trans('ajax.load.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }
    #[NoReturn]
    static function isRead(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            WheelLog::where('is_read', 0)->update(['is_read' => 1]);

            CacheHandler::delete('coupon_wheel_log_is_read');

            response()->success(trans('ajax.update.success'));
        }

        response()->error(trans('ajax.update.error'));
    }
    #[NoReturn]
    static function delete(Request $request, $model): void {

        if($request->isMethod('post')) {

            $id     = (int)$request->input('data');

            $wheelLog  = WheelLog::get($id);

            if(!have_posts($wheelLog)) {
                response()->error(trans('Kết quả lượt chơi này không tồn tại trên hệ thống'));
            }

            if(WheelLog::delete($id)) {

                response()->success(trans('ajax.delete.success'));
            }
        }

        response()->error(trans('ajax.delete.error'));
    }
}
Ajax::admin('AdminCouponWheelLogAjax::load');
Ajax::admin('AdminCouponWheelLogAjax::isRead');
Ajax::admin('AdminCouponWheelLogAjax::delete');