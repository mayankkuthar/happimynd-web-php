<?php $curentUrl = $_SERVER['REQUEST_URI'] ?>
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

<header class="main__header dashboard__header">
  <nav class="navbar">
    <div class="container">
      <div class="landingpage__logo">
        <span><a href="{{ route('user.dashboard') }}"><img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" /><span>@yield('pagetitle')</span></a></span>
      </div>
      <div class="dashboard__info">
        @include('Frontend.includes.notification')
        <div class="dashboard__profile">
          <div class="dashboard__profile__content">
            <div class="dropdown d-flex align-items-center">
              <button type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="dashboard__profile__img">
                  <img class="dashboard__profile__content__img" src="{{ asset(config('constants.mediaAssets.userProfilePicture').auth('user')->user()->default_avatar) }}" alt="userimg" >
                </div>
                <p class="dashboard__profile__content__text">
                  <span>{{ auth('user')->user()->nickname }}</span>
                  <span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M6 9L12 15L18 9" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </span>
                </p>
              </button>
              <div class="dashboard__profile__dropdown dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <ul>
                  <li><a href="{{ route('user.editProfileView') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Edit Profile
                  </a></li>
                  @if(auth('user')->user()->hasPendingAssessment() && !Route::is('user.assessment'))
                    <li>
                      <a href="{{ route('user.assessment') }}">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M14.5391 2.19043H6.53906C6.00863 2.19043 5.49992 2.40114 5.12485 2.77622C4.74978 3.15129 4.53906 3.66 4.53906 4.19043V20.1904C4.53906 20.7209 4.74978 21.2296 5.12485 21.6046C5.49992 21.9797 6.00863 22.1904 6.53906 22.1904H18.5391C19.0695 22.1904 19.5782 21.9797 19.9533 21.6046C20.3283 21.2296 20.5391 20.7209 20.5391 20.1904V8.19043L14.5391 2.19043Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M14.5391 2.19043V8.19043H20.5391" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M16.5391 13.1904H8.53906" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M16.5391 17.1904H8.53906" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M10.5391 9.19043H9.53906H8.53906" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Continue Screening
                      </a>
                    </li>
                  @endif
                  <li><a href="{{ route('user.exploreServices') }}">
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M57.9 50.7C59.64 47.67 60.75 44.01 59.43 39.75C57.84 34.59 53.31 30.6 47.91 30.09C39.3 29.25 32.22 36.3 33.06 44.94C33.6 50.31 37.56 54.87 42.72 56.46C47.01 57.78 50.64 56.67 53.67 54.93L61.17 62.43C62.34 63.6 64.2 63.6 65.37 62.43C66.54 61.26 66.54 59.4 65.37 58.23L57.9 50.7ZM46.5 51C42.3 51 39 47.7 39 43.5C39 39.3 42.3 36 46.5 36C50.7 36 54 39.3 54 43.5C54 47.7 50.7 51 46.5 51ZM36 60V66C19.44 66 6 52.56 6 36C6 19.44 19.44 6 36 6C50.52 6 62.61 16.32 65.4 30H59.19C57.27 22.62 51.99 16.59 45 13.77V15C45 18.3 42.3 21 39 21H33V27C33 28.65 31.65 30 30 30H24V36H30V45H27L12.63 30.63C12.24 32.37 12 34.14 12 36C12 49.23 22.77 60 36 60Z" fill="#E5A662"/>
                    </svg>
                    Buy Services
                  </a></li>
                  <li><a href="{{ route('user.changePasswordView') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M19.4 15C19.2669 15.3016 19.2272 15.6362 19.286 15.9606C19.3448 16.285 19.4995 16.5843 19.73 16.82L19.79 16.88C19.976 17.0657 20.1235 17.2863 20.2241 17.5291C20.3248 17.7719 20.3766 18.0322 20.3766 18.295C20.3766 18.5578 20.3248 18.8181 20.2241 19.0609C20.1235 19.3037 19.976 19.5243 19.79 19.71C19.6043 19.896 19.3837 20.0435 19.1409 20.1441C18.8981 20.2448 18.6378 20.2966 18.375 20.2966C18.1122 20.2966 17.8519 20.2448 17.6091 20.1441C17.3663 20.0435 17.1457 19.896 16.96 19.71L16.9 19.65C16.6643 19.4195 16.365 19.2648 16.0406 19.206C15.7162 19.1472 15.3816 19.1869 15.08 19.32C14.7842 19.4468 14.532 19.6572 14.3543 19.9255C14.1766 20.1938 14.0813 20.5082 14.08 20.83V21C14.08 21.5304 13.8693 22.0391 13.4942 22.4142C13.1191 22.7893 12.6104 23 12.08 23C11.5496 23 11.0409 22.7893 10.6658 22.4142C10.2907 22.0391 10.08 21.5304 10.08 21V20.91C10.0723 20.579 9.96512 20.258 9.77251 19.9887C9.5799 19.7194 9.31074 19.5143 9 19.4C8.69838 19.2669 8.36381 19.2272 8.03941 19.286C7.71502 19.3448 7.41568 19.4995 7.18 19.73L7.12 19.79C6.93425 19.976 6.71368 20.1235 6.47088 20.2241C6.22808 20.3248 5.96783 20.3766 5.705 20.3766C5.44217 20.3766 5.18192 20.3248 4.93912 20.2241C4.69632 20.1235 4.47575 19.976 4.29 19.79C4.10405 19.6043 3.95653 19.3837 3.85588 19.1409C3.75523 18.8981 3.70343 18.6378 3.70343 18.375C3.70343 18.1122 3.75523 17.8519 3.85588 17.6091C3.95653 17.3663 4.10405 17.1457 4.29 16.96L4.35 16.9C4.58054 16.6643 4.73519 16.365 4.794 16.0406C4.85282 15.7162 4.81312 15.3816 4.68 15.08C4.55324 14.7842 4.34276 14.532 4.07447 14.3543C3.80618 14.1766 3.49179 14.0813 3.17 14.08H3C2.46957 14.08 1.96086 13.8693 1.58579 13.4942C1.21071 13.1191 1 12.6104 1 12.08C1 11.5496 1.21071 11.0409 1.58579 10.6658C1.96086 10.2907 2.46957 10.08 3 10.08H3.09C3.42099 10.0723 3.742 9.96512 4.0113 9.77251C4.28059 9.5799 4.48572 9.31074 4.6 9C4.73312 8.69838 4.77282 8.36381 4.714 8.03941C4.65519 7.71502 4.50054 7.41568 4.27 7.18L4.21 7.12C4.02405 6.93425 3.87653 6.71368 3.77588 6.47088C3.67523 6.22808 3.62343 5.96783 3.62343 5.705C3.62343 5.44217 3.67523 5.18192 3.77588 4.93912C3.87653 4.69632 4.02405 4.47575 4.21 4.29C4.39575 4.10405 4.61632 3.95653 4.85912 3.85588C5.10192 3.75523 5.36217 3.70343 5.625 3.70343C5.88783 3.70343 6.14808 3.75523 6.39088 3.85588C6.63368 3.95653 6.85425 4.10405 7.04 4.29L7.1 4.35C7.33568 4.58054 7.63502 4.73519 7.95941 4.794C8.28381 4.85282 8.61838 4.81312 8.92 4.68H9C9.29577 4.55324 9.54802 4.34276 9.72569 4.07447C9.90337 3.80618 9.99872 3.49179 10 3.17V3C10 2.46957 10.2107 1.96086 10.5858 1.58579C10.9609 1.21071 11.4696 1 12 1C12.5304 1 13.0391 1.21071 13.4142 1.58579C13.7893 1.96086 14 2.46957 14 3V3.09C14.0013 3.41179 14.0966 3.72618 14.2743 3.99447C14.452 4.26276 14.7042 4.47324 15 4.6C15.3016 4.73312 15.6362 4.77282 15.9606 4.714C16.285 4.65519 16.5843 4.50054 16.82 4.27L16.88 4.21C17.0657 4.02405 17.2863 3.87653 17.5291 3.77588C17.7719 3.67523 18.0322 3.62343 18.295 3.62343C18.5578 3.62343 18.8181 3.67523 19.0609 3.77588C19.3037 3.87653 19.5243 4.02405 19.71 4.21C19.896 4.39575 20.0435 4.61632 20.1441 4.85912C20.2448 5.10192 20.2966 5.36217 20.2966 5.625C20.2966 5.88783 20.2448 6.14808 20.1441 6.39088C20.0435 6.63368 19.896 6.85425 19.71 7.04L19.65 7.1C19.4195 7.33568 19.2648 7.63502 19.206 7.95941C19.1472 8.28381 19.1869 8.61838 19.32 8.92V9C19.4468 9.29577 19.6572 9.54802 19.9255 9.72569C20.1938 9.90337 20.5082 9.99872 20.83 10H21C21.5304 10 22.0391 10.2107 22.4142 10.5858C22.7893 10.9609 23 11.4696 23 12C23 12.5304 22.7893 13.0391 22.4142 13.4142C22.0391 13.7893 21.5304 14 21 14H20.91C20.5882 14.0013 20.2738 14.0966 20.0055 14.2743C19.7372 14.452 19.5268 14.7042 19.4 15V15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Change Password
                  </a></li>
                  <li><a href="{{ route('user.signout') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M16 17L21 12L16 7" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M21 12H9" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Signout
                  </a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="whtsapp-icon">
            <a href='https://wa.me/919136899581' target="blank"><img src="{{ asset('assets/Frontend/images/whatsapp.png') }}" alt='whatsapp-icon'/></a>
          </div>
      </div>
    </div>
  </nav>
</header>