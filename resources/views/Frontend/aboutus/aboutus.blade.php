@extends('layouts.app')

@section('title', 'Happymind | Our Team')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  <div class="ourteam">
    <div class="ourteam__sec1">
      <div class="container">
        <div data-aos="fade-up" data-aos-duration="500" data-aos-once="true" class="ourteam__sec1__content">
          <h1 style="font-size: 1rem;line-height: 24px;">Emotional & Mental Health remains an ignored subject in India until it visibly & significantly starts impacting the overall quality of life. We believe building conversations around Emotional & Mental Wellbeing and their management in daily life are critical to the success of any individual or organisation. Empowering you to Self Manage your holistic lifestyle without any compromise in privacy & confidentiality. Take the first step today! 
          <br>
          <br>

          HappiMynd is an end-to-end tech enabled platform aimed at redefining emotional, behavioural, and mental wellbeing while focusing on Awareness, Prevention, Early Detection, Self-Management & Therapeutic Treatment, along with personalised assistance.
          <br>
          <br>
          
          We use positive aspects of psychology to bring companies and individuals accessible, affordable, reliable, and science-backed solutions and create long lasting impactful outcomes with utmost confidentiality. We have many Industry Firsts under our belt, one of them being a customised engagement path for 10 unique social profiles.
          Start the journey towards knowing yourself with HappiMynd!
          </h1>
          <div class="ourteam__sec1__img">
            <img src="{{ asset('assets/Frontend/images/ourteam_sec1-img.svg') }}" >
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
