@extends('layouts.app')

@section('title', 'Happimynd | Terms of Services')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  <header>
    <nav class="navbar terms__navbar">
      <div class="container">
        <div class="landingpage__logo">
          <a href="{{ url('/') }}" title="Click on logo to go to home"><img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" />| Terms of Services</a>
        </div>
        <div class="download__terms">
          <a href="https://happimynd.s3.ap-south-1.amazonaws.com/assets/static+assets/HappiMynd_Terms+of+Services_download.pdf" target="_blank">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99992 6.3335H11.0599C11.6533 6.3335 11.9466 7.0535 11.5266 7.4735L8.46659 10.5335C8.20659 10.7935 7.78659 10.7935 7.52659 10.5335L4.46658 7.4735C4.04658 7.0535 4.34658 6.3335 4.93992 6.3335H5.99992V3.00016C5.99992 2.6335 6.29992 2.3335 6.66658 2.3335H9.33325C9.69992 2.3335 9.99992 2.6335 9.99992 3.00016V6.3335ZM3.99992 13.6668C3.63325 13.6668 3.33325 13.3668 3.33325 13.0002C3.33325 12.6335 3.63325 12.3335 3.99992 12.3335H11.9999C12.3666 12.3335 12.6666 12.6335 12.6666 13.0002C12.6666 13.3668 12.3666 13.6668 11.9999 13.6668H3.99992Z" fill="#233A51"/>
            </svg>
            Download Terms of Services
          </a>
        </div>
      </div>
    </nav>
  </header>
  <div class="terms__content">
    <div class="container">
      @isset($dataContent)
      <div class="row">
        <div class="col-lg-4 col-md-4">
          <div class="terms__content__options">
            <ul>
              @foreach($dataContent->content as $content)
              <li><a class="" href="{{ route('getTerms') }}#{{ $content->title }}">{{ $loop->iteration }}. {{ $content->title }}</a></li>
              @endforeach
            </ul>
          </div>
        </div>
        <div class="col-lg-8 col-md-8">
          <div class="terms__content__detail">
            @foreach($dataContent->content as $content)
            <h1 id="{{ $content->title }}">{{ $loop->iteration }}. {{ $content->title }}</h1>
            {!! $content->content !!}
            @endforeach
          </div>
        </div>
      </div>
      @endisset
    </div>
  </div>
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
