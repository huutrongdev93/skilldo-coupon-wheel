<?php
namespace CouponWheel\Modules\Admin\Logs;

use CouponWheel\Models\SpinLog;
use CouponWheel\Models\WheelProgram;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Cms\Table\Columns\ColumnBadge;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;

class LogTable extends SKDObjectTable
{
    protected string $module = 'coupon_wheel_log';

    protected mixed $model = SpinLog::class;

    public function getColumns(): array
    {
        $cols = [];

        // Cột chương trình LUÔN hiện, kể cả khi đang lọc theo 1 chương trình.
        // Không ẩn theo filterProgramId(): lần load lại bảng bằng ajax không mang theo
        // program_id nên header sẽ lệch với số cột của rows.
        $cols['program_id'] = [
            'label'  => trans('coupon-wheel::admin.log.col.program'),
            'column' => fn($item, $args) => ColumnText::make('program_id', $item, $args)
                ->value(fn($item) => $item->program_name ?? ('ID #' . $item->program_id)),
        ];

        $cols['prize_name'] = [
            'label'  => trans('coupon-wheel::admin.log.col.prize'),
            'column' => fn($item, $args) => ColumnText::make('prize_name', $item, $args),
        ];

        $cols['prize_code'] = [
            'label'  => trans('coupon-wheel::admin.log.col.code'),
            'column' => fn($item, $args) => ColumnText::make('prize_code', $item, $args)
                ->value(fn($item) => $item->prize_text ?: ($item->product_id
                    ? trans('coupon-wheel::admin.log.product_id_prefix') . $item->product_id
                    : '—')),
        ];

        $cols['email'] = [
            'label'  => trans('coupon-wheel::admin.log.col.email'),
            'column' => fn($item, $args) => ColumnText::make('email', $item, $args)
                            ->description(fn($item) => $item->phone ?: ''),
        ];

        $cols['status'] = [
            'label'  => trans('coupon-wheel::admin.log.col.result'),
            'column' => fn($item, $args) => ColumnBadge::make('status', $item, $args)
                            ->label(fn($val) => $val ? trans('coupon-wheel::admin.log.result.win') : trans('coupon-wheel::admin.log.result.lose'))
                            ->color(fn($val) => $val ? 'success' : 'secondary'),
        ];

        $cols['ip'] = [
            'label'  => trans('coupon-wheel::admin.log.col.ip'),
            'column' => fn($item, $args) => ColumnText::make('ip', $item, $args),
        ];

        $cols['created'] = [
            'label'  => trans('coupon-wheel::admin.log.col.time'),
            'column' => fn($item, $args) => ColumnText::make('created', $item, $args)
                            ->datetime('d/m/Y H:i'),
        ];

        return $cols;
    }

    public function headerButton(): array
    {
        return [
            'reload' => Admin::button('reload'),
        ];
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        parent::queryDisplay($query, $request, $data);

        return $query->orderBy('created', 'desc');
    }

    /**
     * program_id đang lọc: ưu tiên giá trị controller đẩy qua Cms::getData,
     * sau đó tới request (ajax load lại bảng không đi qua controller).
     */
    protected function filterProgramId(): int
    {
        $programId = (int) Cms::getData('program_id', 0);

        if (!$programId) {
            $programId = (int) request()->input('program_id', 0);
        }

        return max(0, $programId);
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        $programId = $this->filterProgramId();

        if ($programId > 0) {
            $query->where('program_id', $programId);
        }

        return $query;
    }

    /**
     * Được gọi sau khi query xong 1 page — preload tên chương trình bằng 1 query duy nhất.
     */
    public function dataDisplay($objects)
    {
        if ($objects->isEmpty()) {
            return $objects;
        }

        $programIds = $objects->pluck('program_id')->unique()->filter()->values()->toArray();

        $programs = WheelProgram::whereIn('id', $programIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        $objects->each(function ($item) use ($programs) {
            $item->program_name = isset($programs[$item->program_id])
                ? $programs[$item->program_id]->name
                : ('ID #' . $item->program_id);
        });

        return $objects;
    }
}
