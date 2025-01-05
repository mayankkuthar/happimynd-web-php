@extends('layouts.app')

@section('title', 'Happimynd | 401 Not Found')

@section('content')
<div>
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')

  <div class="error401">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-6">
          <div class="error401__text">
            <h1>
              401<br>Unauthorized Access
            </h1>
            <p>You don’t have access to this feature. Please verify if you think this is a mistake.</p>
            <div >
              <a href="{{ route('user.dashboard') }}">Jump to Dashboard</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="error401__img d-flex align-items-end">
            <img src="{{ asset('assets/Frontend/images/errors/401.svg') }}" alt="401">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection