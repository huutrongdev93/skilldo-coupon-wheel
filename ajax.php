<?php
class AdminCouponWheelAjax {
    static function loadRun($ci, $model): void
    {
        $result['status'] 	= 'error';

        $result['message'] 	= 'Load dữ liệu không thành công';

        if(Request::post()) {
            $args   = Qr::set('status', 'run');
            $object = Wheel::get($args);
            $result['data'] =  '';
            if(have_posts($object)) {
                $result['data'] = Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel/item', ['item' => $object], true);
            }
            $result['data']         = base64_encode($result['data']);
            $result['status'] 	    = 'success';
            $result['message'] 	    = 'Load dữ liệu thành công';
        }

        echo json_encode($result);
    }
    static function loadOrder($ci, $model): void
    {

        $result['status'] 	= 'error';

        $result['message'] 	= 'Load dữ liệu không thành công';

        if(Request::post()) {

            $page   = Request::post('page');

            $limit  = Request::post('limit');

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
                    $result['data'] .= Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel/item', ['item' => $object], true);
                }
            }

            $result['data']         = base64_encode($result['data']);
            $result['pagination']   = base64_encode($pagination->frontend());
            $result['status'] 	    = 'success';
            $result['message'] 	    = 'Load dữ liệu thành công';
        }

        echo json_encode($result);
    }
    static function add($ci, $model): void
    {
        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if(Request::post()) {

            $wheel = [];

            if(empty(Request::post('name'))) {
                $result['message'] = 'Tên chương trình không được để trống';
                echo json_encode($result);
                return;
            }
            $wheel['name'] = Request::post('name');

            if(!is_numeric(Request::post('is_live'))) {
                $result['message'] = 'Lưu dữ liệu không thành công.';
                echo json_encode($result);
                return;
            }
            $wheel['is_live'] = Request::post('is_live');

            if(!is_numeric(Request::post('max_spins_per_user'))) {
                $result['message'] = 'Số lần quay tối đa không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['max_spins_per_user'] = Request::post('max_spins_per_user');

            if(!is_numeric(Request::post('reset_counter_days'))) {
                $result['message'] = 'Thời gian lượt chơi tiếp theo không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['reset_counter_days'] = Request::post('reset_counter_days');

            if(!is_numeric(Request::post('show_popup_after'))) {
                $result['message'] = 'Thời gian tự hiển thị sau khi khách hàng truy cập không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['show_popup_after'] = Request::post('show_popup_after');

            if(!is_numeric(Request::post('wheel_spin_time'))) {
                $result['message'] = 'Thời gian quay của vòng quay không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['wheel_spin_time'] = Request::post('wheel_spin_time');

            $award = Request::post('award');

            foreach (range(1,12) as $i) {
                if(empty($award[$i]['label'])) {
                    $result['message'] = 'Tên phần thưởng số '.$i.' không được để trống';
                    echo json_encode($result);
                    return;
                }
                if(!isset($award[$i]['value'])) {
                    $result['message'] = 'Giá trị phần thưởng số '.$i.' không được để trống';
                    echo json_encode($result);
                    return;
                }
                if(!is_numeric($award[$i]['qty'])) {
                    $result['message'] = 'Số lần trúng phần thưởng số '.$i.' không đúng định dạng';
                    echo json_encode($result);
                    return;
                }
                if(!is_numeric($award[$i]['percent'])) {
                    $result['message'] = 'Tỷ lệ trúng phần thưởng số '.$i.' không đúng định dạng';
                    echo json_encode($result);
                    return;
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

            if(empty(Request::post('background'))) {
                $result['message'] = 'Bạn chưa chọn màu nền cho popup';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['background'] = Request::post('background');

            if(empty(Request::post('style'))) {
                $result['message'] = 'Bạn chưa chọn Kiểu vòng quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['style'] = Request::post('style');

            if(empty(Request::post('frame'))) {
                $result['message'] = 'Bạn chưa chọn Kiểu khung vòng quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['frame'] = Request::post('frame');

            if(empty(Request::post('center'))) {
                $result['message'] = 'Bạn chưa chọn Kiểu tâm vòng quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['center'] = Request::post('center');

            if(empty(Request::post('textColor'))) {
                $result['message'] = 'Bạn chưa chọn màu chữ popup';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['textColor'] = Request::post('textColor');

            if(empty(Request::post('btnRunBgColor'))) {
                $result['message'] = 'Bạn chưa chọn màu nền nút quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['btnRunBgColor'] = Request::post('btnRunBgColor');

            if(empty(Request::post('btnRunTxtColor'))) {
                $result['message'] = 'Bạn chưa chọn màu chữ nút quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['btnRunTxtColor'] = Request::post('btnRunTxtColor');

            $wheelTemplate['triggerOpen'] = (empty(Request::post('triggerOpen')))  ? 0 : 1;

            if(Request::post('triggerStyle') !== null) {
                $wheelTemplate['triggerStyle'] = Request::post('triggerStyle');
            }
            if(Request::post('triggerEffect') !== null) {
                $wheelTemplate['triggerEffect'] = Request::post('triggerEffect');
            }
            if(Request::post('triggerBg') !== null) {
                $wheelTemplate['triggerBg'] = Request::post('triggerBg');
            }
            if(Request::post('triggerIcon') !== null) {
                $wheelTemplate['triggerIcon'] = FileHandler::handlingUrl(Request::post('triggerIcon'));
            }

            //Cấu hình văn bản
            $wheelText = [];

            $displayText = Request::post('displayText');

            $languages = Language::list();

            $hasMulti = Language::hasMulti();

            foreach ($languages as $langKey => $language) {

                if(empty($displayText[$langKey]['popup_heading_text'])) {
                    $result['message'] = 'Tiêu đề lớn'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_heading_text'] = $displayText[$langKey]['popup_heading_text'];

                if(empty($displayText[$langKey]['popup_rules_text'])) {
                    $result['message'] = 'Mô tả luật quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_rules_text'] = $displayText[$langKey]['popup_rules_text'];

                if(empty($displayText[$langKey]['popup_main_text'])) {
                    $result['message'] = 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_main_text'] = $displayText[$langKey]['popup_main_text'];

                if(empty($displayText[$langKey]['popup_win_heading_text'])) {
                    $result['message'] = 'Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_win_heading_text'] = $displayText[$langKey]['popup_win_heading_text'];

                if(empty($displayText[$langKey]['popup_win_main_text'])) {
                    $result['message'] = 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_win_main_text'] = $displayText[$langKey]['popup_win_main_text'];

                if(empty($displayText[$langKey]['popup_lose_heading_text'])) {
                    $result['message'] = 'Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_lose_heading_text'] = $displayText[$langKey]['popup_lose_heading_text'];

                if(empty($displayText[$langKey]['popup_lose_main_text'])) {
                    $result['message'] = 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_lose_main_text'] = $displayText[$langKey]['popup_lose_main_text'];

                if(empty($displayText[$langKey]['lang_enter_your_full_name'])) {
                    $result['message'] = 'Ô nhập họ tên'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_enter_your_full_name'] = $displayText[$langKey]['lang_enter_your_full_name'];

                if(empty($displayText[$langKey]['lang_enter_your_email'])) {
                    $result['message'] = 'Ô nhập email'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_enter_your_email'] = $displayText[$langKey]['lang_enter_your_email'];

                if(empty($displayText[$langKey]['lang_enter_phone_number'])) {
                    $result['message'] = 'Ô nhập số điện thoại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_enter_phone_number'] = $displayText[$langKey]['lang_enter_phone_number'];

                if(empty($displayText[$langKey]['lang_spin_button'])) {
                    $result['message'] = 'Chữ nút quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_spin_button'] = $displayText[$langKey]['lang_spin_button'];

                if(empty($displayText[$langKey]['lang_continue_button'])) {
                    $result['message'] = 'Chữ nút tiếp tục'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_continue_button'] = $displayText[$langKey]['lang_continue_button'];

                if(empty($displayText[$langKey]['lang_spin_again'])) {
                    $result['message'] = 'Chữ nút thử lại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_spin_again'] = $displayText[$langKey]['lang_spin_again'];

                if(empty($displayText[$langKey]['lang_input_missing'])) {
                    $result['message'] = 'Thông báo khi điền không đủ thông tin'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_input_missing'] = $displayText[$langKey]['lang_input_missing'];

                if(empty($displayText[$langKey]['lang_no_spins'])) {
                    $result['message'] = 'Thông báo khi không còn quà'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_no_spins'] = $displayText[$langKey]['lang_no_spins'];

                if(empty($displayText[$langKey]['lang_ace_email_check'])) {
                    $result['message'] = 'Thông báo khi trường email không hợp lệ'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_ace_email_check'] = $displayText[$langKey]['lang_ace_email_check'];

                if(empty($displayText[$langKey]['lang_ace_limit_reached'])) {
                    $result['message'] = 'Thông báo khi hết lượt quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_ace_limit_reached'] = $displayText[$langKey]['lang_ace_limit_reached'];

                if(empty($displayText[$langKey]['lang_close'])) {
                    $result['message'] = 'Xác nhận khi đóng vòng quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_close'] = $displayText[$langKey]['lang_close'];
            }

            $wheel['require_email'] = (empty(Request::post('require_email')))  ? 0 : 1;

            $wheel['require_user'] = (empty(Request::post('require_user')))  ? 0 : 1;

            $wheel['status'] = 'pending';

            $id = Wheel::insert($wheel);

            if(!is_skd_error($id)) {

                Wheel::updateMeta($id, 'displayTemplate', $wheelTemplate);

                Wheel::updateMeta($id, 'displayText', $wheelText);

                $result['status'] = 'success';

                $result['message'] = 'Thêm dữ liệu thành công!';
            }
        }

        echo json_encode($result);
    }
    static function save($ci, $model): void
    {
        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if(Request::post()) {

            $id         = (int)Request::post('id');

            $object  = Wheel::get($id);

            if(!have_posts($object)) {
                $result['message'] = 'Chương trình vòng quay này không tồn tại trên hệ thống';
                echo json_encode($result);
                return;
            }

            $wheel = [];

            if(empty(Request::post('name'))) {
                $result['message'] = 'Tên chương trình không được để trống';
                echo json_encode($result);
                return;
            }
            $wheel['name'] = Request::post('name');

            if(!is_numeric(Request::post('is_live'))) {
                $result['message'] = 'Lưu dữ liệu không thành công.';
                echo json_encode($result);
                return;
            }
            $wheel['is_live'] = Request::post('is_live');

            if(!is_numeric(Request::post('max_spins_per_user'))) {
                $result['message'] = 'Số lần quay tối đa không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['max_spins_per_user'] = Request::post('max_spins_per_user');

            if(!is_numeric(Request::post('reset_counter_days'))) {
                $result['message'] = 'Thời gian lượt chơi tiếp theo không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['reset_counter_days'] = Request::post('reset_counter_days');

            if(!is_numeric(Request::post('show_popup_after'))) {
                $result['message'] = 'Thời gian tự hiển thị sau khi khách hàng truy cập không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['show_popup_after'] = Request::post('show_popup_after');

            if(!is_numeric(Request::post('wheel_spin_time'))) {
                $result['message'] = 'Thời gian quay của vòng quay không đúng định dạng';
                echo json_encode($result);
                return;
            }
            $wheel['wheel_spin_time'] = Request::post('wheel_spin_time');

            $award = Request::post('award');

            foreach (range(1,12) as $i) {
                if(empty($award[$i]['label'])) {
                    $result['message'] = 'Tên phần thưởng số '.$i.' không được để trống';
                    echo json_encode($result);
                    return;
                }
                if(!isset($award[$i]['value'])) {
                    $result['message'] = 'Giá trị phần thưởng số '.$i.' không được để trống';
                    echo json_encode($result);
                    return;
                }
                if(!is_numeric($award[$i]['qty'])) {
                    $result['message'] = 'Số lần trúng phần thưởng số '.$i.' không đúng định dạng';
                    echo json_encode($result);
                    return;
                }
                if(!is_numeric($award[$i]['percent'])) {
                    $result['message'] = 'Tỷ lệ trúng phần thưởng số '.$i.' không đúng định dạng';
                    echo json_encode($result);
                    return;
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

            if(empty(Request::post('background'))) {
                $result['message'] = 'Bạn chưa chọn màu nền cho popup';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['background'] = Request::post('background');

            if(empty(Request::post('style'))) {
                $result['message'] = 'Bạn chưa chọn Kiểu vòng quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['style'] = Request::post('style');

            if(empty(Request::post('frame'))) {
                $result['message'] = 'Bạn chưa chọn Kiểu khung vòng quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['frame'] = Request::post('frame');

            if(empty(Request::post('center'))) {
                $result['message'] = 'Bạn chưa chọn Kiểu tâm vòng quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['center'] = Request::post('center');

            if(empty(Request::post('textColor'))) {
                $result['message'] = 'Bạn chưa chọn màu chữ popup';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['textColor'] = Request::post('textColor');

            if(empty(Request::post('btnRunBgColor'))) {
                $result['message'] = 'Bạn chưa chọn màu nền nút quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['btnRunBgColor'] = Request::post('btnRunBgColor');

            if(empty(Request::post('btnRunTxtColor'))) {
                $result['message'] = 'Bạn chưa chọn màu chữ nút quay';
                echo json_encode($result);
                return;
            }
            $wheelTemplate['btnRunTxtColor'] = Request::post('btnRunTxtColor');


            $wheelTemplate['triggerOpen'] = (empty(Request::post('triggerOpen')))  ? 0 : 1;

            if(Request::post('triggerStyle') !== null) {
                $wheelTemplate['triggerStyle'] = Request::post('triggerStyle');
            }
            if(Request::post('triggerEffect') !== null) {
                $wheelTemplate['triggerEffect'] = Request::post('triggerEffect');
            }
            if(Request::post('triggerBg') !== null) {
                $wheelTemplate['triggerBg'] = Request::post('triggerBg');
            }
            if(Request::post('triggerIcon') !== null) {
                $wheelTemplate['triggerIcon'] = FileHandler::handlingUrl(Request::post('triggerIcon'));
            }

            //Cấu hình văn bản
            $wheelText = [];

            $displayText = Request::post('displayText');

            $languages = Language::list();

            $hasMulti = Language::hasMulti();

            foreach ($languages as $langKey => $language) {

                if(empty($displayText[$langKey]['popup_heading_text'])) {
                    $result['message'] = 'Tiêu đề lớn'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_heading_text'] = $displayText[$langKey]['popup_heading_text'];

                if(empty($displayText[$langKey]['popup_rules_text'])) {
                    $result['message'] = 'Mô tả luật quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_rules_text'] = $displayText[$langKey]['popup_rules_text'];

                if(empty($displayText[$langKey]['popup_main_text'])) {
                    $result['message'] = 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_main_text'] = $displayText[$langKey]['popup_main_text'];

                if(empty($displayText[$langKey]['popup_win_heading_text'])) {
                    $result['message'] = 'Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_win_heading_text'] = $displayText[$langKey]['popup_win_heading_text'];

                if(empty($displayText[$langKey]['popup_win_main_text'])) {
                    $result['message'] = 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_win_main_text'] = $displayText[$langKey]['popup_win_main_text'];

                if(empty($displayText[$langKey]['popup_lose_heading_text'])) {
                    $result['message'] = 'Tiêu đề'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_lose_heading_text'] = $displayText[$langKey]['popup_lose_heading_text'];

                if(empty($displayText[$langKey]['popup_lose_main_text'])) {
                    $result['message'] = 'Mô tả'.($hasMulti ? ' ('.$language['label'].')' : '').' khi khách quay không nhận được quà tặng không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['popup_lose_main_text'] = $displayText[$langKey]['popup_lose_main_text'];

                if(empty($displayText[$langKey]['lang_enter_your_full_name'])) {
                    $result['message'] = 'Ô nhập họ tên'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_enter_your_full_name'] = $displayText[$langKey]['lang_enter_your_full_name'];

                if(empty($displayText[$langKey]['lang_enter_your_email'])) {
                    $result['message'] = 'Ô nhập email'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_enter_your_email'] = $displayText[$langKey]['lang_enter_your_email'];

                if(empty($displayText[$langKey]['lang_enter_phone_number'])) {
                    $result['message'] = 'Ô nhập số điện thoại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_enter_phone_number'] = $displayText[$langKey]['lang_enter_phone_number'];

                if(empty($displayText[$langKey]['lang_spin_button'])) {
                    $result['message'] = 'Chữ nút quay'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_spin_button'] = $displayText[$langKey]['lang_spin_button'];

                if(empty($displayText[$langKey]['lang_continue_button'])) {
                    $result['message'] = 'Chữ nút tiếp tục'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_continue_button'] = $displayText[$langKey]['lang_continue_button'];

                if(empty($displayText[$langKey]['lang_spin_again'])) {
                    $result['message'] = 'Chữ nút thử lại'.($hasMulti ? ' ('.$language['label'].')' : '').' không được bỏ trống';
                    echo json_encode($result);
                    return;
                }
                $wheelText[$langKey]['lang_spin_again'] = $displayText[$langKey]['lang_spin_again'];
            }

            $wheel['require_email'] = (empty(Request::post('require_email')))  ? 0 : 1;

            $wheel['require_user'] = (empty(Request::post('require_user')))  ? 0 : 1;

            $wheel['id'] = $object->id;

            $id = Wheel::insert($wheel, $object);

            if(!is_skd_error($id)) {

                CacheHandler::delete('coupon_wheel_popup_'.md5($object->id));

                CacheHandler::delete('coupon_wheel_popup_run');

                Wheel::updateMeta($id, 'displayTemplate', $wheelTemplate);

                Wheel::updateMeta($id, 'displayText', $wheelText);

                $result['status'] = 'success';

                $result['message'] = 'Cập nhật dữ liệu thành công!';
            }
        }

        echo json_encode($result);
    }
    static function status($ci, $model): void {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if(Request::post()) {

            $id     = (int)Request::post('id');

            $wheel  = Wheel::get($id);

            if(!have_posts($wheel)) {
                $result['message'] = 'Chương trình vòng quay này không tồn tại trên hệ thống';
                echo json_encode($result);
                return;
            }

            $status = trim((string)Request::post('status'));

            if(empty($status)) {
                $result['message'] = 'Trạng thái không được để trống';
                echo json_encode($result);
                return;
            }

            if(empty(WheelHelper::status($status))) {
                $result['message'] = 'Trạng thái không đúng định dạng';
                echo json_encode($result);
                return;
            }

            if($wheel->status == $status) {
                $result['message'] = 'Trạng thái chương trình không thay đổi';
                echo json_encode($result);
                return;
            }

            $id = Wheel::insert([
                'id' => $wheel->id,
                'status' => $status
            ], $wheel);

            if(!is_skd_error($id)) {
                CacheHandler::delete('coupon_wheel_popup_'.md5($wheel->id));
                CacheHandler::delete('coupon_wheel_popup_run');
                $result['status'] = 'success';
                $result['message'] = 'Cập nhật dữ liệu thành công!';
            }
        }

        echo json_encode($result);
    }
    static function delete($ci, $model): void {

        $result['status'] = 'error';

        $result['message'] = 'Xóa liệu không thành công.';

        if(Request::post()) {

            $id     = (int)Request::post('data');

            $wheel  = Wheel::get($id);

            if(!have_posts($wheel)) {
                $result['message'] = 'vòng quay này không tồn tại trên hệ thống';
                echo json_encode($result);
                return;
            }

            if(Wheel::delete($id)) {

                CacheHandler::delete('coupon_wheel_popup_'.md5($id));
                CacheHandler::delete('coupon_wheel_popup_run');
                $result['status'] = 'success';
                $result['message'] = 'Xóa dữ liệu thành công!';
            }
        }

        echo json_encode($result);
    }
}
Ajax::admin('AdminCouponWheelAjax::loadRun');
Ajax::admin('AdminCouponWheelAjax::loadOrder');
Ajax::admin('AdminCouponWheelAjax::add');
Ajax::admin('AdminCouponWheelAjax::save');
Ajax::admin('AdminCouponWheelAjax::status');
Ajax::admin('AdminCouponWheelAjax::delete');

class CouponWheelAjax {
    static function renderPopup($ci, $model): void
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
    static function run($ci, $model): void {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if(Request::post()) {

            $_COOKIE['couponWheel_session'] = WheelHelper::cookies();

            $id = (int)Request::post('wheel_hash');

            $wheel = Wheel::get(Qr::set($id)->where('status', 'run'));

            if(!have_posts($wheel)) {
                $result['message'] = 'Chương trình vòng quay mai mắn đã tạm dừng hoặc không còn tồn tại.';
                echo json_encode($result);
                return;
            }

            if($wheel->is_live == 1 && Device::isMobile()) {
                $result['message'] = 'vòng quay không tồn tại.';
                echo json_encode($result);
                return;
            }

            if($wheel->is_live == 2 && !Device::isMobile()) {
                $result['message'] = 'vòng quay không tồn tại.';
                echo json_encode($result);
                return;
            }

            if($wheel->require_user == 1 && !Auth::check()) {
                $result['message'] = 'Chương trình vòng quay mai mắn này chỉ áp dụng cho khách hàng đã đăng ký thành viên.';
                echo json_encode($result);
                return;
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
                $result['message'] = 'Spin error, cannot set cookies. Please reload webpage.';
                echo json_encode($result);
                return;
            }

            if($wheel->require_email) {
                if(empty(Request::post('email'))) {
                    $result['message'] = $displayText['lang_input_missing'];
                    echo json_encode($result);
                    return;
                }
                if (!WheelHelper::validateEmail(Request::post('email'))) {
                    $result['message'] = $displayText['lang_ace_email_check'];
                    echo json_encode($result);
                    return;
                }
                $limitData['email'] = Request::post('email');
                $wheelLog['email'] = Request::post('email');
            }

            if(empty(Request::post('fullname'))) {
                $result['message'] = $displayText['lang_input_missing'];
                echo json_encode($result);
                return;
            }
            $wheelLog['fullname'] = Request::post('fullname');

            if(empty(Request::post('phone'))) {
                $result['message'] = $displayText['lang_input_missing'];
                echo json_encode($result);
                return;
            }
            $limitData['phone'] = Request::post('phone');
            $wheelLog['phone'] = Request::post('phone');

            if(!WheelHelper::validateLimit($limitData, $wheel)) {
                $result['message'] = $displayText['lang_ace_limit_reached'];
                echo json_encode($result);
                return;
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
                $result['message'] = $displayText['lang_no_spins'];
                echo json_encode($result);
                return;
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
                $result['message'] = $wheel_run_id->errors[0]->error;
                echo json_encode($result);
                return;
            }

            CacheHandler::delete('coupon_wheel_log_is_read');

            model()::table('wheels')->increment('popup_spin');

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

                //$result['on_win_url'] = $wheel->on_win_url;

                $result['stage2_heading_text'] = do_shortcode(template_parser($displayText['popup_win_heading_text'],$template_vars));

                $result['stage2_main_text'] = do_shortcode(template_parser($displayText['popup_win_main_text'],$template_vars));
            }

            $result['status'] = 'success';

            $result['message'] = 'Thành công.';
        }

        echo json_encode($result);

        return;
    }
    static function event($ci, $model): void
    {
        $id     = (int)Request::post('id');

        $wheel  = Wheel::get(Qr::set($id));

        if (!have_posts($wheel)) return;

        if (Request::post('code') == 'show_popup') {
            model()::table('wheels')->increment('popup_impressions');
        }
    }
}
Ajax::client('CouponWheelAjax::renderPopup');
Ajax::client('CouponWheelAjax::run');
Ajax::client('CouponWheelAjax::event');

class AdminCouponWheelLogAjax {
    static function load($ci, $model): void
    {

        $result['status'] 	= 'error';

        $result['message'] 	= 'Load dữ liệu không thành công';

        if(Request::post()) {

            $page   = Request::post('page');

            $limit  = Request::post('limit');

            $args   = Qr::set();

            $keyword = Str::clear(Request::post('name'));
            if(!empty($keyword)) {
                $args->where('fullname', 'like', '%'.$keyword.'%');
            }

            $phone = Str::clear(Request::post('phone'));
            if(!empty($phone)) {
                $args->where('phone', 'like', '%'.$phone.'%');
            }

            $time = Str::clear(Request::post('time'));
            if(!empty($time)) {
                $time = explode(' - ', $time);
                if(have_posts($time) && count($time) == 2) {
                    $timeStart = date('Y-m-d', strtotime($time[0])).' 00:00:00';
                    $timeEnd   = date('Y-m-d', strtotime($time[1])).' 23:59:59';
                    $args->where('created', '>=', $timeStart);
                    $args->where('created', '<=', $timeEnd);
                }
            }

            $total  = WheelLog::count($args);

            # [Pagination]
            $url = '#{page}';

            $pagination = pagination($total, $url, $limit, $page);

            # [Data]
            $args->limit($limit)->offset($pagination->offset())->orderByDesc('created');

            $objects = WheelLog::gets($args);

            $result['data'] = '';

            if(have_posts($objects)) {
                foreach ($objects as $object) {
                    $result['data'] .= Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel-log/item', ['item' => $object], true);
                }
            }

            $result['data']         = base64_encode($result['data']);
            $result['pagination']   = base64_encode($pagination->frontend());
            $result['status'] 	    = 'success';
            $result['message'] 	    = 'Load dữ liệu thành công';
        }

        echo json_encode($result);
    }
    static function isRead($ci, $model): void
    {
        $result['status'] 	= 'error';
        $result['message'] 	= 'Load dữ liệu không thành công';
        if(Request::post()) {
            WheelLog::update(['is_read' => 1], Qr::set('is_read', 0));
            CacheHandler::delete('coupon_wheel_log_is_read');
            $result['status'] 	    = 'success';
            $result['message'] 	    = 'Load dữ liệu thành công';
        }

        echo json_encode($result);
    }
    static function delete($ci, $model): void {

        $result['status'] = 'error';

        $result['message'] = 'Xóa liệu không thành công.';

        if(Request::post()) {

            $id     = (int)Request::post('data');

            $wheelLog  = WheelLog::get($id);

            if(!have_posts($wheelLog)) {
                $result['message'] = 'Kết quả lượt chơi này không tồn tại trên hệ thống';
                echo json_encode($result);
                return;
            }

            if(WheelLog::delete($id)) {

                $result['status'] = 'success';

                $result['message'] = 'Xóa dữ liệu thành công!';
            }
        }

        echo json_encode($result);
    }
}
Ajax::admin('AdminCouponWheelLogAjax::load');
Ajax::admin('AdminCouponWheelLogAjax::isRead');
Ajax::admin('AdminCouponWheelLogAjax::delete');