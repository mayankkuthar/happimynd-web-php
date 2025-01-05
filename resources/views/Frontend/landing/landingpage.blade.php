@extends('layouts.app')

@section('title', 'Emotional Well-being is important | Internship at mental health and well-being: Happimynd | Digital Mental Health & Emotional Well-being Platform: Happimynd ')

@section('description', "We are HappiMynd, a digital platform that works behind the scenes to give you a better perspective of your emotional wellness | If you have skills, YOU have us! An internship with the best working atmosphere is waiting for you to enroll. Every skilled person is Eligible | Happimynd helps you assess your emotions and thoughts in a speculated way. You need to experience us, in order to judge us better.")


@section('keywords', "mental health, mental illness, mental health services, mental wellness, mental wellbeing, mental and emotional health, mental health and wellbeing, psychologist, health professionals, therapist, clinicians, anxiety disorder, postpartum depression, bipolar disorder, psychotic disorders.")

@section('content')
<style>
  #close-landing-modal {
      transition: .5s ease;
  }
  .landing-modal-img{
    height: 450px !important;
  }
  .landing-modal .modal-content {
    max-width: 48% !important;
}
  .landing-modal-img img {
      object-fit: contain;
  }
  #close-landing-modal:hover {
      transform: rotate(360deg);
  }
  .brands {
    padding: 0 10px 0 10px;
  }
  .landing-modal-button{
    position: relative !important;
    left: inherit  !important;
    right: inherit  !important;
    bottom: inherit  !important;
    margin-top: 10px !important;
  }
  .landing-modal-button a#happilife_screening2 {
      font-size: 16px;
      padding: 15px 15px;
      background: #20599B;
      border: 1px solid #20599B;
      color: #ffffff;
      transition: .3s ease;
  }
  .landing-modal-button a#happilife_screening2:hover{
    background: #ffffff;
      border: 1px solid #20599B;
      color: #20599B;
  }
  .landing-modal .modal-content {
    padding-bottom: 15px;
}
  .sendfullreport__popup__form__input a img {
      transition: .5s ease;
  }
  .sendfullreport__popup__form__input a img:hover {
      transform: scale(1.1);
  }
  .sendfullreport__popup__form__input{
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 10px;
  }
  .sendfullreport__popup__form__input a{
    margin: 0 10px;
  }
  @media (max-width: 991px){
    .landing-modal-button{
      margin-top: 0px !important;
    }
    .sendfullreport__popup__form__input{
      display: block;
      margin-top: 10px;
      text-align: center;
    }
    .sendfullreport__popup__form__input a{
      margin: 0 0 10px 0 !Important;
      display: inline-block ;
    }
    .sendfullreport__popup__form__input img {
      width: 90px !important;
      height: auto !important;
  }
  .landing-modal .modal-content {
      background: #E5FFFF;
      padding-bottom: 20px;
    }
      #happilife_screening2 {
        font-size: 12px  !important;
        padding: 12px 20px !important; 
    }
  }
  @media (max-width:  767px){
    .sendfullreport__popup__form__input {
      margin-top: 10px;
  }
  }
</style>
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.loginsuccesspopup')
  @include('Frontend.includes.popups.commingsoon')
  <section>

     <!-- Modal -->
  <div class="modal fade landing-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="max-width:80%; margin: 0 auto;">
        <button type="button" class="close" id="close-landing-modal" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <span class="landing-modal-img">
          <img src="{{$quotes->getImageWithS3Url('quotes')}}" alt="">
        </span>
        <div class="sendfullreport__popup__form__input">
          <a href="https://play.google.com/store/apps/details?id=com.happimynd">
            <img src="{{url('/assets/Frontend/images/play_store.png')}}"  >
          </a>
          <a href="https://apps.apple.com/in/app/happimynd-emotional-self-help/id1634742782">
            <img src="{{url('/assets/Frontend/images/app_store.png')}}" style="height: 50px;">
          </a>
        </div>
        <div id="landing_button_div" class="section1__explore__mind m-0 mx-auto landing-modal-button">
          <!-- <a id="happilife_screening2" class="px-4" href="https://b24-soslcy.bitrix24.site/crm_form_1jsqt/"  >{{$button_contents->button_content}} -->
          </a>
        </div>

        


      </div>
    </div>
  </div>
    <div class="section1">
      <div class="container">
        <div class="row no-gutters align-items-center flex-lg-row flex-column-reverse">
          <div class="col-lg-6">
            <div class="section1__content">
              <div class="section1__heading">
                <h3>{!! $sections['section1']->title !!}</h3>
                <p>
                {!! $sections['section1']->content !!}
                </p>
                <div class="section1__explore__mind">
                  <a id="happilife_screening1" href="{{ url('/signup') }}">
                    {{ $landing_buttons[0]->button_content }}
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="section1_mob_back">
              @if ($sections['section1']->image)
              <img src="{{ $sections['section1']->getImagewithS3Url('landing_page') }}" alt="Emotional wellness counselling - employee happiness programs – HappiMynd"  >
              @else
              <img src="{{ asset('assets/Frontend/images/new_sec1img1.png') }}" alt="Emotional wellness counselling - employee happiness programs – HappiMynd"  >
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section>
    <div class="section2">
      <div class="container">
        <div class="section2__play-intro-video d-flex align-items-center justify-content-center">
          <div class="text-center">
            
            <!-- <iframe width="420" height="315" src="{{url('https://www.youtube.com/embed/5Peo-ivmupE')}}" frameborder="0" allow="autoplay" allowfullscreen="allowfullscreen"></iframe> -->

            <video width="400" poster="{{ $introVideoThumbnail ?? '' }}" controls preload="none">
              <source src="{{ $introVideoLink ?? '' }}" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="section3">
      <div class="section3__content">
        <div class="container">
          <div class="row no-gutters align-items-center">
            <div class="col-lg-6">
              <div class="section3__content__wellbeingimg">
                @if ($sections['section3']->image)
                <img src="{{ $sections['section3']->getImagewithS3Url('landing_page') }}" alt="Mind exercises - ways to strengthen your mind – HappiMynd" >
                @else
                <img src="{{ asset('assets/Frontend/images/sec3_img.svg') }}" alt="Mind exercises - ways to strengthen your mind – HappiMynd" >
                @endif
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="d-flex align-items-center">
                <div class="section3__content__heading">
                  <h3>{!! $sections['section3']->title !!}</h3>
                  <p>{!! $sections['section3']->content !!}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="section4">
      <div class="section4__content">
        <div class="container">
          <div class="row align-items-center section4__content__column">
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="d-flex align-items-center">
                <div class="section4__content__heading">
                  <h1>{!! $sections['section4']->title !!}</h1>
                  <p>{!! $sections['section4']->content !!}</p>
                  <div class="section4__content-backzoom">
                    <img src="{{ 'assets/Frontend/images/sec4_backzoom.svg' }}" alt="HappiMynd" >
                  </div>
                </div>
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="section4__content__wellbeingimg">
                @if ($sections['section4']->image)
                <img src="{{ $sections['section4']->getImagewithS3Url('landing_page') }}" alt="Importance of emotional wellbeing" >
                @else
                <img src="{{ asset('assets/Frontend/images/sec4_img.svg') }}" alt="Importance of emotional wellbeing" >
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="section5">
      <div class="section5__content">
        <div class="container">
          <div class="row align-items-center">
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="section5__content__wellbeingimg">
                @if ($sections['section5']->image)
                <img src="{{ $sections['section5']->getImagewithS3Url('landing_page') }}" alt="How to take care of emotional wellbeing?" >
                @else
                <img src="{{ asset('assets/Frontend/images/sec5_img.png') }}" alt="How to take care of emotional wellbeing?" >
                @endif
              </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-6">
              <div class="d-flex align-items-center">
                <div class="section5__content__heading">
                  <h2>{!! $sections['section5']->title !!}</h2>
                  <p>{!! $sections['section5']->content !!}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="section6">
        <div class="section6__content">
            <div class="container">
              <h2>{{$sections['section6']->title}}</h2>
              <div class="row">
                
                <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-4 col-md-6">
                  <div class="section6__content__meditate">
                    <div><img src="{{ $carousel[0]->dataContents[0]->image?$carousel[0]->dataContents[0]->getImagewithS3Url('landing_page'):asset('assets/Frontend/images/sec6_img1.svg') }}" alt="Share your feelings" ></div>
                    <p>{!! $carousel[0]->dataContents[0]->title !!}</p>
                  </div>
                </div>
            
                <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-4 col-md-6">
                  <div class="section6__content__yoga">
                    <div><img src="{{ $carousel[0]->dataContents[1]->image?$carousel[0]->dataContents[1]->getImagewithS3Url('landing_page'):asset('assets/Frontend/images/sec6_img2.svg') }}" alt="Be aware and take self care" ></div>
                    <p>{!! $carousel[0]->dataContents[1]->title !!}</p>
                  </div>
                </div>
                <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-4 col-md-6 ml-auto mr-auto">
                  <div class="section6__content__cycling">
                    <div><img src="{{ $carousel[0]->dataContents[2]->image?$carousel[0]->dataContents[2]->getImagewithS3Url('landing_page'):asset('assets/Frontend/images/sec6_img3.svg') }}" alt="Know early signs " ></div>
                    <p>{!! $carousel[0]->dataContents[2]->title !!}</p>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
  </section>

  <section>
    <div class="section7">
      <div class="section7__content">
        <div class="container">
          <h1>{!! $sections['section7']->title !!}</h1>
          <p>{!! $sections['section7']->content !!}</p>
          <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section7__content-carousel position-relative">
            <div class="loop owl-carousel">
              @foreach ($carousel[1]->dataContents as $data)
              <div>
                <div class="section7__content-slideimg">
                  <img src="{{ $data->image?$data->getImagewithS3Url('landing_page'):asset('assets/Frontend/images/slide_img1.webp') }}" alt="{{$data->title}}" >
                  <div class="section7__content-slide-text">
                   <h1>{!! preg_replace( '#<h[1-4]>(.*)</h[1-4]>#m', '<h1>$1</h1>', $data->content) !!}</h1>
                  </div>
                </div>
              </div>
              @endforeach
              <!--<div>
                <div class="section7__content-slideimg">
                  <img src="{{asset('assets/Frontend/images/slide_img1.webp') }}" alt="Amitabh Bachchan" >
                  <div class="section7__content-slide-text">
                    <h1 title="What mental health needs is more sunlight, more candor, and more conversation.">It’s Okay not to be Okay,but It’s not “Okay” to not seek help.</h1>
                    <p>Amitabh Bachchan</p>
                  </div>
                </div>
              </div>
              <div>
                <div class="section7__content-slideimg">
                  <img src="{{ asset('assets/Frontend/images/slide_img2.webp') }}" alt="Barak Obama" >
                  <div class="section7__content-slide-text">
                    <h1>If ... something inside you feels like it's wounded just like a physial injury. You've got to get help. There's nothing weak about that. It's strong.</h1>
                    <p>Barack Obama</p>
                  </div>
                </div>
              </div>
              <div>
                <div class="section7__content-slideimg">
                  <img src="{{ asset('assets/Frontend/images/slide_img3.webp') }}" alt="Virat Kohli" >
                  <div class="section7__content-slide-text">
                    <h1>As far as the game goes, I have always been very keen on improving my mental state and not really focus on practising long hours in the nets</h1>
                    <p>Virat Kohli</p>
                  </div>
                </div>
              </div>
              <div>
                <div class="section7__content-slideimg">
                  <img src="{{ asset('assets/Frontend/images/slide_img4.webp') }}" alt="Deepika Padukone" >
                  <div class="section7__content-slide-text">
                    <h1>People talk about physical fitness, but mental health is equally important.</h1>
                    <p>Deepika Padukone</p>
                  </div>
                </div>
              </div> -->
            </div>
            <button id="content-carousel-prevbtn" class="section7__content-carousel-prevbtn" type="button">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 6L9 12L15 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <button id="content-carousel-nextbtn" class="section7__content-carousel-nextbtn" type="button">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section8">
      <div class="section8__content">
        <div>
          <h2>{!! $sections['section8']->title !!}</h2>
          <div class="section8__content-check-score">
            <a id="happilife_screening" href="{{ url('/signup') }}">{{ $landing_buttons[1]->button_content }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="section10">
      <div class="section10__content">
        <div class="container">
          <div>
            <h1>Why Choose Us</h1>
            <div class="section10__content__achievements">
              <div class="row">
                @foreach ($carousel[2]->dataContents as $data)
                
                <div id="counter" data-aos="fade-up" data-aos-duration="{{ $data->content }}" data-aos-once="true" class="col-lg-3 col-md-6 col-sm-6 animate_number">
                  <div>

                    @if($data->title == 'Screening Success' || $data->title == 'Awareness Tool Uptake')
                    <h2 ><span class="counter-value" data-count="{{ $data->content }}"></span>%</h2>
                    @elseif($data->title == 'Profiles' || $data->title == 'Reviews & Ratings')
                    <h2 ><span class="counter-value" data-count="{{ $data->content }}"></span></h2>
                    @else
                    <h2 ><span class="counter-value" data-count="{{ $data->content }}"></span>+</h2>
                    @endif
                    
                    <p>{!! $data->title !!}</p>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- <section>
    <div class="section11" id="faq">
      <div class="container">
        <div class="section11__content">
          <h1>Frequently Asked Questions</h1>
          <div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="accordion" id="accordionExample">
              @isset($generalFaqs)
                  @foreach ($generalFaqs->content as $generalFaq)
              <div class="">
                <div class="" id="heading{{ $generalFaq->id }}">
                  <h2 class="section11__content-btn" data-toggle="collapse" data-target="#collapse{{ $generalFaq->id }}" aria-expanded="false" aria-controls="collapse1">
                    {{ $generalFaq->title }}
                    <span>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                  </h2>
                </div>
                <div id="collapse{{ $generalFaq->id }}" class="collapse collapsed" aria-labelledby="heading{{ $generalFaq->id }}" data-parent="#accordionExample">
                  <p class="section11__content-desc">
                    {!! $generalFaq->content !!}
                  </p>
                </div>
              </div>
              <hr style="border-top:1px solid #B6B9C3;">
              @endforeach
              @endisset
          <div class="section11__content__link">
            <a href="{{ route('organisation') }}/#organisationfaq">FAQ related to organisation</a>
          </div>
        </div>
      </div>
    </div>
  </section> --}}
  @isset($clients)
  <section class="section11">
  <div class="our_clients">
  <h1>Corporate Clients</h1>
  <div class="container">
  <div class="owl-carousel owl-theme">
      @foreach ($clients as $client)
      <div>
        <div class="our-clients">
        <img src="{{ $client->getImageWithS3Url() }}" alt="{{ $client->name }}">
        </div> 
      </div>
      @endforeach
  </div>
  </div>
  </div>
  </section>
<script>
  $(document).ready(function() {
    $(".owl-theme").owlCarousel(
      {
        //Basic Speeds
        pagination: true,
        dots: false,
        //Autoplay
        autoplay: true,
        autoplayTimeout: 1000,
        autoplaySpeed: 1000,
        slideTransition: 'linear',
        autoplayHoverPause: true,
        loop: true,
        responsive : {
          1 : { items : 1  }, // from zero to 480 screen width 4 items
          768 : { items : 2  }, // from 480 screen widthto 768 6 items
          1024 : { items : 4 } // from 768 screen width to 1024 8 items   
        }
      }
    );
  })
</script>
  @endisset
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
