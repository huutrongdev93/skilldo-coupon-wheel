<ul class="nav nav-tabs nav-tabs-horizontal mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" href="#base" data-bs-toggle="tab" role="tab" aria-controls="base" aria-selected="true">{{trans('admin.wheel.tab.base')}}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#slices" role="tab" data-bs-toggle="tab" aria-controls="slices" aria-selected="false" tabindex="-1">{{trans('admin.wheel.tab.slices')}}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#display" role="tab" data-bs-toggle="tab" aria-controls="display" aria-selected="false" tabindex="-1">{{trans('admin.wheel.tab.display')}}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#text" role="tab" data-bs-toggle="tab" aria-controls="text" aria-selected="false" tabindex="-1">{{trans('admin.wheel.tab.text')}}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="#form" role="tab" data-bs-toggle="tab" aria-controls="form" aria-selected="false" tabindex="-1">{{trans('admin.wheel.tab.form')}}</a>
    </li>
    <div class="" style="position: absolute; right: 10px;">
        <button name="save" class="btn btn-icon btn-green" form="js_coupon_wheel_form">{!! Admin::icon('save') !!} {{trans('button.save')}}</button>
        <a href="{!! Url::admin('plugins/coupon-wheel') !!}" class="btn btn-icon btn-blue">{!! Admin::icon('back') !!} {{trans('button.back')}}</a>
    </div>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane fade active show" id="base" aria-labelledby="base" tabindex="0" role="tabpanel">
        <div class="box">
            <div class="box-content">
                <div class="row">
                    {!! $formBase->html() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="slices" aria-labelledby="slices" tabindex="0" role="tabpanel">
        <div class="row">
            @for($i = 1; $i <= 12; $i++)
                <div class="col-md-6">
                    <p class="heading">#{{$i}}</p>
                    <div class="box">
                        <div class="box-content p-2">
                            <div class="row">
                                {!! $formAward[$i]->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
    <div class="tab-pane fade" id="display" aria-labelledby="display" tabindex="0" role="tabpanel">
        {!! Plugin::partial(CP_WHEEL_NAME, 'admin/views/coupon-wheel/display', [
            'wheelDisplay' => $wheelDisplay,
            'formTrigger' => $formTrigger,
            'formDisplay' => $formDisplay,
        ]); !!}
    </div>
    <div class="tab-pane fade" id="text" aria-labelledby="text" tabindex="0" role="tabpanel">
        <div class="box mb-3">
            <div class="box-header"><h4 class="box-title">{{trans('admin.wheel.tab.text.heading1')}}</h4></div>
            <div class="box-content">
                <div class="row">
                    {!! $formText['first']->html() !!}
                </div>
            </div>
        </div>

        <div class="box mb-3">
            <div class="box-header"><h4 class="box-title">{{trans('admin.wheel.tab.text.heading2')}}</h4></div>
            <div class="box-content">
                <div class="row">
                    {!! $formText['alertSuccess']->html() !!}
                </div>
            </div>
        </div>

        <div class="box mb-3">
            <div class="box-header"><h4 class="box-title">{{trans('admin.wheel.tab.text.heading3')}}</h4></div>
            <div class="box-content">
                <div class="row">
                    {!! $formText['alertFailed']->html() !!}
                </div>
            </div>
        </div>

	    <div class="box mb-3">
            <div class="box-header"><h4 class="box-title">{{trans('admin.wheel.tab.text.heading4')}}</h4></div>
            <div class="box-content">
			    <div class="row">
                    {!! $formText['alert']->html() !!}
			    </div>
		    </div>
	    </div>

    </div>
    <div class="tab-pane fade" id="form" aria-labelledby="form" tabindex="0" role="tabpanel">
        <div class="box mb-3">
            <div class="box-content">
                <div class="row">
                    {!! $formText['form']->html() !!}
                </div>
            </div>
        </div>
    </div>
</div>