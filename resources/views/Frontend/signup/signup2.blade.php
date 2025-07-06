@extends('layouts.app')

@section('title', 'Happimynd | Signup')
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
.recaptcha-container {
    margin: 20px 0;
    display: flex;
    justify-content: center;
}
.recaptcha-error {
    color: #dc3545;
    font-size: 14px;
    text-align: center;
    margin-top: 10px;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    padding: 10px;
}
</style>

<!-- Include Google reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div id="container1">
  @include('Frontend.includes.popups.ageverification.sendotp')
  @include('Frontend.includes.popups.ageverification.emailinput')
  @include('Frontend.includes.popups.ageverification.phoneinput')
  @include('Frontend.includes.popups.ageverification.confirmcode')
  <header class="main__header">
    <nav class="navbar">
      <div class="container">
        <div class="landingpage__logo">
          <a href="{{ url('/') }}"><img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" /></a>
        </div>
        <div class="landingpage__signup d-flex align-items-center">
          <p>Already have an account?<a href="{{ route('user.loginView') }}">Login</a></p>
          <div class="whtsapp-icon">
            <a href='https://wa.me/919136899581' target="blank"><img src="{{ asset('assets/Frontend/images/whatsapp.png') }}" alt='whatsapp-icon'/></a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <div class="signup">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7 col-md-6">
          <div class="signup_img">
            <img src="{{ asset('assets/Frontend/images/login_img.png') }}" >
          </div>
        </div>
        <div class="col-lg-5 col-md-6">
          <div class="signup_form signup_form-create-profile signup_form-create-profile-individual">
            
            {{-- Display general error messages --}}
            @if(session('error'))
              <div class="alert alert-danger" style="margin-bottom: 20px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                {{ session('error') }}
              </div>
            @endif

            @if(session('success'))
              <div class="alert alert-success" style="margin-bottom: 20px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
                {{ session('success') }}
              </div>
            @endif

            <form id="signupForm" method="post" action="{{ route('user.signup.post') }}">
                @csrf
                {{-- {{ below session varibale for campaign signups }} --}}
                <input type="hidden" name="signup_type" value=@if(Session::get('signupType')) {{ "Campaign" }} @else {{ "individual" }} @endif>
              
              <h1>Create Profile</h1>
              
              <div class="signup_form__username">
                <h2>Choose a Nick Name</h2>
                <input type="text" placeholder="Choose a Nick Name" name="nickname" value="{{ old('nickname') }}" />
                @error('nickname')
                  <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="signup_form__select">
                <h2>Profile type</h2>
                <select name="user_profile_id">
                  <option value="">Select profile type</option>
                  @foreach($userProfiles as $userprofile)
                    <option value="{{ $userprofile->id }}" 
                      @if(old('user_profile_id') == $userprofile->id) selected @endif
                      @if(!$userprofile->status) disabled @endif>
                      {{ $userprofile->name }}
                    </option>
                  @endforeach
                </select>
                @error('user_profile_id')
                  <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="signup_form__age">
                <h2>Age</h2>
                <input type="number" placeholder="Age" name="age" value="{{ old('age') }}" oninput="inputAge()" />
                @error('age')
                  <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
                <div class="sendfull__report__checkbox__input signup_form__age__verifyage">
                  <input class="qcheckbox" type="checkbox" id="confirmage" name="confirmage" onclick="onConfirmAge()" @if(old('confirmage')) checked @endif>
                  <label for="confirmage"><span class ="parent_approval_label">Parents approval required</span></label>
                  <input type="hidden" name="under_age" value="{{ old('under_age', '0') }}" id="under_age">
                  <input type="hidden" name="confirmcodeparent" id="confirmcodeparent" value="{{ old('confirmcodeparent') }}">
                  <input type="hidden" name="sessionId" id="sessionId" value="{{ old('sessionId') }}">
                </div>
              </div>
              
              <div class="signup_form__select">
                <h2>Gender</h2>
                <select name="gender">
                  <option value="">Select Gender</option>
                  @foreach(config('constants.gender') as $gender)
                    <option value="{{ $gender }}" @if(old('gender') == $gender) selected @endif>
                      {{ Str::ucfirst($gender) }}
                    </option>
                  @endforeach
                </select>
                @error('gender')
                  <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="signup_form__username">
                <h2>Username </h2>
                <input type="text" placeholder="Username" name="username" 
                  value="{{ Session::get('username') ?? old('username') }}" 
                  @if (Session::get('username')) readonly @endif>
                @if (Session::get('username'))
                  <input type="hidden" name="username" value="{{ Session::get('username') }}" >
                @endif
                @error('username')
                  <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="signup_form__password">
                <h2>Password</h2>
                <input type="password" placeholder="Password" name="password" />
                @error('password')
                  <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="signup_form__password">
                <h2>Confirm Password</h2>
                <input type="password" placeholder="Confirm Password" name="password_confirmation" />
              </div>
              
              <!-- reCAPTCHA widget -->
              <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
              </div>
              
              <!-- Display reCAPTCHA error if exists -->
              @if ($errors->has('g-recaptcha-response'))
                <div class="recaptcha-error">
                  {{ $errors->first('g-recaptcha-response') }}
                </div>
              @endif
              
              <div class="signup_form__submitbtn">
                <button type="submit" id="submitBtn">Start HappiLIFE Awareness</button>
              </div>
            </form>
            <p>Click "Login/Continue" to agree to HappyMynd's
              <a href="{{ route('getTerms') }}">Terms of Service</a>
              and acknowledge that Happimynd's
              <a href="{{ route('privacy') }}">Privacy Policy</a> applies to you.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

<script src="{{ asset('assets/Frontend/js/verify-popups.js') }}"></script>

@section('js')
<script>
  /** Method for verifying OTP modal opens */
  function verifyOtp(type){
      var otp = $("#otp").val();
      var session_id = $("#sessionId").val();
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
      $.ajax({
        type: 'GET',
        url: "{{ route('verifyGuardianOtpByCode') }}",
        data: {session_id:session_id, otp:otp},
        success: function (data) {
          if(data['error'] == false){
            $('#confirmparentcode').modal('toggle');
            $('#under_age').val('1')
            $('.parent_approval_label').css("color","#3C92C6");
            $('#confirmage').prop("checked", "true");
            showToast(data['message']);
          }else{
            showToast(data['message']);
          }
        },
        error:function(data){
          var error = data.responseJSON
          showToast(error['message'])
        }
      });
  }

  function generateOTP(){
    var url = "";
    var otpType = $('#confirmcodeparent').val()
    const otpMessage = "Confirmation code has been sent to your ";
    if(otpType=="email") url = "{{ route('generateGuardianOTP', ['type'=>'email'])}}";
    else if(otpType=="mobile") url = "{{ route('generateGuardianOTP', ['type'=>'mobile'])}}";

    if(url!=""){
      $.ajax({
          type: "get",
          url: url,
          success: function (data) {
            showToast(otpMessage+otpType);
          },
          error:function(data){
            var error = data.responseJSON
            showToast(error['message'])
          }
      });
    }
  }

  // Form submission validation to ensure reCAPTCHA is completed
  document.getElementById('signupForm').addEventListener('submit', function(e) {
    var recaptchaResponse = grecaptcha.getResponse();
    if (recaptchaResponse.length === 0) {
      e.preventDefault();
      alert('Please complete the reCAPTCHA verification.');
      return false;
    }
  });

  // Function to show toast messages (if you have a toast function)
  function showToast(message) {
    // Implement your toast notification here
    // For now, using alert as fallback
    alert(message);
  }
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection