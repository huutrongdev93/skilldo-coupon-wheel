<div class="box coupon-wheel-form mb-3">
	<div class="box-content mb-2">
		<div class="row">
			<div class="form-group display-items-img">
				<label for="">{{trans('admin.wheel.display.bg')}}</label>
				<div class="d-flex flex-wrap gap-2">
                    @foreach (WheelHelper::displayBackground() as $bgKey => $bgValue)
						<label class="item-img {{($wheelDisplay['background'] == $bgKey) ? 'active' : ''}}">
							<span style="{!! $bgValue !!}"></span>
							<input type="radio" name="background" value="{!! $bgKey !!}" {!! ($wheelDisplay['background'] == $bgKey) ? 'checked' : '' !!}>
						</label>
					@endforeach
				</div>
			</div>
			<div class="form-group display-items-img">
				<label for="">{{trans('admin.wheel.display.style')}}</label>
				<div class="d-flex flex-wrap gap-2">
                    @foreach (WheelHelper::displayWheel() as $bgKey => $bgValue)
						<label class="item-img {!! ($wheelDisplay['style'] == $bgKey) ? 'active' : '' !!}">
							<span style="background: url('{!! $bgValue !!}')"></span>
							<input type="radio" name="style" value="{{$bgKey}}" {{($wheelDisplay['style'] == $bgKey) ? 'checked' : ''}}>
						</label>
					@endforeach
				</div>
			</div>
			<div class="form-group display-items-img">
				<label for="">{{trans('admin.wheel.display.frame')}}</label>
				<div class="d-flex flex-wrap gap-2">
                    @foreach (WheelHelper::displayFrame() as $bgKey => $bgValue)
						<label class="item-img {!! ($wheelDisplay['frame'] == $bgKey) ? 'active' : '' !!}">
							<span style="background: url('{!! $bgValue !!}')"></span>
							<input type="radio" name="frame" value="{{$bgKey}}" {{($wheelDisplay['frame'] == $bgKey) ? 'checked' : ''}}>
						</label>
					@endforeach
				</div>
			</div>
			<div class="form-group display-items-img">
				<label for="">{{trans('admin.wheel.display.center')}}</label>
				<div class="d-flex flex-wrap gap-2">
					@foreach (WheelHelper::displayImgCenter() as $bgKey => $bgValue)
						<label class="item-img {!! ($wheelDisplay['center'] == $bgKey) ? 'active' : '' !!}">
							<span style="background: url('{!! $bgValue !!}')"></span>
							<input type="radio" name="center" value="{{$bgKey}}" {{($wheelDisplay['center'] == $bgKey) ? 'checked' : ''}}>
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
			{!! $formDisplay->html() !!}
		</div>
	</div>
</div>
<div class="box coupon-wheel-form mb-3">
	<div class="box-content mb-2">
		<div class="row">
			<div class="form-group display-items-img">
				<label for="">Tuỳ chọn icon</label>
				<div class="d-flex flex-wrap gap-2">
                    @foreach (WheelHelper::displayGift() as $giftKey => $giftValue)
						<label class="item-img {!! ($wheelDisplay['triggerStyle'] == $giftKey) ? 'active' : '' !!}">
							<span style="background: url('{!! $giftValue !!}')"></span>
							<input type="radio" name="triggerStyle" value="{{$giftKey}}" {!! ($wheelDisplay['background'] == $giftKey) ? 'checked' : '' !!}>
						</label>
					@endforeach
				</div>
			</div>
            {!! $formTrigger->html() !!}
		</div>
	</div>
</div>