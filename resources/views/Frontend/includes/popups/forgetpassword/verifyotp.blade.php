<div class="modal fade" id="verifyotp" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered sendfullreport__popup">
    <div class="modal-content">
      <div class="sendfullreport__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="sendfullreport__popup__content">
        <h1>Check your Mail</h1>
        <p>Verification code sent on your Mail ID. Use here to reset your password.</p>
        <form class="sendfullreport__popup__form forget__otp_email" id="forget__otp_code_email" method="POST" action="{{ route( 'verifyOtpByCode', ['type' => 'email'] ) }}">
          @csrf
          <div class="sendfullreport__popup__form__input">
            <h2>Code</h2>
            <input type="text" id="otp__email_forget" name="otp" placeholder="Enter your code" required>
            <span id="error-message-forgetpass-otp-email" style="display: none;" class="error-message"></span>
          </div>
          <input type="text" id="username__email_forget" name="username" value="" hidden>
          <div class="sendfullreport__popup__form__button">
            <button type="submit" >Continue</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
