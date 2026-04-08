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
      <div class="row align-items-center signup__column-reverse">
        <div class="col-lg-7 col-md-6">
          <div class="signup_img">
            <img src="{{ asset('assets/Frontend/images/login_img.png') }}" >
          </div>
        </div>
        <div class="col-lg-5 col-md-6">
          <div class="signup_options">
            <h1>Get Started</h1>
            <button type="button"><a href="{{ route('sponserSignupView') }}">Organization/Institution</a></button>
            <button type="button"><a href="{{ route('user.individualSignupView') }}">Individual</a></button>

            <button type="button"><a href="{{ route('sponserSignupView') }}">School/University/Institute</a></button>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<div class="modal fade" id="sendFurtherIntruction" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        <h1>Get the App Now</h1>

        <div class="sendfull__report__checkbox__input">
          <input class="qcheckbox" type="checkbox" id="coupon" name="coupon" checked disabled>
          <label for="coupon"><span style="font-size: 15px;">HappiMynd brings the power of emotional self help in your hands.</span></label>
        </div>

        <div class="sendfull__report__checkbox__input">
          <input class="qcheckbox" type="checkbox" id="coupon" name="coupon" checked disabled>
          <label for="coupon"><span style="font-size: 15px;">Download our app now for a seamless experience.</span></label>
        </div>


          <div class="sendfullreport__popup__form__input" style="display: inline-flex;margin-top: 25px;">
            <a href="https://play.google.com/store/apps/details?id=com.happimynd">
              <img src="{{ asset('assets/Frontend/images/play_store.png') }}" style="height: 50px; margin-bottom: 15px;margin-right: 15px;">
            </a>
            <a href="https://apps.apple.com/in/app/happimynd-emotional-self-help/id1634742782">
              <img src="{{ asset('assets/Frontend/images/app_store.png') }}" style="height: 50px;">
            </a>
          </div>
      </div>
    </div>
  </div>
</div>




<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>


<script type="text/javascript">
  $(document).ready(function(){
    $("#sendFurtherIntruction").modal('show');
  })
</script>

@endsection
