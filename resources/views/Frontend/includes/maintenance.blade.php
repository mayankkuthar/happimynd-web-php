@extends('layouts.app')

@section('title', 'Happimynd | Maintenance Mode')

@section('content')
<div>
  <header class="main__header">
    <nav class="navbar">
      <div class="container">
        <div class="landingpage__logo">
          <a href="{{ url('/') }}" title="Click on logo to go to home"><img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" alt="HappiMynd" /></a>
        </div>
      </div>
    </nav>
  </header>

  <div class="error500">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-6">
          <div class="error500__text maintenance_text">
            <h1>
              The site is currently <br>down for maintenance
            </h1>
            <p>Site is down for maintenance! We’ll be up and <br>running shortly.</p>
            <div >
              <a href="{{ route('user.dashboard') }}">Try again</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="error500__img d-flex align-items-end">
            <img src="{{ asset('assets/Frontend/images/errors/maintenance.svg') }}" alt="maintenance">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection