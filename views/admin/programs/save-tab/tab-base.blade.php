<div class="row">
    <div class="col-md-8">

        {{-- Thông tin chương trình --}}
        <div class="box mb-3">
            <div class="box-header"><h3 class="box-title">Thông tin chương trình</h3></div>
            <div class="box-content">
                <div class="row">
                    {!! $formInfo->html() !!}
                </div>
            </div>
        </div>

        {{-- Cài đặt hiển thị --}}
        <div class="box mb-3">
            <div class="box-header"><h3 class="box-title">Cài đặt hiển thị</h3></div>
            <div class="box-content">
                <div class="row">
                    {!! $formDisplay->html() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        {{-- Cài đặt điều kiện --}}
        <div class="box mb-3">
            <div class="box-header"><h3 class="box-title">Cài đặt điều kiện</h3></div>
            <div class="box-content">
                <div class="row">
                    {!! $formCondition->html() !!}
                </div>
            </div>
        </div>

    </div>
</div>