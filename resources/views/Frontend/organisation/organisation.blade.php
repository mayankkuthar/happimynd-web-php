@extends('layouts.app')

@section('title', 'Care for Mental Health Wellness with mynd programme | Receive emotional care at HappiSPACE | Emotional Well-being is taken care of by HappiSPACE')
@section('description', 'Happimynd is all set to bring you the best of services to help you with issues of anxiety, depression or stress | HappiSPACE is best for any organisation looking for features that will help their employees tackle their Emotional Well-being | HappiSPACE enables you to access all other products that ill help you with your emotional well-being and your overall mental health.')

@section('content')
<div id="container1"> 
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')
  <section>
    <div class="organisation">
      <div class="organisation__content">
        <div class="container">
          <div class="row align-items-center organisation__content__column flex-column-reverse flex-sm-row">
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="">
                <div class="organisation__content__heading">
                  <h2>HappiSPACE for Organisations</h2>
                  <h1>{{$organizations[0]->title ??''}}</h1>
                  <p>{{$organizations[0]->description}}</p>
                </div>
                <div class="organisation__content__contact_sales">
                  <a id="get_happispace_now1" href="{{ route('happispaceform') }}" > {{ $organisation_buttons[0]->button_content }}</a>
                  <!-- <a id="get_happispace_now1" href="{{ route('organisation') }}" > {{ $organisation_buttons[0]->button_content }}</a> -->
                  
                </div>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="organisation__content__img">
                <img src="{{$organizations[0]->getImageWithS3Url('org')}}" alt="workplace stress management and wellbeing programs." >
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="organisation__companies">
        <div>
          <!--<h1>2800+ Organisations using our Self Help App</h1>-->
          <ul>
            <li data-aos="fade-up" data-aos-duration="1200" data-aos-once="true">
              <img class="organisation__companies__img-dhl" src="{{$logos[0]->getImageWithS3Url('org')}}" alt="googleimg" />
            </li>
            <li data-aos="fade-up" data-aos-duration="1200" data-aos-once="true">
              <img class="organisation__companies__img-next" src="{{$logos[1]->getImageWithS3Url('org')}}" alt="bloomberg" />
            </li>
            <li data-aos="fade-up" data-aos-duration="1200" data-aos-once="true">
              <img class="organisation__companies__img-aviva" src="{{$logos[2]->getImageWithS3Url('org')}}" alt="facebook" />
            </li>
            <li data-aos="fade-up" data-aos-duration="1200" data-aos-once="true">
              <img class="organisation__companies__img-santander" src="{{$logos[3]->getImageWithS3Url('org')}}" alt="pinterest" />
            </li>
            <li data-aos="fade-up" data-aos-duration="1200" data-aos-once="true">
              <img class="organisation__companies__img-serco" src="{{$logos[4]->getImageWithS3Url('org')}}" alt="newyorktimes" />
            </li>
            <li data-aos="fade-up" data-aos-duration="1200" data-aos-once="true">
              <img class="organisation__companies__img-healthshield" src="{{$logos[5]->getImageWithS3Url('org')}}" alt="newyorktimes" />
            </li>
          </ul>
        </div>
      </div>
      {{-- code copied from landing page --}}
      <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section9__ourservices-tabs__happyapp__thrive organisation__thrive">
        <h2>Powered by<img src="{{$logos[6]->getImageWithS3Url('org')}}" /></h2>
      </div>
    </div>
  </section>

  <section>
    <div class="organisation__howcanwe">
      <div class="container">
        <div class="organisation__howcanwe__content">
          <div class="row">
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="organisation__howcanwe__content__text">
                <!-- <h2>How can we</h2> -->
                <h1>{{$organizations[1]->title ??''}}</h1>
                @foreach($organizations[1]->lines as $line)
                  @if($line=='')
                    @continue
                  @endif
                <p>
                  <span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="12" cy="12" r="12" fill="#3C92C6"/>
                      <path d="M17.3337 8L10.0003 15.3333L6.66699 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </span>
                  <span>
                  {{$line}}
                  </span>
                </p>
                @endforeach
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="organisation__howcanwe__content__img">
                <img src="{{$organizations[1]->getImageWithS3Url('org')}}" >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="organisation__whyus">
      <div class="container">
        <div class="organisation__whyus__content">
          <div class="row">
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="organisation__whyus__content__text">
                <h2>Why HappiMynd?</h2>
                <h1>{{$organizations[2]->title ??''}}</h1>
                @foreach($organizations[2]->lines as $line)
                  @if($line=='')
                    @continue
                  @endif
                <p>
                  <span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="12" cy="12" r="12" fill="#3C92C6"/>
                      <path d="M17.3337 8L10.0003 15.3333L6.66699 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </span>
                  <span>
                  {{$line}}
                  </span>
                </p>
                @endforeach
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="organisation__whyus__content__img">
                <img src="{{$organizations[2]->getImageWithS3Url('org')}}" >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Below two sections copied from landing page -->

  <section>
    <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section8">
      <div class="section8__content section8__content__organisation">
        <div>
          <h2>Build a Happi & Productive workspace</h2>
          <div class="section8__content-check-score section8__content__organisation__score">
            <a id="get_happispace_now" href="{{ route('happispaceform') }}" target="_blank">{{ $organisation_buttons[1]->button_content }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
