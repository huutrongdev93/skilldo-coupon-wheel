<div class="box coupon-wheel-form">
	<div class="box-content p-2 mb-2">
		<div class="row">
			<div class="form-group display-items-img">
				<label for="">Màu nền</label>
				<div class="d-flex flex-wrap gap-2">
                    <?php foreach (WheelHelper::displayBackground() as $bgKey => $bgValue) { ?>
						<label class="item-img <?php echo ($wheelDisplay['background'] == $bgKey) ? 'active' : '';?>">
							<span style="<?php echo $bgValue;?>"></span>
							<input type="radio" name="background" value="<?php echo $bgKey;?>" <?php echo ($wheelDisplay['background'] == $bgKey) ? 'checked' : '';?>>
						</label>
                    <?php } ?>
				</div>
			</div>
			<div class="form-group display-items-img">
				<label for="">Kiểu vòng quay</label>
				<div class="d-flex flex-wrap gap-2">
                    <?php foreach (WheelHelper::displayWheel() as $bgKey => $bgValue) { ?>
						<label class="item-img <?php echo ($wheelDisplay['style'] == $bgKey) ? 'active' : '';?>">
							<span style="<?php echo "background: url('".$bgValue."')";?>"></span>
							<input type="radio" name="style" value="<?php echo $bgKey;?>" <?php echo ($wheelDisplay['style'] == $bgKey) ? 'checked' : '';?>>
						</label>
                    <?php } ?>
				</div>
			</div>
			<div class="form-group display-items-img">
				<label for="">Kiểu khung vòng quay</label>
				<div class="d-flex flex-wrap gap-2">
                    <?php foreach (WheelHelper::displayFrame() as $bgKey => $bgValue) { ?>
						<label class="item-img <?php echo ($wheelDisplay['frame'] == $bgKey) ? 'active' : '';?>">
							<span style="<?php echo "background: url('".$bgValue."')";?>"></span>
							<input type="radio" name="frame" value="<?php echo $bgKey;?>" <?php echo ($wheelDisplay['frame'] == $bgKey) ? 'checked' : '';?>>
						</label>
                    <?php } ?>
				</div>
			</div>
			<div class="form-group display-items-img">
				<label for="">Kiểu tâm vòng quay</label>
				<div class="d-flex flex-wrap gap-2">
                    <?php foreach (WheelHelper::displayImgCenter() as $bgKey => $bgValue) { ?>
						<label class="item-img <?php echo ($wheelDisplay['center'] == $bgKey) ? 'active' : '';?>">
							<span style="<?php echo "background: url('".$bgValue."')";?>"></span>
							<input type="radio" name="center" value="<?php echo $bgKey;?>" <?php echo ($wheelDisplay['center'] == $bgKey) ? 'checked' : '';?>>
						</label>
                    <?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box coupon-wheel-form">
	<div class="box-content p-2 mb-2">
		<div class="row">
			<?php
				$form = new FormBuilder();
                $form->add('textColor', 'color', ['label' => 'Màu chữ', 'start' => 3], $wheelDisplay['textColor']);
                $form->add('btnRunBgColor', 'color', ['label' => 'Màu nền nút quay', 'start' => 3], $wheelDisplay['btnRunBgColor']);
                $form->add('btnRunTxtColor', 'color', ['label' => 'Màu chữ nút quay', 'start' => 3], $wheelDisplay['btnRunTxtColor']);
				echo $form->html(true);
			?>
		</div>
	</div>
</div>
<div class="box coupon-wheel-form">
	<div class="box-content p-2 mb-2">
		<div class="row">
			<div class="form-group display-items-img">
				<label for="">Tuỳ chọn icon</label>
				<div class="d-flex flex-wrap gap-2">
                    <?php foreach (WheelHelper::displayGift() as $giftKey => $giftValue) { ?>
						<label class="item-img <?php echo ($wheelDisplay['triggerStyle'] == $giftKey) ? 'active' : '';?>">
							<span style="<?php echo "background: url('".$giftValue."')";?>"></span>
							<input type="radio" name="triggerStyle" value="<?php echo $giftKey;?>" <?php echo ($wheelDisplay['background'] == $giftKey) ? 'checked' : '';?>>
						</label>
                    <?php } ?>
				</div>
			</div>
            <?php
            echo $formTrigger->html(true);
            ?>
		</div>
	</div>
</div>