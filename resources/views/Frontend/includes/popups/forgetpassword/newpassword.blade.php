<div class="modal fade" id="newpassword" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        <h1>Create new password</h1>
        <form class="sendfullreport__popup__form error_forget__password_reset_form" id="forget__password_reset_form" action="{{ route('forgetPasswordReset') }}" method="POST">
          @csrf
          <div class="sendfullreport__popup__form__input">
            <h2>New Password</h2>
            <input type="text" name="password1" id="password1" placeholder="New Password">
            <span id="error-password1" style="display: none;" class="error-message"></span>
          </div>
          <div class="sendfullreport__popup__form__input">
            <h2>Confirm Password</h2>
            <input type="text" name="password2" id="password2" placeholder="Confirm Password">
            <span id="error-password2" style="display: none;" class="error-message"></span>
          </div>
          <input type="text" id="username__forget_password_reset" name="username" value="" hidden>
          <div class="sendfullreport__popup__form__button">
            <button type="submit" >Continue</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
