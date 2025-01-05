@extends('layouts.app')

@section('title', 'Happimynd | User Dashboard')
@section('pagetitle', '| Dashboard')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.dashboard.header')
  @include('Frontend.includes.popups.raisequery')
  @include('Frontend.includes.popups.commingsoon')
  @include('Frontend.includes.popups.verification_popups')
  @include('Frontend.includes.popups.modalTemplate')
  @include('Frontend.includes.popups.psychologist_popups')
  <div class="dashboard">
    <div class="container">
      <div class="dashboard__toast" id="raised_query_message">
        <div class="dashboard__toast__text">
          <h1>Query raised, You’ll receive an email once resolved.</h1>
        </div>
      </div>

      <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has( $msg))
        <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
        @endif
        @endforeach
      </div>
      @if($showBlinkingText && $blinkingText)
        <div class="dashboard__marquee">
          <marquee scrollamount="4" >
            <img src="{{ asset('assets/Frontend/images/marquee_gif.gif') }}" alt="marquee_gif">
            &nbsp;
            <font size="+1">
              <a href="{{ route(config('constants.blinkingText.'.$blinkingText.'.link')) ?? '' }}">{{ config('constants.blinkingText.'.$blinkingText.'.text') ?? '' }}</a>
            </font>
          </marquee>
        </div>
      @endif
      <div class="dashboard__headtext">
        <h1>Dashboard</h1>
        <div class="dashboard__headtext__subscribed">
          @if(auth('user')->user()->assessment->count() == 0)
            <a href="{{ route('user.assessment') }}">Start HappiLIFE Awareness</a>
          @elseif(auth('user')->user()->hasPendingAssessment() && !Route::is('user.assessment'))
            <a href="{{ route('user.assessment') }}">Complete HappiLIFE Awareness Tool</a>
          @endif
          <a href="{{ route('user.subscribedServices') }}">My Subscribed Services</a>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
          @if(auth('user')->user()->hasPendingAssessment() && !Route::is('user.assessment'))
          <div class="dashboard__options__padding">
            <a href="{{ route('user.assessment') }}" class="dashboard__options dashboard__options__report">
              <div>
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M43.77 7.77C42.63 6.63 41.1 6 39.51 6H18C14.7 6 12 8.7 12 12V60C12 63.3 14.67 66 17.97 66H54C57.3 66 60 63.3 60 60V26.49C60 24.9 59.37 23.37 58.23 22.26L43.77 7.77ZM45 54H27C25.35 54 24 52.65 24 51C24 49.35 25.35 48 27 48H45C46.65 48 48 49.35 48 51C48 52.65 46.65 54 45 54ZM45 42H27C25.35 42 24 40.65 24 39C24 37.35 25.35 36 27 36H45C46.65 36 48 37.35 48 39C48 40.65 46.65 42 45 42ZM39 24V10.5L55.5 27H42C40.35 27 39 25.65 39 24Z" fill="#60D6C3"/>
                </svg>
              </div>
              <div class="dashboard__options__text">
                <h2>Continue Screening</h2>
                <p>Complete screening to get the HappiMynd Summary.</p>
              </div>
            </a>
          </div>
          @else
          <div class="dashboard__options__padding">
            <a href="{{ route('user.downloadReport') }}" class="dashboard__options dashboard__options__report">
              <div>
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M43.77 7.77C42.63 6.63 41.1 6 39.51 6H18C14.7 6 12 8.7 12 12V60C12 63.3 14.67 66 17.97 66H54C57.3 66 60 63.3 60 60V26.49C60 24.9 59.37 23.37 58.23 22.26L43.77 7.77ZM45 54H27C25.35 54 24 52.65 24 51C24 49.35 25.35 48 27 48H45C46.65 48 48 49.35 48 51C48 52.65 46.65 54 45 54ZM45 42H27C25.35 42 24 40.65 24 39C24 37.35 25.35 36 27 36H45C46.65 36 48 37.35 48 39C48 40.65 46.65 42 45 42ZM39 24V10.5L55.5 27H42C40.35 27 39 25.65 39 24Z" fill="#60D6C3"/>
                </svg>
              </div>
              <div class="dashboard__options__text">
                <h2>Download Summary</h2>
                <p>Available for download until 3 months.</p>
              </div>
            </a>
          </div>
          @endif
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
          <div class="dashboard__options__padding">
            <a href="{{ route('blog') }}" class="dashboard__options dashboard__options__content">
              <div>
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18.54 60C22.1519 60 25.08 57.072 25.08 53.46C25.08 49.8481 22.1519 46.92 18.54 46.92C14.9281 46.92 12 49.8481 12 53.46C12 57.072 14.9281 60 18.54 60Z" fill="#6584DB"/>
                  <path d="M16.77 30.6899C14.25 30.2699 12 32.3399 12 34.8899C12 37.0199 13.59 38.7299 15.69 39.0899C24.45 40.6199 31.35 47.5499 32.91 56.3099C33.27 58.4099 34.98 59.9999 37.11 59.9999C39.66 59.9999 41.73 57.7499 41.34 55.2299C39.3 42.6299 29.37 32.6999 16.77 30.6899ZM16.68 13.5599C14.19 13.2899 12 15.2999 12 17.7899C12 19.9799 13.65 21.7799 15.81 21.9899C33.84 23.7899 48.18 38.1299 49.98 56.1599C50.19 58.3499 51.99 59.9999 54.18 59.9999C56.7 59.9999 58.68 57.8099 58.44 55.3199C56.25 33.2999 38.73 15.7499 16.68 13.5599Z" fill="#6584DB"/>
                </svg>
              </div>
              <div class="dashboard__options__text">
                <h2>See Content</h2>
                <p>Read Blogs, Watch Videos, and listen to Audio.</p>
              </div>
            </a>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
          <div class="dashboard__options__padding">
            <a href="javascript:void(0);" class="dashboard__options dashboard__options__query" onclick="showRaiseQueryPop();">
              <div>
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M36 6C19.44 6 6 19.44 6 36C6 52.56 19.44 66 36 66C52.56 66 66 52.56 66 36C66 19.44 52.56 6 36 6ZM39 57H33V51H39V57ZM45.21 33.75L42.51 36.51C41.01 38.04 39.93 39.42 39.39 41.58C39.15 42.54 39 43.62 39 45H33V43.5C33 42.12 33.24 40.8 33.66 39.57C34.26 37.83 35.25 36.27 36.51 35.01L40.23 31.23C41.61 29.91 42.27 27.93 41.88 25.83C41.49 23.67 39.81 21.84 37.71 21.24C34.38 20.31 31.29 22.2 30.3 25.05C29.94 26.16 29.01 27 27.84 27H26.94C25.2 27 24 25.32 24.48 23.64C25.77 19.23 29.52 15.87 34.17 15.15C38.73 14.43 43.08 16.8 45.78 20.55C49.32 25.44 48.27 30.69 45.21 33.75Z" fill="#EA7097"/>
                </svg>
              </div>
              <diV class="dashboard__options__text">
                <h2>Raise a query</h2>
                <p>Ask any doubt that you have regarding HappiMynd</p>
              </diV>
            </a>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
          <div class="dashboard__options__padding">
            <a href="{{ route('user.exploreServices') }}" class="dashboard__options dashboard__options__services">
              <div>
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M57.9 50.7C59.64 47.67 60.75 44.01 59.43 39.75C57.84 34.59 53.31 30.6 47.91 30.09C39.3 29.25 32.22 36.3 33.06 44.94C33.6 50.31 37.56 54.87 42.72 56.46C47.01 57.78 50.64 56.67 53.67 54.93L61.17 62.43C62.34 63.6 64.2 63.6 65.37 62.43C66.54 61.26 66.54 59.4 65.37 58.23L57.9 50.7ZM46.5 51C42.3 51 39 47.7 39 43.5C39 39.3 42.3 36 46.5 36C50.7 36 54 39.3 54 43.5C54 47.7 50.7 51 46.5 51ZM36 60V66C19.44 66 6 52.56 6 36C6 19.44 19.44 6 36 6C50.52 6 62.61 16.32 65.4 30H59.19C57.27 22.62 51.99 16.59 45 13.77V15C45 18.3 42.3 21 39 21H33V27C33 28.65 31.65 30 30 30H24V36H30V45H27L12.63 30.63C12.24 32.37 12 34.14 12 36C12 49.23 22.77 60 36 60Z" fill="#E5A662"/>
                </svg>
              </div>
              <div class="dashboard__options__text">
                <h2>Explore Services</h2>
                <p>Explore more of our services.</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="blog-articles">
    <div class="container">
      {{-- <div class="blog-articles__heading">
        <h1>Latest Blog Articles</h1>
        <a href="javascript:void(0);" onclick="showCommingSoonPop();">See More Blogs</a>
      </div> --}}
      {{-- <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6">
          <div class="blog-articles_content">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-4">
                <div>
                  <img src="{{ asset('assets/Frontend/images/profile_blog_img1.webp') }}" >
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-8 blog-articles_content_text_mob-padding">
                <div>
                  <h2>Fundraising fraud: How to make sure your money supports the right cause</h2>
                  <p>23 July 2020</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6">
          <div class="blog-articles_content">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-4">
                <div>
                  <img src="{{ asset('assets/Frontend/images/profile_blog_img2.webp') }}" >
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-8 blog-articles_content_text_mob-padding">
                <div>
                  <h2>Fundraising fraud: How to make sure your money supports the right cause</h2>
                  <p>23 July 2020</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6">
          <div class="blog-articles_content">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-4">
                <div>
                  <img src="{{ asset('assets/Frontend/images/profile_blog_img3.webp') }}" >
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-8 blog-articles_content_text_mob-padding">
                <div>
                  <h2>Fundraising fraud: How to make sure your money supports the right cause</h2>
                  <p>23 July 2020</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
    </div>
  </div>
  <div class="profile__app-download">
    <div class="container">
      <div class="profile__app-download__content">
        <a href="{{ $hyperlink ?? '' }}"> <img src="{{ $dashboardPic }}"></a>
      </div>
    </div>
  </div>
</div>







@endsection

@section('js')

<!-- Raise Query -->
<script>
    @if(Session::has('popup'))
      sessionStorage.setItem("{{ Session::get('popup') }}", true)
    @endif
  var selected=false;
  $("#query_message").keyup(()=>{
    if($("#query_message").val().trim()!="" && selected){
      $("#btn_submit_raised_query").prop('disabled',false);
    }else{
      $("#btn_submit_raised_query").prop('disabled',true);
    }
  });
  $("#category").change(()=>{
    selected = ($("#category").val()!="")?true:false;
    if($("#query_message").val().trim()!="" && selected){
      $("#btn_submit_raised_query").prop('disabled',false);
    }else{
      $("#btn_submit_raised_query").prop('disabled',true);
    }
  })

  onSuccessSubmit = function(data){
    $('#raisequery').modal('toggle');
    $("#raised_query_message").addClass("dashboard__toast__show");
    setTimeout(()=>{
      $("#raised_query_message").removeClass("dashboard__toast__show");
    }, 2000);

    $("#raised_query_form").trigger("reset");
  }

  beforeSuccessSubmit = function(){
    $("#btn_submit_raised_query").prop('disabled',true);
  }
  formSubmitAjaxEvent("raised_query_form", beforeSuccessSubmit, onSuccessSubmit);
</script>
<!-- Verification -->
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>







@endsection('js')
