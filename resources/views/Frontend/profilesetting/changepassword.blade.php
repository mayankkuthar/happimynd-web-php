@extends('layouts.app')

@section('title', 'Happimynd | Change Password')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.dashboard.header')
  @include('Frontend.includes.popups.commingsoon')
  <div class="signup">
    <div class="dashboard__toast" id="toast">
      <div class="dashboard__toast__text">
        <h1>Password changed successfully</h1>
      </div>
    </div>
    <div class="container">
      <div class="row align-items-center signup__column-reverse">
        <div class="col-lg-7 col-md-6">
          <div class="signup_img">
            <img src="{{ asset('assets/Frontend/images/login_img.svg') }}" >
          </div>
        </div>
        <div class="col-lg-5 col-md-6">
          <div class="signup_form signup_form-create-profile signup_form-create-profile-individual">
            <form id="changePassword" class="edit__profile" method="post" action="{{ route('user.changePassword') }}">
                @csrf
                <input type="hidden" name="signup_type" value="individual">
              <h1>Change Password</h1>
              <div class="signup_form__password">
                <h2>Old Password</h2>
                <input type="password" placeholder="Old Password" name="old_password" />
              </div>
              <div class="signup_form__password">
                <h2>New Password</h2>
                <input type="password" placeholder="New Password" name="password" />
              </div>
              <div class="signup_form__password">
                <h2>Confirm New Password</h2>
                <input type="password" placeholder="New Password" name="password_confirmation" />
              </div>
              <div class="editprofile__submitbtn">
                <button type="submit">Change Password</button>
              </div>
              <div class="signup_form__cancelbtn">
                <button type="button" onclick="window.location.href='{{ url()->previous() }}'">Cancel</button>
              </div>
              <div class="editprofile_form__forgot-pass">
                {{-- <a href="javascript:void(0);" onclick="showCommingSoonPop();">Forgot Password?</a> --}}
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@section('js')
<script>
var onSuccessSubmit = function(data){
$("#toast").addClass("dashboard__toast__show");
setTimeout(()=>{
  $("#toast").removeClass("dashboard__toast__show");
}, 2000);
}
formSubmitAjaxEvent('changePassword', defaultFunction,onSuccessSubmit, defaultFunction);
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
@endsection