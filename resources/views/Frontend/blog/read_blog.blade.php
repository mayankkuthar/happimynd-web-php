@extends('layouts.app')

@section('title', 'Happimynd | Read Blog')


@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')

<div id="container1"> 
  <style>
        audio{
        min-width: 400px;
        height: auto;
        object-fit: inherit;
    }
  </style>
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')
  <div class="readblog">
    @if($post)
    <div class="container readblog__img text-center">
      @if ($post->post_category_id == 2)
      <video width="900" poster="{{ $post->getContentWithS3Url('blog') ?? '' }}" controls preload="none">
        <source src="{{ $post->getContentWithS3Url('blog') ?? '' }}" type="video/mp4">
        Your browser does not support the video tag.
      </video>
      @else
      <img src="{{ $post->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/blog/readblog_img1.png') }}" alt="readblogimg" />
      @endif
    </div>
    <div class="container">
      <div class="readblog__text">
        <h1>{{ $post->title }}</h1>
        {{-- <h2>Admin<span>  |  {{ $post->created_at->format('d M Y') }}</span></h2> --}}
        <p>{!! $post->description !!}</p>
      </div>
    </div>
    @endif
    @if($relatedArticle)
    <div class="readblog__related">
      <div class="container">
        <h1>Related Articles</h1>
        <div class="blog__content__blogs__cards">
          <div class="row">
            @foreach ($relatedArticle as $blog)
            <div class="col-lg-4 col-md-6 col-sm-6">
              <a href="{{ route('readFreeBlog', [$blog->slug]) }}">
                <div class="row no-gutters">
                  <div class="col-lg-12">
                    <div class="blog__content__blogs__cards__img">
                      @if ($blog->post_category_id == 1)
                      <img src="{{ $blog->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/profile_blog_img1.webp') }}" alt="blogimg1" />
                      @elseif($blog->post_category_id ==2)
                      <img src="{{ $blog->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/blog/video_img1.png') }}" alt="blogimg1" />
                      @else
                      <img src="{{ $blog->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/blog/audio_img1.svg') }}" alt="blogimg1" />
                      <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.09127 22.0166C1.38386 22.0166 0 23.4005 0 25.1079V54.8926C0 56.6 1.38386 57.9839 3.09127 57.9839C4.79868 57.9839 6.18254 56.6 6.18254 54.8926V25.1079C6.18254 23.4005 4.79868 22.0166 3.09127 22.0166Z" fill="white"/>
                        <path d="M17.8549 13.084C16.1474 13.084 14.7636 14.4678 14.7636 16.1753V63.8255C14.7636 65.5329 16.1474 66.9167 17.8549 66.9167C19.5623 66.9167 20.9461 65.5329 20.9461 63.8255V16.1753C20.9461 14.4678 19.5623 13.084 17.8549 13.084Z" fill="white"/>
                        <path d="M32.6184 0.651367C30.911 0.651367 29.5271 2.03523 29.5271 3.74264V76.2573C29.5271 77.9647 30.911 79.3486 32.6184 79.3486C34.3258 79.3486 35.7096 77.9647 35.7096 76.2573V3.74264C35.7096 2.03557 34.3258 0.651367 32.6184 0.651367Z" fill="white"/>
                        <path d="M47.3815 18.6719C45.6741 18.6719 44.2903 20.0557 44.2903 21.7631V58.2377C44.2903 59.9451 45.6741 61.329 47.3815 61.329C49.0889 61.329 50.4728 59.9451 50.4728 58.2377V21.7631C50.4728 20.0557 49.0889 18.6719 47.3815 18.6719Z" fill="white"/>
                        <path d="M62.1451 22.0166C60.4377 22.0166 59.0539 23.4005 59.0539 25.1079V54.8926C59.0539 56.6 60.4377 57.9839 62.1451 57.9839C63.8525 57.9839 65.2364 56.6 65.2364 54.8926V25.1079C65.2364 23.4005 63.8522 22.0166 62.1451 22.0166Z" fill="white"/>
                        <path d="M76.9087 31.915C75.2013 31.915 73.8174 33.2989 73.8174 35.0063V44.9935C73.8174 46.7009 75.2013 48.0848 76.9087 48.0848C78.6161 48.0848 80 46.7009 80 44.9935V35.0063C80 33.2989 78.6161 31.915 76.9087 31.915Z" fill="white"/>
                      </svg>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="blog__content__blogs__cards__text">
                      @php
                          $show = $blog->title .' : '.strip_tags($blog->description)
                      @endphp
                      <h3>{{ $blog->title  }}</h3>
                      {{-- <p>{{ $blog->created_at->format('d M Y') }}</p> --}}
                    </div>
                  </div>
                </div>
              </a>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif
    @if ($post->post_category_id == 3)
    <div class="readblog__related__audio">
      <audio id="audio-1">
        {{-- <source src="horse.ogg" type="audio/ogg"> --}}
        <source src="{{ $post->getContentWithS3Url('blog') ?? "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3"  }}" type="audio/mp3">
        Your browser does not support the audio element.
      </audio>
      <div class="container">
        <div class="readblog__related__audio__content">
          <div class="readblog__related__audio__content__image__parent">
            <div>
              <div class="readblog__related__audio__content__image d-flex align-items-center justify-content-center">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1.23651 8.80664C0.553543 8.80664 0 9.36018 0 10.0431V21.957C0 22.64 0.553543 23.1936 1.23651 23.1936C1.91947 23.1936 2.47302 22.64 2.47302 21.957V10.0431C2.47302 9.36018 1.91947 8.80664 1.23651 8.80664Z" fill="white"/>
                  <path d="M7.14196 5.23413C6.459 5.23413 5.90546 5.78767 5.90546 6.47064V25.5307C5.90546 26.2137 6.459 26.7672 7.14196 26.7672C7.82493 26.7672 8.37847 26.2137 8.37847 25.5307V6.47064C8.37847 5.78767 7.82493 5.23413 7.14196 5.23413Z" fill="white"/>
                  <path d="M13.0474 0.260742C12.3644 0.260742 11.8109 0.814285 11.8109 1.49725V30.5031C11.8109 31.1861 12.3644 31.7396 13.0474 31.7396C13.7303 31.7396 14.2839 31.1861 14.2839 30.5031V1.49725C14.2839 0.814423 13.7303 0.260742 13.0474 0.260742Z" fill="white"/>
                  <path d="M18.9526 7.46875C18.2697 7.46875 17.7161 8.02229 17.7161 8.70526V23.2951C17.7161 23.9781 18.2697 24.5316 18.9526 24.5316C19.6356 24.5316 20.1891 23.9781 20.1891 23.2951V8.70526C20.1891 8.02229 19.6356 7.46875 18.9526 7.46875Z" fill="white"/>
                  <path d="M24.858 8.80664C24.1751 8.80664 23.6215 9.36018 23.6215 10.0431V21.957C23.6215 22.64 24.1751 23.1936 24.858 23.1936C25.541 23.1936 26.0945 22.64 26.0945 21.957V10.0431C26.0945 9.36018 25.5409 8.80664 24.858 8.80664Z" fill="white"/>
                  <path d="M30.7635 12.7661C30.0805 12.7661 29.527 13.3197 29.527 14.0026V17.9975C29.527 18.6805 30.0805 19.234 30.7635 19.234C31.4465 19.234 32 18.6805 32 17.9975V14.0026C32 13.3197 31.4465 12.7661 30.7635 12.7661Z" fill="white"/>
                </svg>
              </div>
            </div>
            <div class="mobile-text"><p>{{ $post->title  }}</p></div>
            <div class="readblog__related__audio__content__controls d-flex align-items-center">
              @if ($post->previous)         
              <a href="{{ route('readFreeBlog', [$post->previous->slug]) }}" class="prev">
                <svg width="39" height="24" viewBox="0 0 39 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18 13.7321C16.6667 12.9623 16.6667 11.0377 18 10.2679L30 3.33975C31.3333 2.56995 33 3.5322 33 5.0718L33 18.9282C33 20.4678 31.3333 21.4301 30 20.6603L18 13.7321Z" fill="#49516A"/>
                  <path d="M3 13.7321C1.66667 12.9623 1.66667 11.0377 3 10.2679L15 3.33975C16.3333 2.56995 18 3.5322 18 5.0718L18 18.9282C18 20.4678 16.3333 21.4301 15 20.6603L3 13.7321Z" fill="#49516A"/>
                </svg>
              </a>
              @endif
              <div class="readblog__related__audio__content__controls__playpause">
                <svg class="audio-pause" width="22" height="26" viewBox="0 0 22 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="6" height="26" rx="3" fill="#49516A"/>
                  <rect x="12" width="6" height="26" rx="3" fill="#49516A"/>
                </svg>
                <svg class="audio-play" width="22" height="26" viewBox="0 0 22 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M21 11.2679C22.3333 12.0378 22.3333 13.9622 21 14.7321L3 25.1244C1.66666 25.8942 -1.22094e-06 24.9319 -1.15364e-06 23.3923L-2.4512e-07 2.60769C-1.77822e-07 1.06809 1.66667 0.105843 3 0.875644L21 11.2679Z" fill="#49516A"/>
                </svg>
              </div>
              @if ($post->next)     
              <a href="{{ route('readFreeBlog', [$post->next->slug]) }}" class="next">
                <svg width="39" height="24" viewBox="0 0 39 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M21 10.2679C22.3333 11.0377 22.3333 12.9623 21 13.7321L9 20.6603C7.66667 21.4301 6 20.4678 6 18.9282L6 5.0718C6 3.5322 7.66667 2.56995 9 3.33975L21 10.2679Z" fill="#49516A"/>
                  <path d="M36 10.2679C37.3333 11.0377 37.3333 12.9623 36 13.7321L24 20.6603C22.6667 21.4301 21 20.4678 21 18.9282L21 5.0718C21 3.5322 22.6667 2.56995 24 3.33975L36 10.2679Z" fill="#49516A"/>
                </svg>
              </a>
              @endif
            </div>
          </div>
          <div class="readblog__related__audio__content__progress w-100">
            <p>{{ $post->title  }}</p>
            <div class="readblog__related__audio__content__progress__bar">
              <div class="readblog__related__audio__content__progress__bar__fill d-flex align-items-center">
                <div class="readblog__related__audio__content__progress__bar__filled"></div>
                <div class="readblog__related__audio__content__progress__bar__filled-dot" id="ball-dot"></div>
              </div>
              <div class="readblog__related__audio__content__progress__bar__timer">
                <span class="running_time">0:00</span>
                <span class="total_time">4:32</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection