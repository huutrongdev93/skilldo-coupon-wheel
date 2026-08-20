@php
    use CouponWheel\Supports\WheelDisplay;
@endphp

{{-- FAB (hiển thị khi bật chế độ button) --}}
@if($showButtonTrigger)
<button type="button" id="cw-fab-btn"
        class="cw-fab cw-fab--{{ $triggerPosition }} {{ $triggerEffect }}"
        style="background-color: {{ $triggerBg }};"
        title="{{ trans('coupon-wheel::web.fab.title') }}">
    <img src="{{ $triggerUrl }}" alt="{{ trans('coupon-wheel::web.fab.title') }}" style="width:42px;height:42px;object-fit:contain;">
</button>
@endif

{{-- Audio --}}
<audio id="cw-audio-spin"  preload="auto" style="display:none;">
    <source src="{{ asset('coupon-wheel::audio/spin.mp3') }}" type="audio/mpeg">
</audio>
<audio id="cw-audio-win"   preload="auto" style="display:none;">
    <source src="{{ asset('coupon-wheel::audio/win.mp3') }}" type="audio/mpeg">
</audio>
{{-- Âm thanh khi thua: chỉ render khi file tồn tại (assets/audio/lose.mp3 hiện chưa có).
     wheel.js gọi playAudio('cw-audio-lose') và tự bỏ qua nếu không tìm thấy element,
     nên chỉ cần thả file lose.mp3 vào assets/audio/ là tự động chạy. --}}
@if(file_exists(\SkillDo\Support\Path::plugin('coupon-wheel/assets/audio/lose.mp3')))
<audio id="cw-audio-lose"  preload="auto" style="display:none;">
    <source src="{{ asset('coupon-wheel::audio/lose.mp3') }}" type="audio/mpeg">
</audio>
@endif

{{-- Confetti canvas --}}
<canvas id="cw-confetti" style="position:fixed;inset:0;pointer-events:none;z-index:999999;display:none;"></canvas>

{{-- Overlay --}}
<div class="cw-overlay" id="cw-overlay" role="dialog" aria-modal="true" aria-label="{{ trans('coupon-wheel::admin.title') }}">
    <div class="cw-modal" style="{{ $bgStyle }} background-size:cover;background-position:center;">
        <div class="cw-header">
            <div class="cw-title" style="{!! $headingStyle !!}">{!! $displayText['popupHeading'] !!}</div>
            <button class="cw-close" id="cw-close" aria-label="{{ trans('coupon-wheel::web.close.aria') }}">✕</button>
        </div>
        <div class="cw-body">
            {{-- Cột trái --}}
            <div class="py-4 cw-card--wheel" style="background: none">
                <div class="cw-wheel-wrap">
                    <div class="cw-wheel">
                        <div class="cw-pin"></div>
                        <canvas id="cw-canvas" width="800" height="800"
                                aria-label="{{ trans('coupon-wheel::web.wheel.aria') }}"
                                data-program="{{ $program->id }}"></canvas>
                        <div class="cw-center">
                            <img src="{{ $frameUrl }}" class="cw-frame" alt="">
                            <button class="cw-btn cw-spin-btn" id="cw-spin-btn"
                                    style="background-image:url('{{ $centerUrl }}');background-size:cover;background-position:center;">
                                {{ trans('coupon-wheel::web.spin.btn') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="cw-badge cw-card" id="cw-chances"></div>
            </div>

            {{-- Cột phải --}}
            <div class="cw-card cw-card--form">

                {{-- Form nhập thông tin --}}
                <div id="cw-form-section">
                    <p class="cw-form-desc">
                        {!! $displayText['popupDescription'] !!}
                    </p>
                    <div class="cw-form">
                        <input type="text" name="antibot" class="cw-antibot" tabindex="-1" autocomplete="off" value="">
                        <div class="cw-field-wrap">
                            <label class="cw-label" for="cw-fullname">
                                <i class="fa-regular fa-user"></i> {{ trans('coupon-wheel::web.form.fullname.label') }}
                            </label>
                            <input class="cw-input" id="cw-fullname" type="text"
                                   placeholder="{{ trans('coupon-wheel::web.form.fullname.placeholder') }}" />
                        </div>
                        @if($showEmail)
                        <div class="cw-field-wrap">
                            <label class="cw-label" for="cw-email">
                                <i class="fa-regular fa-envelope"></i>
                                {{ trans('coupon-wheel::web.form.email.label') }}
                                @if(in_array($contactLimit, ['email','both']))
                                <span class="cw-required">{{ trans('coupon-wheel::web.form.email.required') }}</span>
                                @endif
                            </label>
                            <input class="cw-input" id="cw-email" type="email"
                                   placeholder="{{ trans('coupon-wheel::web.form.email.placeholder') }}" />
                        </div>
                        @endif
                        @if($showPhone)
                        <div class="cw-field-wrap">
                            <label class="cw-label" for="cw-phone">
                                <i class="fa-solid fa-phone"></i>
                                {{ trans('coupon-wheel::web.form.phone.label') }}
                                @if(in_array($contactLimit, ['phone','both']))
                                <span class="cw-required">{{ trans('coupon-wheel::web.form.email.required') }}</span>
                                @endif
                            </label>
                            <input class="cw-input" id="cw-phone" type="tel"
                                   placeholder="{{ trans('coupon-wheel::web.form.phone.placeholder') }}" />
                        </div>
                        @endif
                    </div>
                    <p class="cw-hint">
                        <i class="fa-solid fa-circle-info"></i> {{ trans('coupon-wheel::web.' . $hintKey) }}
                    </p>
                    @if($requireLogin)
                    <p class="cw-login-note">
                        <i class="fa-solid fa-lock"></i>
                        {!! trans('coupon-wheel::web.login.note', [
                            'link' => '<a href="' . \SkillDo\Cms\Support\Url::login() . '">' . trans('coupon-wheel::web.login.link_text') . '</a>'
                        ]) !!}
                    </p>
                    @endif
                </div>

                <div class="cw-toast" id="cw-toast"></div>

                {{-- Kết quả --}}
                <div class="cw-result" id="cw-result">
                    <div class="cw-result-inner" id="cw-result-inner"></div>
                    <button type="button" class="cw-copy-btn" id="cw-copy-btn" style="display:none;">
                        <i class="fa-regular fa-copy"></i> {{ trans('coupon-wheel::web.copy.btn') }}
                    </button>
                </div>

            </div>
        </div>
        <div class="cw-footer">
            {{ trans('coupon-wheel::web.footer') }}
        </div>
    </div>
</div>

<script>
window.CWWheel = {
    showAutoTrigger:   {{ $showAutoTrigger   ? 'true' : 'false' }},
    showButtonTrigger: {{ $showButtonTrigger ? 'true' : 'false' }},
    triggerDelay:  {{ $triggerDelay }},
    ajaxUrl:       typeof ajax !== 'undefined' ? ajax : '/ajax',
    programId:     {{ $program->id }},
    storageKey:    'cw_spun_{{ $program->id }}',
    slices:        {!! json_encode(array_values($slices), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) !!},
    showEmail:     {{ $showEmail ? 'true' : 'false' }},
    showPhone:     {{ $showPhone ? 'true' : 'false' }},
    contactLimit:  '{{ $contactLimit }}',
    i18n: {
        winHeading:      {!! json_encode($displayText['winHeading'])  !!},
        winDescription:  {!! json_encode($displayText['winDescription']) !!},
        loseHeading:     {!! json_encode($displayText['loseHeading']) !!},
        loseDescription: {!! json_encode($displayText['loseDescription']) !!},
        viewProduct:     {!! json_encode(trans('coupon-wheel::web.view_product')) !!},
        emailRequired:   {!! json_encode(trans('coupon-wheel::web.validate.email_required')) !!},
        phoneRequired:   {!! json_encode(trans('coupon-wheel::web.validate.phone_required')) !!},
        bothRequired:    {!! json_encode(trans('coupon-wheel::web.validate.both_required')) !!},
    }
};
</script>
