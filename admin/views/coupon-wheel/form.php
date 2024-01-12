<ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
    <li class="nav-item">
        <a href="#base" aria-controls="base" role="tab" data-bs-toggle="tab" class="active">CƠ BẢN</a>
    </li>
    <li class="nav-item">
        <a href="#slices" aria-controls="slices" role="tab" data-bs-toggle="tab">PHẦN THƯỞNG</a>
    </li>
    <li class="nav-item">
        <a href="#display" aria-controls="display" role="tab" data-bs-toggle="tab">GIAO DIỆN</a>
    </li>
    <li class="nav-item">
        <a href="#text" aria-controls="text" role="tab" data-bs-toggle="tab">VĂN BẢN</a>
    </li>
    <li class="nav-item">
        <a href="#form" aria-controls="form" role="tab" data-bs-toggle="tab">CẤU HÌNH FORM</a>
    </li>
</ul>
<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="base" aria-labelledby="base" tabindex="0" role="tabpanel">
        <div class="box">
            <div class="box-content p-2">
                <?php echo $formBase->html(false);?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="slices" aria-labelledby="slices" tabindex="0" role="tabpanel">
        <div class="row">
            <?php for($i = 1; $i <= 12; $i++) { ?>
                <div class="col-md-6">
                    <p class="heading">#<?php echo $i;?></p>
                    <div class="box">
                        <div class="box-content p-2">
                            <div class="row">
                                <?php echo $formAward[$i]->html(false);?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="tab-pane fade" id="display" aria-labelledby="display" tabindex="0" role="tabpanel">
        <?php include_once 'display.php';?>
    </div>
    <div class="tab-pane fade" id="text" aria-labelledby="text" tabindex="0" role="tabpanel">
        <div class="box">
            <div class="box-content p-2">
                <p class="heading">Văn bản Popup</p>
                <div class="row">
                    <?php echo $formText['first']->html(false);?>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-content p-2">
                <p class="heading">Thông báo khi quay thành công</p>
                <div class="row">
                    <?php echo $formText['alertSuccess']->html(false);?>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-content p-2">
                <p class="heading">Thông báo khi quay thất bại</p>
                <div class="row">
                    <?php echo $formText['alertFailed']->html(false);?>
                </div>
            </div>
        </div>

	    <div class="box">
		    <div class="box-content p-2">
			    <p class="heading">Thông báo khi khác</p>
			    <div class="row">
                    <?php echo $formText['alert']->html(false);?>
			    </div>
		    </div>
	    </div>

    </div>
    <div class="tab-pane fade" id="form" aria-labelledby="form" tabindex="0" role="tabpanel">
        <div class="box">
            <div class="box-content p-2">
                <div class="row">
                    <?php echo $formText['form']->html(false);?>
                </div>
            </div>
        </div>
    </div>
</div>