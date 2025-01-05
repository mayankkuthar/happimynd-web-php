@php  
use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('title', 'Get in touch with trained Psychologists at HappiTALK for your healthy well being | Check your wellbeing by our emotional wellness check up: HappiLIFE Screening | Mental health wellness and emotional well-being is cared for at Happimynd')
@section('description', 'Experience our best services like HappiTALK where you can directly get in touch with our trained Psychologists | It has a personalized set of questions to help you check the exact reason for your stress levels | Do you care for your emotional wellness? Start your journey of exploring your emotional well-being and mental health with Happimynd in the right direction.')
@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')
  <div class="ourteam">
    <div class="ourteam__sec1">
      <div class="container">
        <div data-aos="fade-up" data-aos-duration="500" data-aos-once="true" class="ourteam__sec1__content">
          <h1>HappiMynd is brought together by a dedicated team of passionate, experienced, empathetic and energetic individuals. With a cumulative experience of more than 120 years, we are a group of tight-knit people who care about each other as much as we care about our mission</h1>
          {{-- <button type="button"><a href="javascript:void(0);" onclick="showCommingSoonPop();">Join us</a></button> --}}
          <div class="ourteam__sec1__img">
            <img src="{{ asset('assets/Frontend/images/ourteam_sec1-img.svg') }}" >
          </div>
        </div>
      </div>
    </div>
    <div class="ourteam__sec2" id="teams">
      @isset($founders)
      <div class="container">
        <div class="ourteam__sec2__content">
          <h1>Meet the Team</h1>
          <div class="row">
            @foreach($founders as $founder)
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-4 col-md-6">
              <div class="ourteam__sec2__content__detail__hover">
                <div class="ourteam__sec2__content__detail">
                  <div class="ourteam__sec2__content__detail__img">
                    <img src="{{ $founder->getImageWithS3Url('teams')}}" alt="{{$founder->name}}" />
                  </div>
                  <h2>{{$founder->name}}</h2>
                  <h3>{{$founder->designation}}</h3>
                  <p>{!!Str::words($founder->description,500)!!}</p>
                </div>
                @if(isset($founder->linkedin))
                <div class="ourteam__sec2__content__linkedin">
                  <a target="_blank" href="{{$founder->linkedin}}">
                    <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile.svg') }}">
                  </a>
                </div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
      @endisset
    </div>

    <div class="ourteam__sec3">
      @isset($experts)
      <div class="container">
        <div class="ourteam__sec3__content">
          <h1>Panel of Experts</h1>
          <div class="row justify-content-center">
            @foreach($experts as $expert)
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-4 col-md-6 ml-auto mr-auto">
              <div class="ourteam__sec3__content__detail__hover">
                <div class="ourteam__sec3__content__detail">
                  <div class="ourteam__sec3__content__detail__img">
                    <img src="{{ $expert->getImageWithS3Url('teams')}}" alt="{{$expert->name}}" />
                  </div>
                  <h2>{{$expert->name}}</h2>
                  {{-- <h3>{{$expert->designation}}</h3> --}}
                  <p>{!!Str::words($expert->description,500)!!}</p>
                </div>
                @if(isset($expert->linkedin))
                <div class="ourteam__sec3__content__linkedin">
                  <a  target="_blank" href="{{$expert->linkedin}}">
                    <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                  </a>
                </div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
      @endisset
    </div>

    <div class="ourteam__sec2" id="teams" style="margin: 0; margin-bottom: 100px">
      @isset($founders)
      <div class="container">
        <div class="ourteam__sec2__content">
          <h1>Panel of Psychologist</h1>
          <div class="row">
            @foreach($psychologists as $psychologist)
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-4 col-md-6">
              <div class="ourteam__sec2__content__detail__hover">
                <div class="ourteam__sec2__content__detail">
                  <div class="ourteam__sec2__content__detail__img">
                    <img src="{{ $psychologist->getImageWithS3Url('teams')}}" alt="{{$psychologist->name}}" />
                  </div>
                  <h2>{{$psychologist->name}}</h2>
                  <h3>{{$psychologist->designation}}</h3>
                  <p>{!!Str::words($psychologist->description,500)!!}</p>
                </div>
                @if(isset($psychologist->linkedin))
                <div class="ourteam__sec2__content__linkedin">
                  <a target="_blank" href="{{$expert->linkedin}}" >
                    <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile.svg') }}">
                  </a>
                </div>
                @endif
              </div>
            </div>
            @endforeach
                {{-- <div class="ourteam__sec2__content__linkedin">
                  <a target="_blank" href="https://www.linkedin.com/in/dr-neeraj-tripathi-95119bb3/" >
                    <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile.svg') }}">
                  </a>
                </div> --}}
              </div>
            </div>
          </div>
          @endisset
        </div>
      </div>
    </div>

    {{-- <div class="ourteam__sec5">
      <div class="container">
        <div class="ourteam__sec5__content">
          <h1>Team Members</h1>
          <div class="ourteam__sec5__content__teammembers">
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ps_murthy.png') }}">
                </div>
                <h2>PS Murthy</h2>
                <p>Strategic Advisor</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ps_murthy.png') }}">
                </div>
                <h2>Salina Kyle</h2>
                <p>Product lead</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img3.png') }}">
                </div>
                <h2>Sara Black</h2>
                <p>Scientist</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img4.png') }}">
                </div>
                <h2>Jon Possor</h2>
                <p>App lead</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img5.png') }}">
                </div>
                <h2>Ravi Doshi</h2>
                <p>team lead</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img6.png') }}">
                </div>
                <h2>Cersei</h2>
                <p>Researcher</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img7.png') }}">
                </div>
                <h2>Sansa Stark</h2>
                <p>Psychologist</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img8.png') }}">
                </div>
                <h2>Tyrion Lannister</h2>
                <p>Advisor</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img9.png') }}">
                </div>
                <h2>Jorah Mormont</h2>
                <p>Scientist</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img10.png') }}">
                </div>
                <h2>Gilly</h2>
                <p>Product manager</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img11.png') }}">
                </div>
                <h2>Rohit</h2>
                <p>Bot Writer</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img12.png') }}">
                </div>
                <h2>Arya Stark</h2>
                <p>Researcher</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img13.png') }}">
                </div>
                <h2>Daenerys Targaryen</h2>
                <p>Psychologist</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img14.png') }}">
                </div>
                <h2>Brienne of Tarth</h2>
                <p>Psychologist</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="ourteam__sec5__content__teammembers__info">
              <div class="ourteam__sec5__content__teammembers__info__content">
                <div>
                  <img src="{{ asset('/assets/Frontend/images/teams/ourteam__sec5_img15.png') }}">
                </div>
                <h2>Jon Snow</h2>
                <p>Psychologist</p>
              </div>
              <div class="ourteam__sec5__content__linkedin">
                <a href="javascript:void(0);" onclick="showCommingSoonPop();">
                  <img src="{{ asset('/assets/Frontend/images/teams/linkedinprofile_white.svg') }}">
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
  </div>

  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
