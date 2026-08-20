<div class="box coupon-wheel-form mb-3">
    <div class="box-content mb-2">
        <div style="max-width: 400px; margin: 0 auto 0 0">
            {!! \SkillDo\Cms\Form\Form::textBuilding('headingStyle', ['label' => 'Tiêu đề vòng xoay'], $settingDisplay['headingStyle'] ?? [])->render(); !!}
        </div>
    </div>
</div>

<div class="box coupon-wheel-form mb-3">
    <div class="box-content mb-2">
        <div class="row">
            <div class="form-group display-items-img">
                <label for="">{{ trans('coupon-wheel::admin.display.bg') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\CouponWheel\Supports\WheelDisplay::background() as $bgKey => $bgValue)
                        <label class="item-img {{($settingDisplay['background'] == $bgKey) ? 'active' : ''}}">
                            <span style="{!! $bgValue !!}"></span>
                            <input type="radio" name="background" value="{!! $bgKey !!}" {!! ($settingDisplay['background'] == $bgKey) ? 'checked' : '' !!}>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group display-items-img">
                <label for="">{{ trans('coupon-wheel::admin.display.frame') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\CouponWheel\Supports\WheelDisplay::frameCenter() as $bgKey => $bgValue)
                        <label class="item-img {!! ($settingDisplay['frame'] == $bgKey) ? 'active' : '' !!}">
                            <span style="background: url('{!! $bgValue !!}')"></span>
                            <input type="radio" name="frame" value="{{$bgKey}}" {{($settingDisplay['frame'] == $bgKey) ? 'checked' : ''}}>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group display-items-img">
                <label for="">{{ trans('coupon-wheel::admin.display.center') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\CouponWheel\Supports\WheelDisplay::imgCenter() as $bgKey => $bgValue)
                        <label class="item-img {!! ($settingDisplay['center'] == $bgKey) ? 'active' : '' !!}">
                            <span style="background: url('{!! $bgValue !!}')"></span>
                            <input type="radio" name="center" value="{{$bgKey}}" {{($settingDisplay['center'] == $bgKey) ? 'checked' : ''}}>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box coupon-wheel-form mb-3">
    <div class="box-content mb-2">
        <div class="row">
            <div class="col-md-3 form-group display-items-img">
                <label for="">{{ trans('coupon-wheel::admin.display.trigger_icon_opts') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\CouponWheel\Supports\WheelDisplay::gift() as $giftKey => $giftValue)
                        <label class="item-img {!! ($settingDisplay['triggerStyle'] == $giftKey) ? 'active' : '' !!}">
                            <span style="background: url('{!! $giftValue !!}')"></span>
                            <input type="radio" name="triggerStyle" value="{{$giftKey}}" {!! ($settingDisplay['triggerStyle'] == $giftKey) ? 'checked' : '' !!}>
                        </label>
                    @endforeach
                </div>
            </div>
            {!! $formTrigger->html() !!}
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.item-img').click(function(e){
            $(this).closest('.display-items-img').find('.item-img').removeClass('active');
            $(this).addClass('active');
        });
    })
</script>