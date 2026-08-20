<?php
namespace CouponWheel\Ajax\Admin;

use CouponWheel\Enum\SliceDifficulty;
use CouponWheel\Models\WheelProgram;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Support\Url;
use SkillDo\Http\Request;
use SkillDo\Support\Auth;
use SkillDo\Validate\Rule;

class ProgramAjax
{
    public static function save(Request $request): void
    {
        $id = (int) $request->input('id');

        // Validate fields cơ bản
        $validate = $request->validate([
            'name'     => Rule::make(trans('coupon-wheel::admin.form.name'))->notEmpty(),
            'start_at' => Rule::make(trans('coupon-wheel::admin.form.start_at'))->notEmpty(),
            'end_at'   => Rule::make(trans('coupon-wheel::admin.form.end_at'))->notEmpty(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        // Validate slices
        $slices = $request->input('slices', []);

        if (!is_array($slices) || empty($slices) || count($slices) < 2)
        {
            response()->error(trans('coupon-wheel::admin.ajax.save.error.slices'));
        }

        $validDifficulties = array_column(SliceDifficulty::cases(), 'value');

        $cleanSlices = [];

        foreach ($slices as $index => $slice)
        {
            if (empty($slice['name']))
            {
                response()->error(trans('coupon-wheel::admin.ajax.save.error.slice_name', ['num' => $index + 1]));
            }

            $difficulty = $slice['difficulty'] ?? 'normal';

            if (!in_array($difficulty, $validDifficulties))
            {
                $difficulty = 'normal';
            }

            $cleanSlices[] = [
                'name'         => trim($slice['name']),
                'difficulty'   => $difficulty,
                'quantity'     => max(0, (int) ($slice['quantity'] ?? 0)),
                'prize_type'   => in_array($slice['prize_type'] ?? '', ['text', 'product']) ? $slice['prize_type'] : 'text',
                'prize_text'   => trim($slice['prize_text'] ?? ''),
                'product_id'   => (int) ($slice['product_id'] ?? 0),
                'product_name' => trim($slice['product_name'] ?? ''),
                'bg_color'     => trim($slice['bg_color'] ?? '#4e73df'),
                'text_color'   => trim($slice['text_color'] ?? '#ffffff'),
            ];
        }

        // Settings điều kiện + kích hoạt
        $settings = [
            'require_login' => (int) $request->input('require_login', 0),
            'spin_limit'    => $request->input('spin_limit', 'forever'),
            'contact_limit' => in_array($request->input('contact_limit'), ['none','email','phone','both'])
                               ? $request->input('contact_limit')
                               : 'none',
            'show_email'    => (int) $request->input('show_email', 1),
            'show_phone'    => (int) $request->input('show_phone', 1),
            'trigger_type'  => (function() use ($request) {
                $types = [];
                if ((int) $request->input('trigger_auto',   0)) $types[] = 'auto';
                if ((int) $request->input('trigger_button', 0)) $types[] = 'button';
                return !empty($types) ? $types : ['auto']; // mặc định auto nếu không chọn gì
            })(),
            'trigger_delay' => max(0, (int) $request->input('trigger_delay', 3)),
            // Cài đặt giao diện
            'display' => [
                'background'      => $request->input('background', 'style1'),
                'frame'           => $request->input('frame', 'style1'),
                'center'          => $request->input('center', 'style1'),
                'triggerStyle'    => $request->input('triggerStyle', 'style1'),
                'triggerIcon'     => $request->input('triggerIcon', ''),
                'triggerEffect'   => $request->input('triggerEffect', ''),
                'triggerBg'       => $request->input('triggerBg', '#ffe2e2'),
                'triggerPosition' => in_array($request->input('triggerPosition'), ['bottom-right','bottom-left','top-right','top-left'])
                                     ? $request->input('triggerPosition')
                                     : 'bottom-right',
                'headingStyle'    => self::cleanHeadingStyle($request->input('headingStyle', [])),
            ],
            // Văn bản đa ngôn ngữ
            'displayText' => self::cleanDisplayText($request->input('displayText', [])),
        ];

        $status = (int) $request->input('status', 0);

        // Nếu bật chương trình này → tắt tất cả chương trình khác
        if ($status === 1)
        {
            $query = WheelProgram::where('status', 1);

            if ($id > 0)
            {
                $query->where('id', '!=', $id);
            }

            $query->update(['status' => 0]);
        }

        $data = [
            'name'     => $request->input('name'),
            'start_at' => self::toMysqlDatetime($request->input('start_at')),
            'end_at'   => self::toMysqlDatetime($request->input('end_at')),
            'slices'   => $cleanSlices,
            'settings' => $settings,
            'status'   => $status,
        ];

        if ($id > 0)
        {
            $data['id'] = $id;

            $result = WheelProgram::insert($data);
        }
        else
        {
            $data['user_created'] = Auth::id();

            $result = WheelProgram::insert($data);
        }

        if (is_skd_error($result))
        {
            response()->error($result);
        }

        $savedId = $id > 0 ? $id : $result;

        response()->success(trans('coupon-wheel::admin.ajax.save.success'), [
            'id'       => $savedId,
            'redirect' => Url::admin('coupon-wheel'),
        ]);
    }

    /**
     * Sanitize dữ liệu headingStyle từ field textBuilding.
     * Chấp nhận array với các key hợp lệ, strip_tags các giá trị string.
     */
    private static function cleanHeadingStyle(mixed $raw): array
    {
        if (!is_array($raw)) return [];

        $allowed = ['typography', 'color', 'stroke', 'shadow', 'margin', 'padding'];

        $result = [];

        foreach ($raw as $key => $value)
        {
            if (!in_array($key, $allowed)) continue;

            if (is_array($value))
            {
                $result[$key] = $value; // giữ nguyên mảng con (fontSize desktop/tablet/mobile)
            }
            else
            {
                $result[$key] = strip_tags(trim((string) $value));
            }
        }

        return $result;
    }

    /**
     * Sanitize dữ liệu văn bản đa ngôn ngữ.
     * Chỉ chấp nhận các field hợp lệ (base fields + _langKey suffix),
     * loại bỏ HTML tag.
     */
    private static function cleanDisplayText(mixed $raw): array
    {
        if (!is_array($raw)) return [];

        // Các field gốc được phép lưu
        $allowedFields = [
            'popupHeading', 'popupDescription',
            'winHeading', 'winDescription',
            'loseHeading', 'loseDescription',
        ];

        // Tạo danh sách tất cả field name hợp lệ (gốc + _langKey cho mỗi ngôn ngữ không default)
        $validKeys = [];
        $langDefault = Language::default();

        foreach ($allowedFields as $field) {
            $validKeys[] = $field; // field ngôn ngữ mặc định
            foreach (Language::list() as $langKey => $langData) {
                if ($langKey === $langDefault) continue;
                $validKeys[] = $field . '_' . $langKey;
            }
        }

        $result = [];
        foreach ($validKeys as $key) {
            $result[$key] = isset($raw[$key]) ? strip_tags(trim((string) $raw[$key])) : '';
        }

        return $result;
    }

    /**
     * Chuyển định dạng từ air-datepicker (dd/mm/yyyy HH:mm) sang MySQL datetime (yyyy-mm-dd HH:mm:ss)
     */
    private static function toMysqlDatetime(string $value): string
    {
        if (empty($value)) return '';

        // Nếu đã ở dạng MySQL thì giữ nguyên
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return $value;
        }

        // Dạng dd/mm/yyyy HH:mm
        $dt = \DateTime::createFromFormat('d/m/Y H:i', trim($value));
        if ($dt) {
            return $dt->format('Y-m-d H:i:s');
        }

        return $value;
    }

    public static function delete(Request $request): void
    {
        $id = (int) $request->input('id');
        if ($id <= 0) {
            response()->error(trans('coupon-wheel::admin.ajax.delete.error.id'));
        }

        $result = WheelProgram::destroy($id);

        if (is_skd_error($result)) {
            response()->error($result);
        }

        response()->success(trans('coupon-wheel::admin.ajax.delete.success'));
    }

    public static function productSearch(Request $request): void
    {
        if (!class_exists('Ecommerce\Models\Product')) {
            response()->error('Plugin Sicommerce chưa được kích hoạt.');
        }

        $keyword = trim($request->input('keyword', ''));

        $query = \Ecommerce\Models\Product::where('public', 1)
            ->select('id', 'title', 'image', 'price', 'price_sale');

        if (!empty($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        $products = $query->limit(15)->get();

        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'id'    => $product->id,
                'title' => $product->title,
                'image' => !empty($product->image) ? asset($product->image) : '',
                'price' => $product->price_sale > 0 ? $product->price_sale : $product->price,
            ];
        }

        response()->success('OK', ['items' => $items]);
    }

    public static function clone(Request $request): void
    {
        $id = (int) $request->input('id');

        if ($id <= 0) {
            response()->error(trans('coupon-wheel::admin.ajax.clone.error.id'));
        }

        /** @var WheelProgram $original */
        $original = WheelProgram::find($id);

        if (empty($original)) {
            response()->error(trans('coupon-wheel::admin.ajax.clone.error.not_found'));
        }

        $data = [
            'name'         => $original->name . trans('coupon-wheel::admin.ajax.clone.name_suffix'),
            'start_at'     => $original->start_at,
            'end_at'       => $original->end_at,
            'slices'       => $original->slices,
            'settings'     => $original->settings,
            'status'       => 0,
            'user_created' => Auth::id(),
        ];

        $newId = WheelProgram::insert($data);

        if (is_skd_error($newId)) {
            response()->error($newId);
        }

        response()->success(trans('coupon-wheel::admin.ajax.clone.success'), [
            'id'       => $newId,
            'redirect' => Url::admin('coupon-wheel/edit/' . $newId),
        ]);
    }

    public static function toggleStatus(Request $request): void
    {
        $id = (int) $request->input('id');

        if ($id <= 0) {
            response()->error(trans('coupon-wheel::admin.ajax.delete.error.id'));
        }

        /** @var WheelProgram $program */
        $program = WheelProgram::find($id);

        if (empty($program)) {
            response()->error(trans('coupon-wheel::admin.ajax.clone.error.not_found'));
        }

        $newStatus = $program->status == 1 ? 0 : 1;

        // Nếu bật → tắt tất cả chương trình khác
        if ($newStatus === 1)
        {
            WheelProgram::where('status', 1)
                ->where('id', '!=', $id)
                ->update(['status' => 0]);
        }

        WheelProgram::where('id', $id)->update(['status' => $newStatus]);

        response()->success(trans('coupon-wheel::admin.program.toggle.success'), [
            'id'     => $id,
            'status' => $newStatus,
        ]);
    }
}







