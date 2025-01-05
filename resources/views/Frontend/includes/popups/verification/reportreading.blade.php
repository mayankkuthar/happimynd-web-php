<?php
$countries = App\Models\Country::all();
$user = Auth::user();
$isUserFromOrg =  App\Models\UserToken::where('user_id', $user->id)->first();
?>


@if($isUserFromOrg)
<div class="modal fade" id="reportreading" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  @else
  <div class="modal fade" id="reportreading" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    @endif

    <!-- <div class="modal fade" id="reportreading" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->


    <div class="modal-dialog reportreading__popup">
      <div class="modal-content">

        @if($isUserFromOrg)
        <div class="sendfullreport__popup__close" data-dismiss="modal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
        @endif

        <div class="reportreading__popup__content">
          <h1 style="font-size: 1.9rem;">Avail your Awareness Summary Reading</h1>
          <!-- <p>Avail a 30 min Summary Reading session by an Emotional Wellbeing Expert</p> -->
          <p style="padding-bottom: 17px;font-size: 1rem !important;">Verify your phone number to understand your Summary and know the next steps.</p>
          <p style="padding-bottom: 21px;font-size: 1rem !important; ">Please wait while your summary is being processed. You can refresh your screen if download does not start automatically within 3 minutes.</p>


          <form class="reportreading__popup__form" method="post" action="{{route('user.updateMobile')}}" id="update_mobile_popup_form">
            @csrf
            <div class="signup_form__select">
              <h2>Country</h2>
              <select name="country_id">
                <option value="">Select your Country</option>

                @foreach ($countries as $country)
                <option value="{{ $country->id }}" {{ (old('country_id', $user->country_id) == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="reportreading__popup__form__input">
              <h2>Phone</h2>
              @if($user->mobile)
              <input type="tel" name="mobile" placeholder="Phone" require value="{{$user->mobile}}">
              @else
              <input type="tel" name="mobile" placeholder="Phone" require>
              @endif
              <input type="hidden" name="user_id" value="{{ $user->id }}">
            </div>
            <div class="reportreading__popup__form__button">
              <button class="reportreading__popup__form__button__continue" type="submit" id="conitnue_mobile_popup_form" disabled>Continue</button>

              <?php
              $user = Auth::user();
              $user_org_token =  App\Models\UserToken::where('user_id', $user->id)->first();
              // dd($user_org_token); die();
              ?>

              @if($user_org_token)
              <button class="reportreading__popup__form__button__later" type="button" id="later_mobile_popup_form">Later</button>
              @endif


              {{-- <a href="javascript:void(0);">Send me a zoom/meet link instead</a> --}}

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>