<x-backend-layout>
  <x-slot name="title">
    Code Expiry Settings
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Code Expiry Settings</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                @include('Backend.includes.flash_message')
                <p>When a code is created without an expiry date, it expires after the number of days set below.
                  Changing a value only affects codes created afterwards; existing codes keep their current expiry.</p>
                <br>
                <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.codeExpirySettings.update') }}">
                  @csrf
                  @if($canEditToken)
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3" for="token_days">HappiMynd code validity (days)<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9">
                      <input type="number" class="form-control" id="token_days" name="token_days" min="1" max="3650" required value="{{ old('token_days', $tokenDays) }}">
                      @error('token_days')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  @endif
                  @if($canEditCoupon)
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3" for="coupon_days">Coupon code validity (days)<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9">
                      <input type="number" class="form-control" id="coupon_days" name="coupon_days" min="1" max="3650" required value="{{ old('coupon_days', $couponDays) }}">
                      @error('coupon_days')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  @endif
                  <div class="ln_solid"></div>
                  <div class="form-group">
                    <div class="col-md-9 col-sm-9 offset-md-3">
                      <button type="submit" class="btn btn-success">Save</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
