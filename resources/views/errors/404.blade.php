@extends('layouts.app')

@section('title', 'Happimynd | 404 Not Found')

@section('content')
<div>
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')

  <div class="error404">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7 col-md-7">
          <div class="error404__text">
            <h1>
              404<br>We couldn’t find<br>the page  you’re looking for
            </h1>
            <p>Searching for Metal Health?</p>
            <div >
              <a href="{{ route('user.dashboard') }}">Jump to Dashboard</a>
            </div>
          </div>
        </div>
        <div class="col-lg-5 col-md-5">
          <div class="error404__img d-flex align-items-end">
            <img src="{{ asset('assets/Frontend/images/errors/404.svg') }}" alt="404">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection