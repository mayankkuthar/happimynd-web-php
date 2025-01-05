@extends('layouts.app')

@section('title', 'Happimynd | Report Preview')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div>
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.loginsuccesspopup')
  @include('Frontend.includes.popups.commingsoon')
  <div class="container">

  <div class="reportreview__popup__content text-align-center mt-2">
    <div class="reportreview__popup__content__reportcontent">
      @php $loopVariable = 0; @endphp
      @foreach($score as $name => $s)
        @if($loopVariable == 6 ) @php $loopVariable=0 @endphp  @break @endif
        @php $loopVariable++ @endphp
        <div class="@if($loopVariable != 1) reportreview__popup__content__reportcontent__text__blured position-relative @else reportreview__popup__content__reportcontent__text @endif">
          <div class="reportreview__popup__content__reportcontent__text">
            <h1>{{ $s['category_in_report'] }}@isset($s['picture'])<img hight="65px" width="65px" src="{{ $s['picture'] }}" />@endif</h1>
            <span>{!! $s['summary'] ?? ''!!}</span></div>
          <div class="reportreview__popup__content__reportcontent__text__blured__lock">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M25.3333 14.6665H6.66667C5.19391 14.6665 4 15.8604 4 17.3332V26.6665C4 28.1393 5.19391 29.3332 6.66667 29.3332H25.3333C26.8061 29.3332 28 28.1393 28 26.6665V17.3332C28 15.8604 26.8061 14.6665 25.3333 14.6665Z" stroke="#49516A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9.33301 14.6665V9.33317C9.33301 7.56506 10.0354 5.86937 11.2856 4.61912C12.5359 3.36888 14.2316 2.6665 15.9997 2.6665C17.7678 2.6665 19.4635 3.36888 20.7137 4.61912C21.964 5.86937 22.6663 7.56506 22.6663 9.33317V14.6665" stroke="#49516A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
        @php array_shift($score) @endphp
      @endforeach
      <div class="reportreview__popup__content__reportcontent__text__blured position-relative">
        <div class="reportreview__popup__content__reportcontent__text">
          <div class="report__content__summary__wheel report__content__summary__wheel--preview">
            <div class="report__content__summary__wheel__img report__content__summary__wheel__img--preview">
              <div id="wheeloflife" class="wheeloflife__preview">
                <img id="wheeloflife__background" src="{{ asset('assets/Frontend/images/wheel/wheel_of_life.png') }}" alt="Wheel Of Life">
                <div id="selfesteem" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="happiness" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="resilience" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="jobsatisfaction" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="burnout" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="stress" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="anxiety" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="internetaddiction" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
                <div id="depression" class="wheeloflife__fill">
                  <svg viewBox="0 0 72 111">
                    <path d="M0 26.3L72 0v111L0 26.3z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="reportreview__popup__content__reportcontent__text__blured__lock">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M25.3333 14.6665H6.66667C5.19391 14.6665 4 15.8604 4 17.3332V26.6665C4 28.1393 5.19391 29.3332 6.66667 29.3332H25.3333C26.8061 29.3332 28 28.1393 28 26.6665V17.3332C28 15.8604 26.8061 14.6665 25.3333 14.6665Z" stroke="#49516A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.33301 14.6665V9.33317C9.33301 7.56506 10.0354 5.86937 11.2856 4.61912C12.5359 3.36888 14.2316 2.6665 15.9997 2.6665C17.7678 2.6665 19.4635 3.36888 20.7137 4.61912C21.964 5.86937 22.6663 7.56506 22.6663 9.33317V14.6665" stroke="#49516A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>

    </div>
    <div class="reportreview__popup__content__text">
      <h1>Get complete HappiLIFE Screening Summary</h1>
      <p>Get complete Summary of Globally validated 10 Parameter Screening Summary. Gain insight into your state of Emotional wellbeing.</p>
      <div class="reportreview__popup__content__text__btn">
        <div class="reportreview__popup__content__text__btn__nothanks">
          <a href="{{ route('user.dashboard') }}"><button type="button" data-dismiss="modal">No,Thanks</button></a>
        </div>
        <div class="reportreview__popup__content__text__btn__proceed">
          <a href="{{ route('user.payment.buyBundle') }}"><button type="button">Proceed</button></a>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
@endsection
