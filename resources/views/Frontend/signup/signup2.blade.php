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
</style>
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
            <form id="signupForm" method="post" action="{{ route('user.signup.post') }}">
                @csrf
                {{-- {{ below session varibale for campaign signups }} --}}
                <input type="hidden" name="signup_type" value=@if(Session::get('signupType')) {{ "Campaign" }} @else {{ "individual" }} @endif>
              <h1>Create Profile</h1>
              <div class="signup_form__username">
                <h2>Choose a Nick Name</h2>
                <input type="text" placeholder="Choose a Nick Name" name="nickname" />
              </div>
              <div class="signup_form__select">
                <h2>Profile type</h2>
                <select name="user_profile_id">
                  <option>Select profile type</option>
                  @foreach($userProfiles as $userprofile)
                    <option value="{{ $userprofile->id }}" @if(!$userprofile->status) disabled @endif>{{ $userprofile->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="signup_form__age">
                <h2>Age</h2>
                <input type="number" placeholder="Age" name="age" oninput="inputAge()" />
                <div class="sendfull__report__checkbox__input signup_form__age__verifyage">
                  <input class="qcheckbox" type="checkbox" id="confirmage" name="confirmage" onclick="onConfirmAge()">
                  <label for="confirmage"><span class ="parent_approval_label">Parents approval required</span></label>
                  <input type="hidden" name="under_age" value="0" id="under_age">
                  <input type="hidden" name="confirmcodeparent" id="confirmcodeparent">
                  <input type="hidden" name="sessionId" id="sessionId">
                </div>
              </div>
              <div class="signup_form__select">
                <h2>Gender</h2>
                <select name="gender">
                  <option>Select Gender</option>
                  @foreach(config('constants.gender') as $gender)
                    <option value="{{ $gender }}">{{ Str::ucfirst($gender) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="signup_form__username">
                <h2>Username </h2>
                <input type="text" placeholder="Username" name="username" value="{{ Session::get('username') ?? ''}}" @if (Session::get('username')) readonly @endif>
                @if (Session::get('username'))
                  <input type="hidden" name="username" value="{{ Session::get('username') }}" >
                  @endif
              </div>
              <div class="signup_form__password">
                <h2>Password</h2>
                <input type="password" placeholder="Password" name="password" />
              </div>
              <div class="signup_form__password">
                <h2>Confirm Password</h2>
                <input type="password" placeholder="Confirm Password" name="password_confirmation" />
              </div>
              <div class="signup_form__submitbtn">
                <button type="submit">Start HappiLIFE Awareness</button>
              </div>
            </form>
            <p>Click “Login/Continue” to agree to HappyMynd’s
              <a href="{{ route('getTerms') }}">Terms of Service</a>
              and acknowledge that Happimynd’s
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
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection