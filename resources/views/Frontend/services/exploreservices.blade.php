@extends('layouts.app')

@section('title', 'Self Improvement & Management Tool | Apps for Mental Wellness | HappiMynd')
@section('description', 'HappiMynd offers a complete solution for self-improvement and management with digitally empowered tools and apps for mental wellness.  Our service ensures your complete confidentiality.')

@section('content')
<div id="container1">
  @include('Frontend.includes.dashboard.header')
  @include('Frontend.includes.popups.commingsoon')
  @include('Frontend.includes.popups.happiAppPopup')

  <div class="explore-services">
    <div class="container">
      <h1>Explore Services</h1>
      <div class="section9__ourservices-tabs__content">
        @isset($happiApp)
        <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section9__ourservices-tabs__happyapp explore-services-happyapp">
          <div class="row explore-services-happyapp-flexdir">
            <div class="col-lg-6 col-md-6">
              <div class="explore-services__content__text">
                <span>{{$data[0]->title}}</span>
                <h2>{!! $happiApp->title ?? '' !!}</h2>
                <p>{!! $happiApp->content ?? '' !!}</p>
                <a href="javascript:void(0)" id="happiApp">{{$button_contents[1]->button_content}}</a>
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <div class="section9__ourservices-tabs__content-img">
                <img src="{{ asset('assets/Frontend/images/sec9_happyapp.svg') }}" >
              </div>
            </div>
          </div>
        </div>
        @endisset
        <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section9__ourservices-tabs__happytalk explore-services-happytalk">
          <div class="row align-items-center">
            <div class="col-lg-6 col-md-6">
              <div class="section9__ourservices-tabs__content-img">
                <img src="{{ asset('assets/Frontend/images/sec9_happytalk.svg') }}" >
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <div class="explore-services__content__text">
                <span>{{$data[1]->title}}</span>
                <h2>{!! $happiTALK->title ?? ''!!}</h2>
                <p>{!! $happiTALK->content ?? '' !!}</p>
                <a href="{{ route('user.payment.buyBundle') }}" >{{$button_contents[3]->button_content}}</a>
              </div>
            </div>
          </div>
        </div>
        <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section9__ourservices-tabs__happyspace explore-services-happyspace">
          <div class="row explore-services-happyspace-flexdir">
            <div class="col-lg-6 col-md-6">
              <div class="explore-services__content__text">
                <span>{{$data[2]->title}}</span>
                <h2>{!! $happiSPACE->title ?? ''!!}</h2>
                <p>{!! $happiSPACE->content ?? ''!!}</p>
                <a href="{{ route('happispaceform') }}">{{$button_contents[4]->button_content}}</a>
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <div class="section9__ourservices-tabs__content-img">
                <img src="{{ asset('assets/Frontend/images/sec9_happyspace.svg') }}" >
              </div>
            </div>
          </div>
        </div>
        <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="section9__ourservices-tabs__happychat explore-services-happychat">
          <div class="row">
            <div class="col-lg-6 col-md-6">
              <div class="section9__ourservices-tabs__content-img">
                <img src="{{ asset('assets/Frontend/images/sec9_happychat.svg') }}" >
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <div class="explore-services__content__text">
                <span>{{$data[3]->title}}</span>
                <h2>{!! $happiCHAT->title ?? '' !!}</h2>
                <p>{!! $happiCHAT->content ?? '' !!}</p>
                <a href="{{ route('user.payment.buyBundle') }}">{{$button_contents[2]->button_content}}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  const thriveCode = "";
  const thriveCheckURL = "{{ route('user.checkForThriveCode') }}";
  $(document).ready(()=>{
    $('#happiApp').click(function(e){
      e.preventDefault();
      @if(!auth('user')->user()->hasHappiAPPPlan())
        window.location = "{{ route('user.payment.buyBundle') }}";
      @endif
      $.ajax({
        type: "get",
        url: thriveCheckURL,
        success: function (data) {
          if(data['data']!=""){
            window.location="{{ route('user.thrivecode')}}?code="+data['data'];
          }else{
            $('#happiAppPopup_message').html(data['message']);
            if(data['status']!='2'){
              $('#happiAppPopup_btns').hide();
            }
          }
        }
      });

      $('#happiAppPopup').modal("show");
      if(thriveCode!=""){
        $('#happiAppPopup_message').html('Your HappiApp Code is ....');
        $('#happiAppPopup_btns').hide();
      }
    })

    onSuccessSubmit = function(data){
      window.location="{{ route('user.thrivecode')}}?code="+data['data'];
    }

    beforeSuccessSubmit = function(){
      $("#happiAppPopup_yes_btn").prop('disabled',true);
    }
    formSubmitAjaxEvent("happiAppPopup_form", beforeSuccessSubmit, onSuccessSubmit);

    if(sessionStorage.getItem('open_thrivecode_popup')){
      sessionStorage.removeItem('open_thrivecode_popup');
      $('#happiApp').click();
    }
  })
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection