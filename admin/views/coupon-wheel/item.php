<tr class="js_column js_coupon_wheel_item tr_<?php echo $item->id;?>" data-item="<?php echo htmlentities(json_encode($item));?>">
    <td class="code column-code">
        <a href="<?php echo Url::admin('plugins?page=couponWheel&view=edit&id='.$item->id);?>">
            <b><?php echo $item->name;?></b>
        </a>
    </td>
    <td class="column">
        <?php if($item->status == 'run') echo '<span class="badge badge-green">'.WheelHelper::status($item->status.'.label').'</span>';?>
        <?php if($item->status == 'pending') echo '<span class="badge badge-yellow">'.WheelHelper::status($item->status.'.label').'</span>';?>
        <?php if($item->status == 'stop') echo '<span class="badge badge-red">'.WheelHelper::status($item->status.'.label').'</span>';?>
    </td>
    <td class="column text-center"><?php echo $item->popup_spin;?></td>
    <td class="column">
        <?php echo date('d-m-Y h:i a', strtotime($item->created));?>
    </td>
    <td class="column text-right" style="width: 300px;">
        <?php if($item->status != 'run') {?>
	        <?php if (Auth::hasCap('couponWheelDelete')) {
                echo Admin::btnConfirm([
                    'id' => $item->id,
                    'btn' => 'red btn-red-bg p-1 ps-2 pe-2',
                    'trash' => 'disable',
                    'action' => 'delete',
                    'ajax' => 'AdminCouponWheelAjax::delete',
                    'module' => 'wheels',
                    'des' => 'Bạn chắc chắn muốn xóa chương trình '.$item->name.' này?'
                ]);
            } ?>
        <?php if (Auth::hasCap('couponWheelEdit')) { ?><button class="js_coupon_wheel_btn_status btn-green btn btn-green-bg p-1 ps-2 pe-2" data-id="<?php echo $item->id;?>" data-status="run">Khởi chạy</button><?php } ?>
        <?php } else { ?>
            <?php if (Auth::hasCap('couponWheelEdit')) { ?><button class="js_coupon_wheel_btn_status btn-red btn btn-red-bg p-1 ps-2 pe-2" data-id="<?php echo $item->id;?>" data-status="stop">Tạm dừng</button><?php } ?>
        <?php }?>
        <a class="btn-blue btn btn-blue-bg p-1 ps-2 pe-2" href="<?php echo Url::admin('plugins?page=couponWheel&view=edit&id='.$item->id);?>">Chỉnh sửa</a>
    </td>
</tr>