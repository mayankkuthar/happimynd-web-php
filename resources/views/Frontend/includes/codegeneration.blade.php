@extends('layouts.app')

@section('title', 'Happimynd | Code Generation')

@section('content')
<div>
  @include('Frontend.includes.dashboard.header')
  @include('Frontend.includes.popups.commingsoon')
  <div class="modal fade" id="alreadypaid" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog alreadypaid__popup">
      <div class="modal-content">
        <div class="alreadypaid__popup__close" data-dismiss="modal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <diV class="alreadypaid__popup__content">
          <h1>Already Paid! 🎉</h1>
          <p>HappiApp Code is a paid feature which let you use the Thrive app. Your corporation paid for this feature for you. HappiApp Code can be generated one time only, So make sure you don’t lose this code.</p>
          <button type="button" data-dismiss="modal">Yes, I Understood</button>
        </diV>
      </div>
    </div>
  </div>
  {{-- <div class="alreadypaid__popup_overlay show" onclick="alreadyClosePop();"></div>
  <div class="alreadypaid__popup show">
    <div class="alreadypaid__popup__close" onclick="alreadyClosePop();">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <diV class="alreadypaid__popup__content">
      <h1>Already Paid! 🎉</h1>
      <p>HappiApp Code is a paid feature which let you use the Thrive app. Your corporation paid for this feature for you. HappiApp Code can be generated one time only, So make sure you don’t lose this code.</p>
      <button type="button" onclick="alreadyClosePop();">Yes, I Understood</button>
    </diV>
  </div> --}}
  <div class="thrive__code">
    <div class="container position-relative">
      <div class="thrive__code__content-code">
        <h1>Congratulations! 🎉</h1>
        <h1>Here is your HappiAPP Code</h1>
        @foreach ($codes as $code)
            <input type="text" id="thrivecode-{{$loop->iteration }}" value="{{ $code->code }}" readonly>
            <p class="mb-2" onclick="copyThriveCode('thrivecode-{{$loop->iteration}}');">Tab to copy</p>
        @endforeach

        @if ($getNewCodeBtn)
            <div class="thrive__code__content-download-appbtn">
                <h5>(Remaining: {{$code_left}})</h5>
                <a href="{{ route('user.getThriveCode') }}">Avail your HappiApp Code</a>
            </div>
        @endif

        @if ($no_code_available)
        <div class="thrive__code__content-download-appbtn">
            <h5>{{$no_code_available}}</h5>
        </div>
        @endif
      </div>

      <div class="thrive__code__content-steps">
        <h1>Next Steps</h1>
        <p><span>1.</span> Copy the code</p>
        <p><span>2.</span> Download "Thrive: Mental Wellbeing" <br>from Apple or Android App store</p>
        <p><span>3.</span> Register using HappiAPP Code (HappiApp Code)</p>
        <p><span>4.</span> Enjoy the app & be Happi!</p>
        {{-- <div class="thrive__code__content-download-appbtn">
          <a href="javascript:void(0);">Download Thrive APP</a>
        </div> --}}
        <div class="app__download_link d-flex align-items-center justify-content-center">
          <a class="app__download_link__app_store" href="https://apps.apple.com/gb/app/thrive-mental-wellbeing/id1048928580" target="_blank" rel="noreferrer noopener">
            <img src="{{ asset('assets/Frontend/images/app_store.png') }}" />
          </a>
          <a class="app__download_link__play_store" href="https://play.google.com/store/apps/details?id=com.help.stressfree&hl=en_IN&gl=US" target="_blank" rel="noreferrer noopener">
            <img src="{{ asset('assets/Frontend/images/play_store.png') }}" />
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
