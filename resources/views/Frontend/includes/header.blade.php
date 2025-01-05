<?php $curentUrl = $_SERVER['REQUEST_URI'] ?>
<style>
.whtsapp-icon {
    max-width: 40px;
    height: 40px;
    margin-left: 20px;
}
.whtsapp-icon a {
    background: transparent !important;
    padding: 0 !important;
}
.whtsapp-icon img {
    width: 100%;
    height: 100%;
}

</style>
<header class="main__header">
  <nav class="navbar">
    <div class="container">
      <div class="landingpage__logo">
        <a href="{{ url('/') }}" title="Click on logo to go to home"><img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" alt="HappiMynd" /></a>
      </div>
      <div class="landingpage__navigation-menu">
        <div class="menubar__close" ><img onclick="showMenuBar()" src="{{ asset('assets/Frontend/images/close_white.svg') }}" ></div>
        <ul class="landingpage__navigation-menu--list">
          <li><a @if($curentUrl == '/services') class="active" @else class="" @endif href="{{ route('services') }}" >Individual Services</a></li>
          <li><a @if($curentUrl == '/organisation') class="active" @else class="" @endif href="{{ route('organisation') }}" >Corporate Services</a></li>
          {{-- <li><a @if($curentUrl == '/educationalservices') class="active" @else class="" @endif href="{{ route('educationalservices') }}">Educational Services</a></li>
          <li><a @if($curentUrl == '/otherservices') class="active" @else class="" @endif href="{{ route('otherservices') }}">Other Services</a></li> --}}
          @guest('user')
          <li class="login__signup1"><a id="happilife_login" href="{{ route('user.loginView') }}">Login</a></li>
          <li class="login__signup2"><a id="happilife_signup" href="{{ route('user.signupView') }}">Signup</a></li>
          @endguest
          @auth('user')
          <li><a id="view_dashboard" class="viewdashboard" href="{{ route('user.dashboard') }}" >View Dashboard</a></li>
          @endauth
        </ul>
      </div>
      <div class="d-flex align-items-center">
        <div class="landingpage__login-signup ">
          @guest('user')
            <a id="happilife_login1" href="{{ route('user.loginView') }}">Login</a>
            <a id="happilife_signup1" href="{{ route('user.signupView') }}">Signup</a>
        @endguest
        @auth('user')<a id="view_dashboard1" class="viewdashboard" href="{{ route('user.dashboard') }}" >View Dashboard</a> @endauth
        
      </div>
      <div class="whtsapp-icon">
            <a href='https://wa.me/919136899581' target="blank"><img src="{{ asset('assets/Frontend/images/whatsapp.png') }}" alt='whatsapp-icon'/></a>
          </div>
      </div>
      
      <div class="menubar-overlay" onclick="showMenuBar()"></div>
      <div class="header__navbar__menu--btn" onclick="showMenuBar()">
        <div class="header__navbar__menu--btn__icon"></div>
      </div>
    </div>
  </nav>
</header>
