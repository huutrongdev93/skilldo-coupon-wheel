<tr class="js_column js_wheel_log_item tr_<?php echo $item->id;?> <?php echo ($item->is_read == 0) ? 'un_read' : '';?>">
    <td class="column">
        <b style=""><?php echo $item->wheel_name;?></b>
    </td>
    <td class="column">
        <?php if(!empty($item->fullname)) echo '<p class="mb-1"><b>'.$item->fullname.'</b></p>';?>
        <?php if(!empty($item->phone)) echo '<p class="mb-1"><span class="badge badge-green">'.$item->phone.'</span></p>';?>
        <?php if(!empty($item->email)) echo '<p class="mb-1"><span class="badge badge-yellow">'.$item->email.'</span></p>';?>
    </td>
    <td class="column">
        <?php
        echo (empty($item->coupon_code)) ? '<p class="mb-0" style="color:#000;font-weight:bold;">Không trúng</p>' : '<p class="mb-0" style="color:green;font-weight:bold;">Trúng thưởng</p>';
        echo $item->slice_label;
        ?>
    </td>
    <td class="column">
        <?php echo $item->coupon_code; ?>
    </td>
    <td class="column">
        <?php echo date('d-m-Y h:i a', strtotime($item->created));?>
    </td>
    <td class="column text-right">
	    <?php
        if (Auth::hasCap('couponWheelLogDelete')) {
            echo Admin::btnConfirm([
                'id' => $item->id,
                'btn' => 'red btn-red-bg p-1 ps-2 pe-2',
                'trash' => 'disable',
                'action' => 'delete',
                'ajax' => 'AdminCouponWheelLogAjax::delete',
                'module' => 'wheels_log',
                'des' => 'Bạn chắc chắn muốn xóa lượt chơi này?'
            ]);
        }
	    ?>
    </td>
</tr>