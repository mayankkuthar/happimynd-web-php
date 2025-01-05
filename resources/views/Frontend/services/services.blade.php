@extends('layouts.app')

@section('title', 'Health wellbeing trained psychologists | Best Digital Mental Wellness and Emotional Care Platform: Happimynd | Trained Digital Psychologists Best in Gurgaon: Happimynd')
@section('description', 'Trained digital psychologists are here to diagnose you, address your issues, and treat them at the initial stage | Happimynd brings you emotional wellbeing, physical wellbeing and mental health wellness services | Get in touch with experienced Psychologists like, Veena Nair, Dr. Amit Chakraborty, etc. at just ONE Click.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')

  <section>
    <div class="section9">
      <div class="container">
        <div class="section9__content" id="ourservices">
          <div class="section9__content__logoimg">
            <img src="{{ asset('assets/Frontend/images/happimynd_logo.png') }}" alt="HappiMynd" />
          </div>
          <h1 class="section9__content__heading">OUR Services</h1>
          <p class="section9__content__desc">Helps you take care of your Mind</p>
          <div class="section9__ourservices-tabs">
            <div class="section9__ourservices-tabs-style" id="happilife">
              <p class="happilife active-tab">{{$data[4]->overview}}</p>
              <button id="happi_life" type="button" class="happilife active-tab" onclick="switchTabs('happilife')">{{$data[4]->title}}</button>
            </div>
            <div class="section9__ourservices-tabs-style" id="happiapp">
              <p class="happiapp">{{$data[0]->overview}}</p>
              <button id="happi_app" type="button" class="happiapp" onclick="switchTabs('happiapp')">{{$data[0]->title}}</button>
            </div>
            <div class="section9__ourservices-tabs-style" id="happichat">
              <p class="happichat">{{$data[3]->overview}}</p>
              <button id="happi_chat" type="button" class="happichat" onclick="switchTabs('happichat')">{{$data[3]->title}}</button>
            </div>
            <div class="section9__ourservices-tabs-style" id="happitalk">
              <p class="happitalk">{{$data[1]->overview}}</p>
              <button id="happi_talk" type="button" class="happitalk" onclick="switchTabs('happitalk')">{{$data[1]->title}}</button>
            </div>
            <div class="section9__ourservices-tabs-style" id="happispace">
              <p class="happispace">{{$data[2]->overview}}</p>
              <button id="happi_space" type="button" class="happispace" onclick="switchTabs('happispace')">{{$data[2]->title}}</button>
            </div>

            <div class="section9__ourservices-tabs-style" id="happiguide">
              <p class="happiguide">Summary Reading</p>
              <button id="happiguide" type="button" class="happiguide" onclick="switchTabs('happiguide')">HappiGUIDE</button>
            </div>


          </div>
          <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section9__ourservices-tabs__content">
            <div class="section9__ourservices-tabs__happilife">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div>
                    <span>{{$data[4]->title}}</span>
                    <h2>{{$exploreServiceContent->content[4]->title}}</h2>
                    {!!$exploreServiceContent->content[4]->content!!}
                    <div class="section9__ourservices-tabs__happilife__btn">
                      <!-- <a href="{{ route('user.signupView') }}">{{$button_contents[0]->button_content}}</a> -->
                      <a href="{{url('subscribedservices') }}" >{{$button_contents[0]->button_content}}</a>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="section9__ourservices-tabs__content-img">
                    <img src="{{$data[4]->getImageWithS3Url('services')}}" alt="HappiLIFE Screening" >
                  </div>
                </div>
              </div>
            </div>
            <div class="section9__ourservices-tabs__happiapp">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div>
                    <span>{{$data[0]->title}}</span>
                    <h2>{{$exploreServiceContent->content[0]->title}}</h2>
                    {!!$exploreServiceContent->content[0]->content!!}
                    <!-- <a href="{{ route('user.signupView') }}" >{{$button_contents[1]->button_content}}</a> -->
                    <a href="{{url('subscribedservices') }}" >{{$button_contents[1]->button_content}}</a>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="section9__ourservices-tabs__content-img">
                    <img src="{{$data[0]->getImageWithS3Url('services')}}" alt="HappiAPP - HappiMynd" >
                  </div>
                </div>
              </div>
              <div class="section9__ourservices-tabs__happyapp__thrive">
                <!-- <h2>Powered by<img src="{{ asset('assets/Frontend/images/thrive_logo_happyapp.svg') }}" alt="Thrive Logo" /></h2> -->
              </div>
            </div>
            <div class="section9__ourservices-tabs__happichat">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div>
                    <span>{{$data[3]->title}}</span>
                    <h2>{{$exploreServiceContent->content[3]->title}}</h2>
                    {!!$exploreServiceContent->content[3]->content!!}
                    <!-- <a href="{{ route('user.signupView') }}" >{{$button_contents[2]->button_content}}</a> -->
                    <a href="{{url('subscribedservices') }}" >{{$button_contents[2]->button_content}}</a>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="section9__ourservices-tabs__content-img">
                    <img src="{{$data[3]->getImageWithS3Url('services')}}" alt="HappiCHAT - express your feelings" >
                  </div>
                </div>
              </div>
            </div>
            <div class="section9__ourservices-tabs__happitalk">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div>
                    <span>{{$data[1]->title}}</span>
                    <h2>{{$exploreServiceContent->content[1]->title}}</h2>
                    {!!$exploreServiceContent->content[1]->content!!}
                    <!-- <a href="{{ route('user.signupView') }}" >{{$button_contents[3]->button_content}}</a> -->
                    <a href="{{ route('user.psychologist') }}" >{{$button_contents[3]->button_content}}</a>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="section9__ourservices-tabs__content-img">
                    <img src="{{$data[1]->getImageWithS3Url('services')}}" alt="HappiTALK - HappiMynd" >
                  </div>
                </div>
              </div>
            </div>
            <div class="section9__ourservices-tabs__happispace">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div>
                    <span>{{$data[2]->title}}</span>
                    <h2>{{$exploreServiceContent->content[2]->title}}</h2>
                    {!!$exploreServiceContent->content[2]->content!!}
                    <!-- <a href="{{ route('happispaceform') }}">{{$button_contents[4]->button_content}}</a> -->
                    <a href="{{url('subscribedservices') }}" >{{$button_contents[4]->button_content}}</a>
                    
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="section9__ourservices-tabs__content-img">
                    <img src="{{$data[2]->getImageWithS3Url('services')}}" alt="HappiSPACE - HappiMynd" >
                  </div>
                </div>
              </div>
            </div>


            <div class="section9__ourservices-tabs__happiguide">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div>
                    <span>HappiGUIDE</span>
                    <h2>Summary Interpretation by Emotional Wellbeing Expert</h2>
                    On completing your HappiLIFE awareness, as the next logical step, our professional expert will offer a thorough explanation and clear interpretation of your summary. Make the most out of your summary by gaining a deeper understanding of the different facets of your personality. This enables you to identify strengths and weaknesses while helping you pinpoint areas of management and improvement. The summary reading session takes you one step closer to knowing yourself and keeping a check on your mental and emotional wellbeing to achieve long term happiness and a truly enhanced quality of life.
                    <a href="{{url('subscribedservices') }}" style="margin-top: 20px;" >Get HappiGUIDE now</a>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6">
                  <div class="section9__ourservices-tabs__content-img">
                    <img src="{{$data[2]->getImageWithS3Url('services')}}" alt="HappiGUIDE - HappiMynd" >
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $(".section9__ourservices-tabs__happiguide").hide();
  })
  
</script>
@endsection




