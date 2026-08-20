<?php
namespace CouponWheel\Modules\Admin\Programs;

use CouponWheel\Models\WheelProgram;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Url;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\Columns\ColumnBadge;
use SkillDo\Cms\Table\Columns\ColumnCheckbox;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;

class ProgramTable extends SKDObjectTable
{
    protected string $module = 'coupon_wheel_program';

    protected mixed $model = WheelProgram::class;

    public function getColumns(): array
    {
        return [
            'cb'       => 'cb',
            'name'     => [
                'label'  => trans('coupon-wheel::admin.program.col.name'),
                'column' => fn($item, $args) => ColumnText::make('name', $item, $args)->title()
            ],
            'start_at' => [
                'label'  => trans('coupon-wheel::admin.program.col.time'),
                'column' => fn($item, $args) => ColumnText::make('start_at', $item, $args)
                                ->value(fn($item) => ($item->start_at ? date('d/m/Y H:i', strtotime($item->start_at)) : '—')
                                    . ' → '
                                    . ($item->end_at ? date('d/m/Y H:i', strtotime($item->end_at)) : '—')),
            ],
            'status'   => [
                'label'  => trans('coupon-wheel::admin.program.col.status'),
                'column' => fn($item, $args) => ColumnBadge::make('status', $item, $args)
                                ->color(fn($v) => $v == 1 ? 'success' : 'secondary')
                                ->label(fn($v) => $v == 1
                                    ? trans('coupon-wheel::admin.program.status.running')
                                    : trans('coupon-wheel::admin.program.status.paused'))
                                ->class('js-cw-toggle-status')
                                ->attributes([
                                    'class' => 'js-cw-toggle-status',
                                    'data-id' => $item->id,
                                    'data-model' => $this->model,
                                    'data-status' => $item->status,
                                    'data-label-on' => trans('coupon-wheel::admin.program.status.running'),
                                    'data-label-off' => trans('coupon-wheel::admin.program.status.paused')
                                ]),
            ],
            'action'   => trans('coupon-wheel::admin.program.col.action'),
        ];
    }

    public function actionButton($item, $module, $table): array
    {
        return [
            'logs'   => Admin::button('success', [
                'href'    => Url::admin('coupon-wheel/logs?program_id=' . $item->id),
                'icon'    => '<i class="fa-solid fa-chart-bar"></i>',
                'tooltip' => trans('coupon-wheel::admin.program.btn.logs'),
            ]),
            'edit'   => Admin::button('blue', [
                'href'    => Url::admin('coupon-wheel/edit/' . $item->id),
                'icon'    => Admin::icon('edit'),
                'tooltip' => trans('coupon-wheel::admin.program.btn.edit'),
            ]),
            'clone'  => Admin::button('default', [
                'icon'      => '<i class="fa-regular fa-copy"></i>',
                'tooltip'   => trans('coupon-wheel::admin.program.btn.clone'),
                'class'     => 'js-cw-clone',
                'data-id'   => $item->id,
                'data-name' => html_escape($item->name),
            ]),
            'delete' => Admin::btnDelete([
                'id'          => $item->id,
                'model'       => $this->model,
                'module'      => $this->module,
                'description' => trans('coupon-wheel::admin.program.btn.delete_confirm', ['name' => html_escape($item->name)]),
            ]),
        ];
    }

    public function headerButton(): array
    {
        return [
            'add'    => Admin::button('add', ['href' => Url::admin('coupon-wheel/add')]),
            'reload' => Admin::button('reload'),
        ];
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        return $query->orderBy('created', 'desc');
    }
}

