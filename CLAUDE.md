# CLAUDE.md — Plugin coupon-wheel

File này giúp agent hiểu ngay cấu trúc plugin mà không cần scan lại source. Đọc TRƯỚC khi sửa bất kỳ file nào trong plugin.

## Plugin này là gì

**Marketing - Coupon Wheel** — vòng quay may mắn (lucky wheel) hiển thị popup ở frontend, khách nhập email/SĐT rồi quay để trúng **mã giảm giá (text)** hoặc **sản phẩm**. Admin tạo "chương trình" (program) gồm danh sách ô (slice), mỗi ô có **độ khó** (difficulty) quy ra trọng số weighted-random, và giới hạn số lượng giải.

- **Namespace PHP: `CouponWheel\*`**. Main class `CouponWheel` trong `index.php`. Version 3.0.0.
- **Không có ServiceProvider**, không alias toàn cục. `plugin.json` chỉ khai `autoload.alias` + PSR-4 cho `Enum`/`Supports`/`Notifications` (các thư mục còn lại theo convention loader).
- Phụ thuộc **mềm**, đều guard `class_exists`:
  - **sicommerce** (`Ecommerce\Models\Product`) — chỉ cần khi ô thưởng loại `product` (search sản phẩm admin + resolve tên/URL khi trúng).
  - **telegram** (`Telegram\Services\TelegramNotification`) — bắn thông báo mỗi lượt quay.

## Database

2 bảng, tạo trong `ActivatorService`, **uninstall DROP cả hai** (mất toàn bộ lịch sử quay — cân nhắc trước khi gỡ plugin).

| Bảng | Nội dung |
|---|---|
| `coupon_wheel_programs` | `name`, `start_at`/`end_at` (datetime), `slices` (longText), `settings` (text), `status` (0 tạm dừng / 1 đang chạy) |
| `coupon_wheel_logs` | `program_id`, `slice_index`, `prize_name`, `product_id`, `prize_text`, `status` (1=trúng, 0=trượt), `user_id`, `email`, `phone`, `ip`, `session_id`, `created` |

**Quan trọng:** `slices` và `settings` được lưu bằng **PHP `serialize()`** (boot `saving` serialize, boot `retrieved` unserialize) — KHÔNG phải JSON. Mọi truy vấn/sửa dữ liệu ngoài model đều phải tự serialize/unserialize; không `LIKE`/`JSON_EXTRACT` được.

Cột `coupon_wheel_logs.status` được thêm sau bằng nhánh ALTER trong Activator (guard `hasColumn`) — bản cài cũ vẫn nâng cấp được.

### Cấu trúc `slices` (mỗi phần tử)

`name`, `difficulty` (enum), `quantity` (0 = không giới hạn), `prize_type` (`text`|`product`), `prize_text`, `product_id`, `product_name`, `bg_color`, `text_color`.

### Cấu trúc `settings`

`require_login`, `spin_limit` (`forever`|`daily`|`none`), `contact_limit` (`none`|`email`|`phone`|`both`), `show_email`, `show_phone`, `trigger_type` (**mảng**: `['auto']`, `['button']` hoặc cả hai), `trigger_delay`, `display` (background/frame/center/triggerStyle/triggerIcon/triggerEffect/triggerBg/triggerPosition/headingStyle), `displayText` (6 field text × mỗi ngôn ngữ).

## Cơ chế trúng thưởng (weighted random) — `Supports\DifficultyWeight`

Xác suất **không nhập trực tiếp**, mà suy ra từ enum `SliceDifficulty`: `very_easy`=50, `easy`=30, `normal`=15, `hard`=5, `very_hard`=1, `never`=0.

- `roll($slices, $wonCounts)` — bỏ qua slice weight ≤ 0 và slice đã đạt `quantity`; random theo tổng weight; trả index hoặc `-1`.
- `isExhausted()` — true khi mọi slice weight > 0 đều hết quota → báo lỗi trước khi cho quay.
- `estimatePercentages()` — chỉ dùng preview % trong admin (không tính quota).

`stop_angle` do **server** tính (`360*5 + (360 - góc giữa ô trúng)`) rồi trả về cho JS phanh dừng — client không tự quyết kết quả.

## Map file

| File | Chức năng |
|---|---|
| `index.php` | Class `CouponWheel` — active/uninstall gọi Activator/Deactivator |
| `bootstrap/config.php` | Wire toàn bộ hook: `admin_navigation`, `admin_breadcrumb`, `admin_system_tabs`, `admin_assets`, `theme_custom_assets`, và **`cle_footer` → `WheelWidget::render`** (điểm chèn vòng quay vào frontend) |
| `bootstrap/ajax.php` | `Ajax::admin` save/delete/clone/productSearch/toggleStatus; `Ajax::client` spin |
| `routes/admin.php` | Prefix `admin/coupon-wheel`, middleware `auth::admin`: `/`, `/add`, `/edit/{id}`, `/logs` |
| `app/Enum/SliceDifficulty.php` | Enum độ khó + `weight()`, `label()`, `options()`, `weights()` |
| `app/Models/WheelProgram.php` | Bảng programs — boot serialize/unserialize `slices`/`settings` |
| `app/Models/SpinLog.php` | Bảng logs |
| `app/Supports/DifficultyWeight.php` | Weighted random + kiểm tra hết giải (xem mục trên) |
| `app/Supports/WheelDisplay.php` | Thư viện giao diện: 26 background, 8 frame, 5 center, 7 trigger icon + `defaults()`/`normalize()`/`triggerIconUrl()` |
| `app/Services/ActivatorService.php` / `DeactivatorService.php` | Tạo / drop 2 bảng |
| `app/Services/AdminService.php` | Menu con dưới nhóm `marketing` (programs + logs), breadcrumb, tab trong System |
| `app/Services/AssetsService.php` | CSS/JS admin + web |
| `app/Controllers/Admin/ProgramController.php` | index/add/edit — build 5 form (`formInfo`, `formCondition`, `formDisplay`, `formTrigger`, `formDisplayText`) rồi đẩy sang view |
| `app/Controllers/Admin/LogController.php` | Trang log, nhận `?program_id=` để lọc |
| `app/Modules/Admin/Programs/ProgramTable.php` | Bảng chương trình; badge status có class `js-cw-toggle-status` (bật/tắt inline) |
| `app/Modules/Admin/Logs/LogTable.php` | Bảng log; `dataDisplay()` preload tên chương trình bằng 1 query (tránh N+1) |
| `app/Modules/Web/WheelWidget.php` | Lấy chương trình đang chạy, normalize display + displayText (theo ngôn ngữ hiện tại), render `views/web/wheel` |
| `app/Ajax/Admin/ProgramAjax.php` | save (validate + sanitize slices/settings), delete, clone, productSearch, toggleStatus |
| `app/Ajax/Web/SpinAjax.php` | Toàn bộ nghiệp vụ quay (xem mục dưới) |
| `app/Notifications/SpinNotification.php` | Gửi Telegram mỗi lượt quay (no-op nếu chưa bật plugin telegram), luôn try/catch |

**views/**: `admin/programs/index|save` + 4 tab (`tab-base`, `tab-slices`, `tab-display`, `tab-text`), `admin/logs/index`, `web/wheel.blade.php` (popup + FAB + canvas + audio + confetti). View `save.blade.php` bơm `window.CWData` (slices JSON, bảng weight, ajaxUrl, i18n) cho `assets/js/admin.js`.

**assets/**: `css/admin.less` → `admin.css` (**sửa `.less`**, không sửa `.css`), `css/wheel.css` (viết tay), `js/admin.js` (repeater slice + preview xác suất + submit), `js/wheel.js` (class `PrizeWheel`: vẽ canvas, quay vô tận khi bấm → AJAX → phanh dừng đúng ô), `images/` (bg/frame/center/trigger), `audio/`.

**language/**: vi + en, 2 file mỗi ngôn ngữ: `admin.php`, `web.php`. Gọi `trans('coupon-wheel::admin.x')` / `trans('coupon-wheel::web.x')`.

## Luồng quay (`SpinAjax::spin`) — thứ tự kiểm tra CỐ ĐỊNH

1. Honeypot `antibot` (field ẩn, có giá trị = bot).
2. Lấy chương trình `status=1` và `start_at <= now <= end_at`. Không có → lỗi.
3. `require_login` → chặn khách vãng lai.
4. Rate-limit theo `spin_limit`: `forever` = 1 lần/đời, `daily` = 1 lần/ngày, `none` = không giới hạn. Điều kiện dò trùng là **IP HOẶC session_id**.
5. Chống trùng theo `contact_limit` (email / phone / cả hai).
6. Đếm `wonCounts` (chỉ query khi có slice giới hạn quantity) → `isExhausted()` → `roll()`.
7. Resolve giải: nếu `product` thì tra `Ecommerce\Models\Product` lấy tên + URL. `won = (product có product_id) || (text có prize_text)`.
8. Ghi `SpinLog` → gửi Telegram → trả `stop_angle` + thông tin giải.

## Quy tắc khi sửa

1. **Chỉ 1 chương trình chạy tại một thời điểm.** `ProgramAjax::save` và `toggleStatus` đều tắt tất cả chương trình khác khi bật một chương trình; `WheelWidget`/`SpinAjax` đều lấy `->first()`. Giữ nguyên bất biến này nếu thêm luồng bật/tắt mới.
2. Mọi ghi vào `slices`/`settings` phải đi qua model `WheelProgram` (serialize tự động) — đừng `DB::table()->update()` trực tiếp.
3. Thêm field vào `settings` → phải sửa cả 3 chỗ: `ProgramAjax::save` (sanitize/whitelist), `ProgramController::buildForm*` (render), `WheelWidget::render` (đọc + default). Field không nằm trong whitelist của `save()` sẽ bị **rơi mất im lặng**.
4. Ajax admin chạy qua `/admin/ajax` và **chỉ nhận POST**; ajax web (`spin`) qua `Ajax::client`.
5. `start_at`/`end_at` nhập bằng air-datepicker dạng `d/m/Y H:i`, được `toMysqlDatetime()` đổi sang MySQL — giữ hàm này khi đổi widget ngày.
6. Chuỗi hiển thị dùng `trans('coupon-wheel::...')`. `displayText` là văn bản do admin nhập (đa ngôn ngữ, suffix `_<langKey>`), khác với `trans()` — mặc định lấy từ `trans` rồi mới bị `displayText` ghi đè.
7. Sửa CSS admin → sửa `assets/css/admin.less`.

## Đã sửa (2026-08-08) — đừng "sửa lại" theo hướng cũ

- 🔴 `routes/admin.php` từng dùng `auth::admin` (hai dấu hai chấm) thay vì `auth:admin`. `MiddlewareNameResolver` tách tên tại dấu `:` đầu tiên nên `Authenticate::handle()` nhận `$guard = ':admin'`, khiến điều kiện `$guard == 'admin' && !Auth::hasCap('loggin_admin')` **không bao giờ đúng** → bất kỳ user đã đăng nhập nào (kể cả khách hàng thường) cũng vào được `/admin/coupon-wheel/*`. **Luôn dùng `auth:admin`** (một dấu) — đây là alias core đăng ký trong `CmsServiceProvider`. Plugin `popup` mắc cùng lỗi và đã sửa cùng lượt.

## Đã sửa (2026-08-10)

- `views/web/wheel.blade.php` tham chiếu `audio/lose.mp3` trong khi file không tồn tại (chỉ có `spin.mp3`, `win.mp3`, `close.mp3`) → 404 mỗi lần thua. Nay thẻ `<audio id="cw-audio-lose">` chỉ render khi file có thật (`file_exists(Path::plugin(...))`); `playAudio()` trong `wheel.js` vốn đã tự bỏ qua khi không tìm thấy element, nên **thả `lose.mp3` vào `assets/audio/` là tự chạy**, không cần sửa code.
- `LogTable::getColumns()` có comment "chỉ hiện cột chương trình khi xem tổng hợp" nhưng code luôn thêm cột. Đã **giữ code, sửa comment**: không ẩn được cột theo `program_id` vì lần load lại bảng bằng ajax không mang theo tham số đó → header sẽ lệch số cột với rows. Helper `filterProgramId()` được tách ra dùng cho `queryFilter()`.

## Gotcha (giới hạn thiết kế, KHÔNG phải bug)

- `DeactivatorService` drop thẳng 2 bảng khi **xóa hẳn** plugin (không chạy khi chỉ tắt) → mất sạch log lượt quay. Đây là hành vi cố ý, giống rating-star; nếu cần giữ log thì đổi thành no-op như `plugins/affiliate/app/Services/DeactivatorService.php`.
- Rate-limit dựa trên IP/session/email/phone do client gửi — chống được người dùng thường, không chống được kẻ cố tình (đổi IP + xoá cookie). Không có giới hạn theo `user_id` kể cả khi `require_login=1`.
- Prize loại `text` chỉ là chuỗi hiển thị; plugin **không** sinh/kiểm mã giảm giá thật (không tích hợp plugin discounts). Muốn mã thật thì admin phải tự tạo bên discounts rồi dán vào `prize_text`.
- `assets/audio/close.mp3` hiện không được dùng ở đâu.
