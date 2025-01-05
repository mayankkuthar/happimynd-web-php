<div class="modal fade" id="confirmparentcode" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog confirmcode__popup">
    <div class="modal-content">
      <div class="confirmcode__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="confirmcode__popup__content">
        <h1>Parent’s authorisation</h1>
        <p>Please use 6 digit code received on your parent’s mail ID.
          <br>
          This code is a token of authorisation from your parent to proceed with your HappiLIFE Screening
        </p>
        <form class="confirmcode__popup__form" method="post" id="confirmcode_popup_form">
          @csrf
          <div class="confirmcode__popup__form__input">
            <h2>Confirmation code</h2>
            <input type="text" name="otp" id ="otp" placeholder="6-digit code" required>
          </div>
          <div class="confirmcode__popup__form__button">
            <button class="confirmcode__popup__form__button__continue" type="button" onclick="verifyOtp('email')">Verify</button>
            <a href="javascript:void(0)" onclick="generateOTP();">Resend code</a>
            {{-- <span>0.54</span> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>