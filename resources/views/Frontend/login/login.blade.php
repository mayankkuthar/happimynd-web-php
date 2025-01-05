@extends('layouts.app')

@section('title', 'Happimynd | Login')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')
@section('content')
<style>
.whtsapp-icon {
    max-width: 40px;
    height: 40px;
    margin-left: 20px;
}
.whtsapp-icon img {
    width: 100%;
    height: 100%;
}
</style>
<div id="container1">
  <header class="main__header">
    <nav class="navbar">
      <div class="container">
        <div class="landingpage__logo">
          <a href="{{ url('/') }}"><img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" /></a>
        </div>
        <div class="landingpage__login d-flex align-items-center">
          <p>Don’t have an account? <a href="{{ route('user.signupView') }}">Create one</a></p>
          <!-- whatsapp icon  -->
          <div class="whtsapp-icon">
            <a href='https://wa.me/919136899581' target="blank"><img src="{{ asset('assets/Frontend/images/whatsapp.png') }}" alt='whatsapp-icon'/></a>
          </div>
        </div>
      </div>
    </nav>
  </header>
  <div class="login">
    <div class="container">
      <div class="row">
        <div class="col-lg-7 col-md-6">
          <div class="login_img">
            <img src="{{ asset('assets/Frontend/images/login_img.png') }}" >
          </div>
        </div>
        <div class="col-lg-5 col-md-6">
            <input id="entered_username_forgetpass" value="" style="display: none">
          <div class="login_form">
            <form method="post" action="{{ route('user.login.post') }}" id="signinForm">
                @csrf
              <h1>Welcome</h1>
              <div class="login_form__username">
                <h2>Username</h2>
                <input type="text" placeholder="Username" name="username" />
              </div>
              <div class="login_form__password">
                <h2>Password</h2>
                <input type="password" placeholder="Password" name="password"  />
              </div>
              <span id="invalidCredentials" class="error-message"></span>
              <div class="login_form__submitbtn">
                <button type="submit">Login to HappiMynd</button>
                <div class="forget__password">
                  <button type="button" onclick="showForgetPassPop()">Forgot password?</button>
                </div>
              </div>
            </form>
            <p>Click “Login/Continue” to agree to HappyMynd’s
              <a href="{{ route('getTerms') }}">Terms of Service</a> and acknowledge that Happimynd’s <a href="{{ route('privacy') }}">Privacy Policy</a> applies to you.</p>
          </div>
        </div>
      </div>
    </div>

    @include('Frontend.includes.popups.forgetpassword.usercheck')
    @include('Frontend.includes.popups.forgetpassword.verifyotp')
    @include('Frontend.includes.popups.forgetpassword.verifyotpmobile')
    @include('Frontend.includes.popups.forgetpassword.sendotp')
    @include('Frontend.includes.popups.forgetpassword.newpassword')

  </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
  signinSuccessCallback = function(data) {
      if (data.error) {
        $('#invalidCredentials').text('Invalid username/password. please try again');
      }
      else{
        window.location = data.message.route
      }
  }
  formSubmitAjaxEvent('signinForm', defaultFunction, signinSuccessCallback, defaultFunction);

  function showVerifyOtpPop() {
    $(".modal").modal('hide');
    var username = $('#entered_username_forgetpass').val();
    // var base_url = window.location.origin;
    // var url = `${ base_url }/generate-otp-email/${username}`;
    // var url = "{{route('generateOTP', ['type'=>'email'])}}"+"?username"+username;
    $.ajax({
        type: 'GET',
        url: "{{ route('generateOTP', ['type'=>'email']) }}"+"?username="+username,
        success: function (data) {
            console.log(data);
        }
    });
    $("#verifyotp").modal('show');
  }

  function showVerifyOtpMobilePop() {
    $(".modal").modal('hide');
    var username = $('#entered_username_forgetpass').val();
    $.ajax({
        type: 'GET',
        url: "{{ route('generateOTP', ['type'=>'mobile']) }}"+"?username="+username,
        success: function (data) {
            console.log(data);
        }
    });
    $("#verifyotpmobile").modal('show');
  }
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
