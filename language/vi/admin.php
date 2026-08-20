<?php
return [

    // -------------------------------------------------------------------------
    // Chung
    // -------------------------------------------------------------------------
    'title'       => 'Vòng Quay May Mắn',
    'btn.save'    => 'Lưu chương trình',
    'btn.back'    => 'Quay lại',

    // -------------------------------------------------------------------------
    // Trang danh sách chương trình
    // -------------------------------------------------------------------------
    'program.page_title'        => 'Chương trình vòng quay',
    'program.tab.base'          => 'Cơ bản',
    'program.tab.slices'        => 'Phần thưởng',
    'program.tab.display'       => 'Giao diện',
    'program.tab.text'          => 'Văn bản',
    'program.add_title'         => 'Thêm chương trình mới',
    'program.edit_title'        => 'Chỉnh sửa chương trình',

    // Cột bảng
    'program.col.name'          => 'Tên chương trình',
    'program.col.time'          => 'Thời gian',
    'program.col.status'        => 'Trạng thái',
    'program.col.action'        => 'Hành động',
    'program.status.running'    => 'Đang chạy',
    'program.status.paused'     => 'Tạm dừng',

    // Nút hành động
    'program.btn.edit'          => 'Chỉnh sửa',
    'program.btn.clone'         => 'Nhân bản',
    'program.btn.logs'          => 'Xem kết quả quay',
    'program.btn.delete_confirm' => 'Xóa chương trình ":name"?',

    // -------------------------------------------------------------------------
    // Form - Tab Cơ bản
    // -------------------------------------------------------------------------
    'form.name'             => 'Tên chương trình',
    'form.status'           => 'Trạng thái',
    'form.status.note'      => 'Chỉ 1 chương trình được chạy cùng lúc.',
    'form.status.paused'    => 'Tạm dừng',
    'form.status.running'   => 'Đang chạy',
    'form.start_at'         => 'Bắt đầu',
    'form.end_at'           => 'Kết thúc',

    'form.require_login'       => 'Yêu cầu đăng nhập',
    'form.require_login.no'    => 'Không — Ai cũng quay được',
    'form.require_login.yes'   => 'Có — Phải đăng nhập',
    'form.spin_limit'          => 'Giới hạn lượt quay',
    'form.spin_limit.forever'  => '1 lần mãi mãi / user',
    'form.spin_limit.daily'    => '1 lần mỗi ngày',
    'form.spin_limit.none'     => 'Không giới hạn',
    'form.contact_limit'       => 'Giới hạn trùng lặp theo',
    'form.contact_limit.note'  => 'Mỗi email / SĐT chỉ được quay 1 lần (áp dụng cho mọi mức giới hạn lượt quay)',
    'form.contact_limit.none'  => 'Không giới hạn',
    'form.contact_limit.email' => 'Email',
    'form.contact_limit.phone' => 'Số điện thoại',
    'form.contact_limit.both'  => 'Email hoặc Số điện thoại',
    'form.show_email'          => 'Hiển thị trường Email',
    'form.show_email.note'     => 'Bỏ chọn để ẩn trường email khỏi form',
    'form.show_phone'          => 'Hiển thị trường Số điện thoại',
    'form.show_phone.note'     => 'Bỏ chọn để ẩn trường số điện thoại khỏi form',

    // Toggle status (table)
    'program.toggle.success'   => 'Đã cập nhật trạng thái.',
    'program.toggle.error'     => 'Không thể cập nhật trạng thái.',

    // -------------------------------------------------------------------------
    // Form - Tab Phần thưởng (Slices)
    // -------------------------------------------------------------------------
    'slice.box_title'           => 'Ô vòng quay',
    'slice.btn_add'             => 'Thêm ô',
    'slice.title_prefix'        => 'Ô #',
    'slice.field.name'          => 'Tên giải thưởng *',
    'slice.field.name_placeholder' => 'VD: Giảm 10%, Freeship, ...',
    'slice.field.difficulty'    => 'Độ khó trúng',
    'slice.field.prize_type'    => 'Loại phần thưởng',
    'slice.prize_type.text'     => 'Tự điền',
    'slice.prize_type.product'  => 'Sản phẩm',
    'slice.field.prize_text'    => 'Nội dung / Mã thưởng',
    'slice.field.prize_text_placeholder' => 'VD: GIAM10, Freeship toàn quốc...',
    'slice.field.product'       => 'Sản phẩm',
    'slice.field.quantity'      => 'Số lượng trúng',
    'slice.field.bg_color'      => 'Màu nền',
    'slice.field.text_color'    => 'Màu chữ',
    'slice.quantity.note'       => 'Khi điền số lượng trúng bằng 0 tức là không giới hạn số lần trúng',

    // Xác suất
    'slice.prob.title'          => 'Xác suất ước tính',
    'slice.prob.empty'          => 'Thêm ô vòng quay để xem xác suất.',
    'slice.prob.warning'        => 'Không có ô nào có thể trúng!',

    // Độ khó
    'difficulty.very_easy'  => '🟢 Rất dễ trúng',
    'difficulty.easy'       => '🔵 Dễ trúng',
    'difficulty.normal'     => '🟡 Bình thường',
    'difficulty.hard'       => '🟠 Khó trúng',
    'difficulty.very_hard'  => '🔴 Rất khó trúng',
    'difficulty.never'      => '⛔ Không bao giờ trúng',

    // -------------------------------------------------------------------------
    // Form - Tab Giao diện
    // -------------------------------------------------------------------------
    'display.bg'                => 'Màu nền',
    'display.frame'             => 'Khung tâm vòng quay',
    'display.center'            => 'Tâm vòng quay',
    'display.trigger_icon_opts' => 'Tuỳ chọn icon',

    'form.trigger_type'                => 'Kiểu kích hoạt',
    'form.trigger_type.auto'           => 'Tự hiện sau X giây',
    'form.trigger_type.button'         => 'Floating button',
    'form.trigger_type.at_least_one'   => 'Phải chọn ít nhất 1 hình thức kích hoạt.',
    'form.trigger_delay'        => 'Hiện sau (giây)',
    'form.trigger_icon'         => 'Icon tự chọn',
    'form.trigger_icon.note'    => 'Nếu bạn muốn tự tuỳ chỉnh icon, vui lòng tải lên hình ảnh có định dạng png kích thước 240x240',
    'form.trigger_position'     => 'Vị trí hiển thị',
    'form.trigger_position.bottom_right' => 'Dưới - Phải',
    'form.trigger_position.bottom_left'  => 'Dưới - Trái',
    'form.trigger_position.top_right'    => 'Trên - Phải',
    'form.trigger_position.top_left'     => 'Trên - Trái',
    'form.trigger_effect'       => 'Hiệu ứng',
    'form.trigger_effect.none'  => 'Không sử dụng hiệu ứng',
    'form.trigger_effect.1'     => 'Hiệu ứng 1',
    'form.trigger_effect.2'     => 'Hiệu ứng 2',
    'form.trigger_effect.3'     => 'Hiệu ứng 3',
    'form.trigger_effect.4'     => 'Hiệu ứng 4',
    'form.trigger_bg'           => 'Màu nền',

    // -------------------------------------------------------------------------
    // Form - Tab Văn bản
    // -------------------------------------------------------------------------
    'text.group.base'           => 'Cơ bản',
    'text.group.win'            => 'Thông báo khi quay thành công',
    'text.group.lose'           => 'Thông báo khi quay thất bại',
    'text.field.heading'        => 'Tiêu đề',
    'text.field.description'    => 'Mô tả',

    // Default values
    'text.default.popupHeading'     => 'VÒNG QUAY MAY MẮN BẤM QUAY NHẬN NGAY QUÀ HOT',
    'text.default.popupDescription' => 'Đừng bỏ qua cơ hội nhận được nhiều ưu đãi hấp dẫn từ vòng xoay may mắn. Bạn có may mắn hôm nay? Hãy thử ngay!',
    'text.default.winHeading'       => 'CHÚC MỪNG BẠN ĐÃ QUAY TRÚNG THƯỞNG',
    'text.default.winDescription'   => 'Phần thưởng của bạn là',
    'text.default.loseHeading'      => 'Chúc bạn may mắn lần sau!',
    'text.default.loseDescription'  => 'Cảm ơn bạn đã tham gia. Hãy thử lại ở chương trình tiếp theo nhé!',

    // -------------------------------------------------------------------------
    // Trang thống kê log
    // -------------------------------------------------------------------------
    'log.page_title'        => 'Thống kê lượt quay',
    'log.col.program'       => 'Chương trình',
    'log.col.prize'         => 'Giải thưởng',
    'log.col.code'          => 'Mã / Sản phẩm',
    'log.col.email'         => 'Email / SĐT',
    'log.col.result'        => 'Kết quả',
    'log.col.ip'            => 'IP',
    'log.col.time'          => 'Thời gian quay',
    'log.result.win'        => '🎁 Trúng thưởng',
    'log.result.lose'       => '😔 Không trúng',
    'log.product_id_prefix' => 'Sản phẩm #',
    'log.filter.label'      => 'Đang xem kết quả của',
    'log.filter.clear'      => 'Xem tất cả',

    // -------------------------------------------------------------------------
    // Ajax / Thông báo
    // -------------------------------------------------------------------------
    'ajax.save.success'         => 'Lưu chương trình thành công!',
    'ajax.save.error.name'      => 'Tên chương trình không được để trống',
    'ajax.save.error.start_at'  => 'Thời gian bắt đầu không được để trống',
    'ajax.save.error.end_at'    => 'Thời gian kết thúc không được để trống',
    'ajax.save.error.slices'    => 'Vui lòng thêm ít nhất 2 ô vòng quay.',
    'ajax.save.error.slice_name' => 'Ô số :num chưa có tên giải thưởng.',
    'ajax.delete.success'       => 'Đã xóa chương trình.',
    'ajax.delete.error.id'      => 'ID không hợp lệ.',
    'ajax.clone.success'        => 'Nhân bản thành công!',
    'ajax.clone.error.id'       => 'ID không hợp lệ.',
    'ajax.clone.error.not_found' => 'Không tìm thấy chương trình.',
    'ajax.clone.name_suffix'    => ' (Bản sao)',

];
