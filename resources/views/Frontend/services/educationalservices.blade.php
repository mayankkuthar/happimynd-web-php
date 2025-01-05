@extends('layouts.app')

@section('title', 'Self Improvement & Management Tool | Apps for Mental Wellness | HappiMynd')
@section('description', 'HappiMynd offers a complete solution for self-improvement and management with digitally empowered tools and apps for mental wellness. Our service ensures your complete confidentiality.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')
  @include('Frontend.includes.popups.education_services.emailinput')

  <div class="container">
    <div class="flash-message">
      @foreach (['danger', 'warning', 'success', 'info'] as $msg)
      @if(Session::has( $msg))
      <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
      @endif
      @endforeach
    </div>
    <div class="educationalservices">
      @if($mostPopular)
          <h1>{{ $mostPopular->name }} </h1>
        <div>
          <div class="row">
            @foreach ($mostPopular->service as $service_list)
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex">
              <div class="educationalservices__card">
                <a href="javascript:void();" class="educationalservices__card__link">
                  <div class="educationalservices__card_image d-flex align-items-center justify-content-center">
                    <img src="{{ $service_list->getThumbnailWithS3Url('services') ?? '' }}" alt="image1" />
                  </div>
                  <h2>{{ $service_list->title }}
                  </h2>
                </a>
                <h3>{{ $service_list->educationService->author->name  }}</h3>
                <div class="educationalservices__card__starrating">
                  <p>{{ floatval($service_list->educationService->rating)  }}</p>
                  <div class="rating">
                    <div class="rating-upper" style="width: 80%">
                      @for($i=0; $i< $service_list->educationService->rating; $i++ )
                      <span>★</span>
                      @endFor
                    </div>
                    <div class="rating-lower">
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                    </div>
                  </div>
                  <p>({{ number_format($service_list->educationService->downloads) }})</p>
                  <input type="hidden" id="service_id" value="{{ $service_list->id }}">
                </div>
                <div class="educationalservices__card__pricing">
                  <div class="d-flex align-items-sm-center align-items-start justify-content-sm-between flex-column flex-sm-row">
                    <h4>₹{{ number_format($service_list->educationService->discounted_price, 2) }}<span>₹{{ number_format($service_list->price, 2) }}</span></h4>
                    @if ($service_list->buy_link)
                    <a href="javascript:void();" href = "{{ $service_list->buy_link }}" target="_blank" >Buy now</a>
                    @else
                    <a href="javascript:void();" onclick="showEmailInput('{{ $service_list->id }}');">Buy now</a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endforeach

          </div>
        </div>
      @endisset
      @if($recommended)
        <h1>{{ $recommended->name }} </h1>
        <div>
          <div class="row">
            @foreach ($recommended->service as $service_list)
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex">
              <div class="educationalservices__card">
                <a href="javascript:void();" class="educationalservices__card__link">
                  <div class="educationalservices__card_image d-flex align-items-center justify-content-center">
                    <img src="{{ $service_list->getThumbnailWithS3Url('services') ?? '' }}" alt="image1" />
                  </div>
                  <h2>{{ $service_list->title }}
                  </h2>
                </a>
                <h3>{{ $service_list->educationService->author->name  }}</h3>
                <div class="educationalservices__card__starrating">
                  <p>{{ floatval($service_list->educationService->rating)  }}</p>
                  <div class="rating">
                    <div class="rating-upper" style="width: 80%">
                      @for($i=0; $i< $service_list->educationService->rating; $i++ )
                      <span>★</span>
                      @endFor
                    </div>
                    <div class="rating-lower">
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                    </div>
                  </div>
                  <p>({{ number_format($service_list->educationService->downloads) }})</p>
                  <input type="hidden" id="service_id" value="{{ $service_list->id }}">
                </div>
                <div class="educationalservices__card__pricing">
                  <div class="d-flex align-items-sm-center align-items-start justify-content-sm-between flex-column flex-sm-row">
                    <h4>₹{{ number_format($service_list->educationService->discounted_price, 2) }}<span>₹{{ number_format($service_list->price, 2) }}</span></h4>
                    @if ($service_list->buy_link)
                    <a href="javascript:void();" href = "{{ $service_list->buy_link }}" target="_blank" >Buy now</a>
                    @else
                    <a href="javascript:void();" onclick="showEmailInput('{{ $service_list->id }}');">Buy now</a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      @endisset
      @if($allCourses)
      <h1>All Courses</h1>
      <div>
        <div class="row">
          @foreach ($allCourses as $service_list)
          <div class="col-lg-4 col-md-6 col-sm-12 d-flex">
            <div class="educationalservices__card">
              <a href="javascript:void();" class="educationalservices__card__link">
                <div class="educationalservices__card_image d-flex align-items-center justify-content-center">
                  <img src="{{ $service_list->getThumbnailWithS3Url('services') ?? '' }}" alt="image1" />
                </div>
                <h2>{{ $service_list->title }}
                </h2>
              </a>
              <h3>{{ $service_list->educationService->author->name  }}</h3>
              <div class="educationalservices__card__starrating">
                <p>{{ floatval($service_list->educationService->rating)  }}</p>
                <div class="rating">
                  <div class="rating-upper" style="width: 80%">
                    @for($i=0; $i< $service_list->educationService->rating; $i++ )
                    <span>★</span>
                    @endFor
                  </div>
                  <div class="rating-lower">
                      <span>★</span>
                      <span>★</span>
                      <span>★</span>
                      <span>★</span>
                      <span>★</span>
                  </div>
                </div>
                <p>({{ number_format($service_list->educationService->downloads) }})</p>
                <input type="hidden" id="service_id" value="{{ $service_list->id }}">
              </div>
              <div class="educationalservices__card__pricing">
                <div class="d-flex align-items-sm-center align-items-start justify-content-sm-between flex-column flex-sm-row">
                  <h4>₹{{ number_format($service_list->educationService->discounted_price, 2) }}<span>₹{{ number_format($service_list->price,2) }}</span></h4>
                  @if ($service_list->buy_link)
                  <a href="javascript:void();" href = "{{ $service_list->buy_link }}" target="_blank" >Buy now</a>
                  @else
                  <a href="javascript:void();" onclick="showEmailInput('{{ $service_list->id }}');">Buy now</a>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
  @include('Frontend.includes.footer')
</div>
@endsection
@section('js')
<script src="{{ asset('assets/Frontend/js/services.js') }}"></script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection