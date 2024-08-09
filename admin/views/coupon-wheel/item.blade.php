<tr class="js_column js_coupon_wheel_item tr_{{$item->id}}" data-item="{!! htmlentities(json_encode($item)) !!}">
    <td class="code column-code">
        <a href="{!! Url::admin('plugins/coupon-wheel/edit/'.$item->id) !!}">
            <b>{{$item->name}}</b>
        </a>
    </td>
    <td class="column">
        @if($item->status == 'run')
            <span class="badge badge-green">{!! WheelHelper::status($item->status.'.label') !!}</span>
        @endif
        @if($item->status == 'pending')
            <span class="badge badge-yellow">{!! WheelHelper::status($item->status.'.label') !!}</span>
        @endif
        @if($item->status == 'stop')
            <span class="badge badge-red">{!! WheelHelper::status($item->status.'.label') !!}</span>
        @endif
    </td>
    <td class="column text-center">{{$item->popup_spin}}</td>
    <td class="column">
        {{date('d-m-Y h:i a', strtotime($item->created))}}
    </td>
    <td class="column text-right" style="width: 300px;">
        @if($item->status != 'run')
	        @if (Auth::hasCap('couponWheelDelete'))
                {!! Admin::btnConfirm('red', [
                    'id' => $item->id,
                    'label' => trans('general.delete'),
                    'class' => ['btn-red-bg p-1 ps-2 pe-2'],
                    'action' => 'delete',
                    'ajax'  => 'AdminCouponWheelAjax::delete',
                    'model' => 'wheels',
                    'description'   => 'Bạn chắc chắn muốn xóa chương trình '.$item->name.' này?'
                ]) !!}
            @endif
            @if (Auth::hasCap('couponWheelEdit'))
                {!! Admin::button('green', [
                    'class'         => ['js_coupon_wheel_btn_status', 'btn-green-bg', 'p-1', 'ps-2', 'pe-2'],
                    'data-id'       => $item->id,
                    'data-status'   => 'run',
                    'text'          => 'Khởi chạy'
                ]) !!}
            @endif
        @else
            @if (Auth::hasCap('couponWheelEdit'))
                {!! Admin::button('red', [
                    'class'         => ['js_coupon_wheel_btn_status', 'btn-red-bg', 'p-1', 'ps-2', 'pe-2'],
                    'data-id'       => $item->id,
                    'data-status'   => 'stop',
                    'text'          => 'Tạm dừng'
                ]) !!}
            @endif
        @endif
        <a class="btn-blue btn btn-blue-bg p-1 ps-2 pe-2" href="{!! Url::admin('plugins/coupon-wheel/edit/'.$item->id); !!}">Chỉnh sửa</a>
    </td>
</tr>