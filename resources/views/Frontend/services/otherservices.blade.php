@extends('layouts.app')

@section('title', 'Self Improvement & Management Tool | Apps for Mental Wellness | HappiMynd')
@section('description', 'HappiMynd offers a complete solution for self-improvement and management with digitally
    empowered tools and apps for mental wellness. Our service ensures your complete confidentiality.')

@section('content')
    <div id="container1">
        @include('Frontend.includes.header')
        @include('Frontend.includes.popups.commingsoon')
        @include('Frontend.includes.popups.coupon')
        @include('Frontend.includes.popups.other_services.emailinput')
            <div class="container">
              <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has( $msg))
                <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
                @endif
                @endforeach
              </div>
                <div class="otherservices">
                    <div>
                      @if($happimynd->service->count() > 0)
                        <h1>{{ $happimynd->name }} </h1>
                        <div class="row">
                            @foreach ($happimynd->service as $service_list)
                                <div class="col-lg-4 col-md-6 col-sm-12 d-flex">
                                    <div class="otherservices__card d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="otherservices__card__offer">
                                                <span>{{ $service_list->discount }}%</span>
                                            </div>
                                            <a href="javascript:void();" class="otherservices__card__link">
                                                <div
                                                    class="otherservices__card_image d-flex align-items-center justify-content-center">
                                                    <img src="{{ $service_list->getThumbnailWithS3Url('services') ?? '' }}"
                                                        alt="image1" />
                                                </div>
                                                <h2>{{ $service_list->title }}</h2>
                                            </a>
                                            <p>{{ $service_list->description }}</p>
                                            <input type="hidden" id="service_id" value="{{ $service_list->id }}">
                                        </div>
                                        <div class="educationalservices__card__pricing">
                                            {{-- <div class="d-flex align-items-center justify-content-end"> --}}
                                              <div class="d-flex align-items-sm-center align-items-start justify-content-sm-between flex-column flex-sm-row">
                                              <h4>Rs.{{ number_format($service_list->discountedPrice(), 2) }}</h4>
                                              <h4>
                                              <span>Rs.{{ number_format($service_list->price, 2) }}</span>
                                              </h4>
                                                <a href="javascript:void();" onclick="showEmailInput('{{ $service_list->id }}');">Buy now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                      @endif
                    </div>
                    <div>
          @if($otherServices->service->count() > 0)
            <h1>{{ $otherServices->name }} </h1>
              <div class="row">
              @foreach ($otherServices->service as $service_list)
              <div class="col-lg-4 col-md-6 col-sm-12 d-flex">
                <div class="otherservices__card d-flex flex-column justify-content-between">
                  <div>
                    <div class="otherservices__card__offer">
                      <span>{{ $service_list->discount }}%</span>
                    </div>
                    <a href="javascript:void();" class="otherservices__card__link">
                      <div class="otherservices__card_image d-flex align-items-center justify-content-center">
                        <img src="{{ $service_list->getThumbnailWithS3Url('services') ?? '' }}"
                        alt="image1" />
                      </div>
                      <h2>{{ $service_list->title }}</h2>
                    </a>
                    <p>{{ $service_list->description }}</p>
                    <input type="hidden" id="service_id" value="{{ $service_list->id }}">
                  </div>
                  <div class="educationalservices__card__pricing">
                    {{-- <div class="d-flex align-items-center justify-content-end"> --}}
                      <div class="d-flex align-items-sm-center align-items-start justify-content-sm-between flex-column flex-sm-row">
                        <h4>Rs.{{ number_format($service_list->discountedPrice(), 2) }}</h4>
                      <h4><span>Rs.{{ number_format($service_list->price, 2) }} </span></h4>
                      @if($service_list->coupon || $service_list->buy_link)
                      <a href="javascript:void();" onclick="showCouponInfo('{{ $service_list->id }}');">Buy now</a>
                      @else
                      <a href="javascript:void();" onclick="showEmailInput('{{ $service_list->id }}');">Buy now</a>
                      @endIf
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>
                </div>
            </div>
        @include('Frontend.includes.footer')
    </div>
    <script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
