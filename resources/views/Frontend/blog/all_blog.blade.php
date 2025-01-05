@extends('layouts.app')

@section('title', 'Happimynd | Blog')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.commingsoon')
  <div class="blog">
    <div class="blog__header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-3 col-md-4 col-sm-12 col-12">
            <h1>Happimynd Library</h1>
          </div>
          <div class="col-lg-6 col-md-4 col-sm-6 col-6">
            <ul class="blog__header__list">
              <li><a class="text-decoration-none"  href="{{ route('blog') }}">All</a> </li>
              <li class="{{ $posts->id == 1 ? "active": '' }}"><a class="text-decoration-none" href="{{ route('allblogs', ['blog']) }}">Blogs</a> </li>
              {{-- <li class="{{ $posts->id == 2 ? "active": '' }}"><a class="text-decoration-none" href="{{ route('allblogs', ['video']) }}">Videos</a> </li> --}}
              {{-- <li class="{{ $posts->id == 3 ? "active": '' }}"><a class="text-decoration-none" href="{{ route('allblogs', ['audio']) }}">Audios</a> </li> --}}
            </ul>
            <div class="blog__header__mob-dropdown">
              <select class="blog__header__mob-dropdown__filter">
                <option>Filter</option>
                <option><a class="text-decoration-none"  href="{{ route('blog') }}">All</a></option>
                <option><a class="text-decoration-none" href="{{ route('allblogs', ['blog']) }}">Blogs</a></option>
                {{-- <option><a class="text-decoration-none" href="{{ route('allblogs', ['video']) }}">Videos</a></option> --}}
                {{-- <option><a class="text-decoration-none" href="{{ route('allblogs', ['audio']) }}">Audios</a></option> --}}
              </select>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6 col-6">
            <div class="blog__header__search">
              {{-- <input type="text" placeholder="Search" /> --}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="blog__content">
      <div class="container">
        <div class="blog__content__blogs">
          <div class="blog__content__blogs__cards">
            <div class="row">
              @if ($posts)
              @foreach ($posts->post as $post)
              <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                <a href="{{ route('readFreeBlog', [$post->slug]) }}">
                  <div class="row no-gutters">
                    <div class="col-lg-12">
                      <div class="blog__content__blogs__cards__img">
                        @if ($post->post_category_id == 1)
                        <img src="{{ $post->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/profile_blog_img1.webp') }}" alt="blogimg1" />
                        @elseif($post->post_category_id == 2)
                        <img src="{{ $post->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/profile_blog_img1.webp') }}" alt="blogimg1" />
                        @else 
                          <img src="{{ $post->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/blog/audio_img1.svg') }}" alt="blogimg1" />
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
                            $show = $post->title .' : '.strip_tags($post->description)
                        @endphp
                        <h3>{{ $post->title }}</h3>
                        {{-- <p>{{ $post->created_at->format('d M Y') }}</p> --}}
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              @endforeach
              @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection