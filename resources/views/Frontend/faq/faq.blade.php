@extends('layouts.app')

@section('title', 'FAQ | HappiMynd')
@section('description', 'In FAQ get answers for all the questions related to HappiMynd and its services related to emotional and mental wellbeing.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')
  <section>
    <div class="section11" id="faq">
      <div class="container">
        <div class="section11__content">
          <h1>General FAQs</h1>
          <div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="accordion" id="accordionExample">
              @isset($generalFaqs)
                  @foreach ($generalFaqs as $generalFaq)      
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
        </div>
      </div>
    </div>
  </section>
  <section>
    <div class="section11" id="organisationfaq">
      <div class="container">
        <div class="section11__content" >
          <h1>FAQ related to organisation</h1>
          <div>
            <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="accordion" id="accordionExample">
              @isset($organizationFaqs)
                  @foreach ($organizationFaqs as $organizationFaq)
                      
              <div class="">
                <div class="" id="heading{{ $organizationFaq->id }}">
                  <h2 class="section11__content-btn" data-toggle="collapse" data-target="#collapse{{ $organizationFaq->id }}" aria-expanded="false" aria-controls="collapse1">
                    {{ $organizationFaq->title }}
                    <span>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                  </h2>
                </div>
                <div id="collapse{{ $organizationFaq->id }}" class="collapse collapsed" aria-labelledby="heading{{ $organizationFaq->id }}" data-parent="#accordionExample">
                  <p class="section11__content-desc">
                    {!! $organizationFaq->content !!}
                  </p>
                </div>
              </div>
              <hr style="border-top:1px solid #B6B9C3;">
              @endforeach
              @endisset
        </div>
      </div>
    </div>
  </section>
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection