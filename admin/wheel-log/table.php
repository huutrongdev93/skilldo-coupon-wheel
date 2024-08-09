<?php

use SkillDo\Form\Form;
use SkillDo\Table\SKDObjectTable;
use SkillDo\Http\Request;

class AdminWheelLogTable extends SKDObjectTable {

    function get_columns() {
        $this->_column_headers = [];
        $this->_column_headers['cb']       = 'cb';
        $this->_column_headers['name']     = [
            'label' => trans('admin.wheel.log.table.title'),
            'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('wheel_name', $item, $args)
        ];
        $this->_column_headers['info']     = [
            'label' => trans('admin.wheel.log.table.info'),
            'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnView::make('info', $item, $args)->html(function (\SkillDo\Table\Columns\ColumnView $column) {
                if(!empty($column->item->fullname)) {
                    echo '<p class="mb-1"><b>'.$column->item->fullname.'</b></p>';
                }
                if(!empty($column->item->phone)) {
                    echo '<p class="mb-1"><span class="badge text-bg-green">'.$column->item->phone.'</span></p>';
                }
                if(!empty($column->item->email)) {
                    echo '<span class="mb-1"><span class="badge text-bg-yellow">'.$column->item->email.'</b></span>';
                }
            })
        ];
        $this->_column_headers['coupon']    = [
            'label' => trans('admin.wheel.log.table.coupon'),
            'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnView::make('coupon', $item, $args)->html(function (\SkillDo\Table\Columns\ColumnView $column) {
                echo (empty($column->item->coupon_code)) ? '<p class="mb-0" style="color:#000;font-weight:bold;">Không trúng</p>' : '<p class="mb-0" style="color:green;font-weight:bold;">Trúng thưởng</p>';
                echo $column->item->slice_label;
            })
        ];
        $this->_column_headers['value']    = [
            'label' => trans('admin.wheel.log.table.value'),
            'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('coupon_code', $item, $args)
        ];
        $this->_column_headers['created']    = trans('admin.wheel.log.table.created');
        $this->_column_headers['action']   = trans('table.action');

        return apply_filters( "manage_wheelLog_columns", $this->_column_headers );
    }

    function actionButton($item, $module, $table): array
    {
        $listButton = [];

        if (Auth::hasCap('couponWheelLogDelete')) {

            $listButton[] = Admin::btnConfirm('red', [
                'id' => $item->id,
                'class' => 'btn-red-bg p-1 ps-2 pe-2',
                'icon' => Admin::icon('delete'),
                'action' => 'delete',
                'ajax' => 'AdminCouponWheelLogAjax::delete',
                'model' => 'wheels_log',
                'description' => 'Bạn chắc chắn muốn xóa lượt chơi này?'
            ]);
        }

        /**
         * @since 7.0.0
         */
        return apply_filters('admin_wheelLog_table_columns_action', $listButton);
    }

    function headerFilter(Form $form, Request $request)
    {
        $formFilter = form();
        /**
         * @singe v7.0.0
         */
        return apply_filters('admin_wheelLog_table_form_filter', $formFilter);
    }

    function headerSearch(Form $form, Request $request): Form
    {
        $form->text('name', ['placeholder' => trans('table.search.keyword').'...'], request()->input('keyword'));
        $form->text('phone', ['placeholder' => trans('phone').'...'], request()->input('phone'));
        $form->daterange('time', ['placeholder' => 'Thời gian'.'...'], request()->input('time'));

        /**
         * @singe v7.0.0
         */
        return apply_filters('admin_wheelLog_table_form_search', $form);
    }
}