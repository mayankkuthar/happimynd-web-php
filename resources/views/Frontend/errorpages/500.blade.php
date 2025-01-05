@extends('layouts.app')

@section('title', 'Happimynd | 500 Not Found')

@section('content')
<div>
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')

  <div class="error500">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-6">
          <div class="error500__text">
            <h1>
              Well!<br>This is unexpected
            </h1>
            <p>Error code: 500<br><br>
              An Error has occurred and we’re working to fix the problem! We’ll be up and running shortly.</p>
            <div >
              <a href="{{ route('user.dashboard') }}">Jump to Dashboard</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="error500__img d-flex align-items-end">
            <img src="{{ asset('assets/Frontend/images/errors/500.svg') }}" alt="500">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection