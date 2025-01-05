@extends('layouts.app')

@section('title', 'Happimynd | Blog')

@section('description', "HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.")


@section('keywords', 'early signs, emotional well-being, deterioration, feelings of helplessness, hopelessness, worthlessness, emotional problems, anxiety, depression, self care, happy, holistic, hormones, exercise, food, happy hormones, neurotransmitters, scientific, lifestyle, tips, positive life, mental health, hormonal imbalance estrogen, progesterone, testosterone, mental disorders, mood disorder, cognitive impairment, Peer pressure, Low self-confidence, Low self-esteem, Overcome peer pressure, Managing peer pressure, Alcohol pressure, Unhealthy dynamics, Cope, Bystander intervention, Social circle, Adolescence, Friend, Mental Burnout, Symptoms of Burnout, Burnout Syndrome, Burnout at Work, Psychology of burnout, work from home, Stress Burnout, consequences, depression, overwork, anxiety, coping, pandemic, corona, COVID 19, Stress Management, Stress Advantages, Manage Stress, Stress Hormone, cortisol, self care, good stress, eustress, distress, self regulation, flow, job satisfaction, emotional regulation, emotional management, emotions, emotional intelligence, emotional quotient, workplace, authorities, confrontation, self-help, holistic life, happy life, psychological self-assessment, work productivity, Age-related mental disorders, Dementia, Geriatric, Alzheimer, Assessment, Prevention, Depression, cognitive ability, dependency, cognitive decline, old age, elderly, ageing population, emotional wellbeing, elderly concerns, how to help the elderly, community services, awareness, Employee productivity, Employee wellness program, Emotional health issues, online gaming, gaming addiction, lockdown, parenting tips, incentives, physical exercise, board games, professional help, virtual counselling, loved one, gratitude, healthy relationships, saying thank you, grateful, support, appreciate, law of reciprocity, Mundane routine, Boredom, monotonous, boring life, monotony, early intervention, assessment, side-effects of boredom, how to stay enthusiastic, how to keep boredom away, things to do when you are bored, Covid-19, Mental Health, corona, pandemic, depression, stress, immunity, psychological effect, psychological care, coping, frustration, unemployment, loneliness, fatigue, True passion, Schedule, Classes, College, College life, University, New phase, Adulthood, Adult, Extracurricular activities, Mental health support,Internal conflict, external conflict, interpersonal conflict, unresolved conflict, conflict management, online therapy, CBT, cost-effective therapy, face-to-face therapy, virtual sessions, data protection, counselling, new normal, overthink, mental health, accept, aware, conscious, focus, worrying, effort, perspective, problem-solving, body image, social media, low self-esteem, eating disorders, body positivity, affirmations, self confidence, emotional, tragedy, pandemic, capabilities, grateful, optimism, difficult, strategies, fear, resilience, positive, goals, Self-esteem, body image, perception, body neutrality, perfections, imperfections, body shaming, employee wellbeing, burnout, wellness programme, mental wellbeing, employee care, employee engagement, Social media, stress, social media detox, intention, addiction, screen time, device, notifications, FOMO, Instagram, self-esteem, social media-worthy,Pregnancy and Mental Health, psychiatric medications, anxiety, depression, stress, prenatal, perinatal, postnatal, parenting, Emotional wellbeing, Personality crisis, Psychological issues, Busy schedule, Self-awareness, Multiple choice questions, Profile-based screening, Holistic wellness, Assisted summary reading, Psychology experts, Personality, traits, characteristics, behavior, personality development, personality test, openness, extrovert, introvert, big 5, facets of personality, five factor model, OCEAN, suicide, stigma, taboo, suicide myths, suicide prevention, suicide helpline, ask for help, mental health, awareness, mental health, workplace, work-life, working professionals, corporate life, corporate professionals, corporate mental health, mental wellbeing check, mental health check, productivity, employers, employees, mental health leave, frontline workers, PTSD, awareness, symptoms, isolation, moral injury, professional help, Coping, practice, solve, cope, blessed, gratitude, mindfulness, unpredictable, appreciation, Stress, pandemic, distress, productivity, workplace, work-life, boundaries, self-care, personal time, communicating, colleagues,Empathy, mental illness, unconditional support, pandemic, mental wellbeing, listening, venting, catharsis, sharing, acceptance, Pandemic, kids, school, child, parenting, behaviour, temper tantrums, empathy, routine, parent-child bond, public speaking, presentation skills, curiosity, surprise, learning styles, fillers, psychological tricks, stage fright, Partner, relationship, couple, negative behaviour, toxic relationships, red flag, controlling, communication, abuse, friendship, true friends, bond, strong nomd, support, love, forgiveness, trust, Relationships, love, positivity, commitment, toxicity, unhealthy attachments, fear, distress, social distancing, new normal, Interview, experience, worry, knowledge, company, questions, candidate, body, power posing, interviewer, confidence, attention span, DIY workspace, distractions, hand taken notes, mental wellbeing, professional help, focus, concentration, distraction, taking breaks, negative peer pressure, workplace pressure, positive peer pressure, setting boundaries, reframing perspective, group conformity, setting limits, boundaries, resilience, rejections, job rejections, pushing limits, emotions, acceptance, learning, safety-net, Emotions, connection, listening, intimacy, understanding, trust, compassion, true love, mutual respect, bond, partner, marriage, pressure , sick, burnout,body,rest,slowdown,sleep,ache, Creativity, experience, mood, hangovers, Work, stress, stressful, difficult, human, concentrate, Decision, decision making, planning, faster, delay, risk, results, Routine, habits, morning, List, productive, night, healthy, breakfast, purpose, self-confidence, hobby, activity, life, improvement, well being, Self care, working, pandemic, office, stress, learn, assert, anger, life, communicate, mind, aggressive, calm, Stress, workplace, personal time, family, self-isolation, stress levels, colleagues, help, holding space, loved ones, connected, judgment, practising, Emotional wellbeing, healthy mind, good habits, stress, healthy mental habits, Relationship, marriage, life partner, family, connect, common interests, commitment, compromise, effort, Self-improvement, Self-management, Emotional wellness, physical health, relaxation, happiness, awareness, self-management skills, mindful, affirmations, power, subconscious, reminders, change, statements, habits, self talk, self awareness, Mental health, anger, relaxation, anger management, controlling anger, self-management, tips, healthy sleep, positive thoughts, Strategies, progress, daily, mistakes, works, remember, Emotional wellbeing, management strategies, emotional regulation, emotions, feelings, identify emotions, acceptance, triggers, wellbeing')

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
              <li><a class="text-decoration-none active"  href="{{ route('blog') }}">All</a> </li>
              @if($blogs)
              <li><a class="text-decoration-none" href="{{ route('allblogs', ['blog']) }}">Blogs</a> </li>
              @endif
              @if($videos)
              <li><a class="text-decoration-none" href="{{ route('allblogs', ['video']) }}">Videos</a> </li>
              @endif
              @if($audios)
              <li><a class="text-decoration-none" href="{{ route('allblogs', ['audio']) }}">Audios</a> </li>
              @endif
            </ul>
            <div class="blog__header__mob-dropdown">
              <div class="dropdown">
                <button class="blog__header__mob-dropdown__filter" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Filter
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" href="{{ route('blog') }}">All</a>
                  @if($blogs)
                  <a class="dropdown-item" href="{{ route('allblogs', ['blog']) }}">Blogs</a>
                  @endif
                  @if($videos)
                  <a class="dropdown-item" href="{{ route('allblogs', ['video']) }}">Videos</a>
                  @endif
                  @if($audios)
                  <a class="dropdown-item" href="{{ route('allblogs', ['audio']) }}">Audios</a>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6 col-6">
            {{-- <div class="blog__header__search">
              <input type="text" placeholder="Search" />
            </div> --}}
          </div>
        </div>
      </div>
    </div>
    <div class="blog__content">
      <div class="container">
        @if ($featured)
        <div class="blog__content__featured">
          <h1>Featured</h1>
          <div class="row">
            <div class="col-lg-6">
              <div class="blog__content__featured__text">
                {{-- <span>{{ $featured->created_at->format('d M Y') }}</span> --}}
                <h2>{{ $featured->title }}</h2>
                <p>{{ Str::limit(strip_tags($featured->description),150, '...') }}</p>
                <a href="{{ route('readFreeBlog', [$featured->slug]) }}">Read</a>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="blog__content__featured__img text-left text-lg-right">
                <img src="{{ $featured->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/featured_img1.webp') }}" alt="blogimg1" />/
              </div>
            </div>
          </div>
        </div>
        @endif
        @if($blogs)
        <div class="blog__content__blogs">
          <div class="blog__content__blogs__heading">
            <h2>Blogs</h2>
            {{-- {{ dd($featured->slug) }} --}}
            <a href="{{ route('allblogs', 'blog') }}" >See More Blogs</a>
          </div>
          
          <div class="blog__content__blogs__cards">
            <div class="owl-carousel owl-theme blog__carousel">
                @foreach ($blogs as $blog)
                {{-- <a href="javascript:void(0);" onclick="showCommingSoonPop();"> --}}
                <a href="{{ route('readFreeBlog', [$blog->slug]) }}">
                  <div class="row no-gutters">
                    <div class="col-lg-12">
                      <div class="blog__content__blogs__cards__img">
                        <img src="{{ $blog->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/profile_blog_img1.webp') }}" alt="blogimg1" />
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
                @endforeach
              </div>
            </div>
          </div>
          @endif
          @if ($videos)
        <div class="blog__content__blogs">
          <div class="blog__content__blogs__heading">
            <h2>Videos</h2>
            <a href="{{ route('allblogs', ['video']) }}">See More Videos</a>
          </div>
          <div class="blog__content__blogs__cards">
            <div class="owl-carousel owl-theme videos__carousel">
              @foreach ($videos as $video)
              <a href="{{ route('readFreeBlog', [$video->slug]) }}">
                <div class="row no-gutters">
                  <div class="col-lg-12">
                    <div class="blog__content__blogs__cards__img">
                      <img src="{{ $video->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/blog/video_img1.png') }}" alt="blogimg1" />
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="blog__content__blogs__cards__text">
                      @php
                        $show = $video->title .' : '.strip_tags($video->description)
                      @endphp
                      <h3>{{ $video->title }}</h3>
                      {{-- <p>{{ $video->created_at->format('d M Y') }}</p> --}}
                    </div>
                  </div>
                </div>
              </a>
              @endforeach
            </div>
          </div>
        </div>
        @endif
        @if($audios)          
        <div class="blog__content__blogs">
          <div class="blog__content__blogs__heading">
            <h2>Audios</h2>
            <a href="{{ route('allblogs', ['audio']) }}">See More Audios</a>
          </div>
          <div class="blog__content__blogs__cards">
            <div class="owl-carousel owl-theme audios__carousel">
              @foreach ($audios as $audio)
              <a href="{{ route('readFreeBlog', [$audio->slug]) }}" >
                <div class="row no-gutters">
                  <div class="col-lg-12">
                    <div class="blog__content__blogs__cards__img">
                      <img src="{{ $audio->getThumbnailWithS3Url('blog') ?? asset('assets/Frontend/images/blog/audio_img1.svg')  }}" alt="audio image" />
                      <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.09127 22.0166C1.38386 22.0166 0 23.4005 0 25.1079V54.8926C0 56.6 1.38386 57.9839 3.09127 57.9839C4.79868 57.9839 6.18254 56.6 6.18254 54.8926V25.1079C6.18254 23.4005 4.79868 22.0166 3.09127 22.0166Z" fill="white"/>
                        <path d="M17.8549 13.084C16.1474 13.084 14.7636 14.4678 14.7636 16.1753V63.8255C14.7636 65.5329 16.1474 66.9167 17.8549 66.9167C19.5623 66.9167 20.9461 65.5329 20.9461 63.8255V16.1753C20.9461 14.4678 19.5623 13.084 17.8549 13.084Z" fill="white"/>
                        <path d="M32.6184 0.651367C30.911 0.651367 29.5271 2.03523 29.5271 3.74264V76.2573C29.5271 77.9647 30.911 79.3486 32.6184 79.3486C34.3258 79.3486 35.7096 77.9647 35.7096 76.2573V3.74264C35.7096 2.03557 34.3258 0.651367 32.6184 0.651367Z" fill="white"/>
                        <path d="M47.3815 18.6719C45.6741 18.6719 44.2903 20.0557 44.2903 21.7631V58.2377C44.2903 59.9451 45.6741 61.329 47.3815 61.329C49.0889 61.329 50.4728 59.9451 50.4728 58.2377V21.7631C50.4728 20.0557 49.0889 18.6719 47.3815 18.6719Z" fill="white"/>
                        <path d="M62.1451 22.0166C60.4377 22.0166 59.0539 23.4005 59.0539 25.1079V54.8926C59.0539 56.6 60.4377 57.9839 62.1451 57.9839C63.8525 57.9839 65.2364 56.6 65.2364 54.8926V25.1079C65.2364 23.4005 63.8522 22.0166 62.1451 22.0166Z" fill="white"/>
                        <path d="M76.9087 31.915C75.2013 31.915 73.8174 33.2989 73.8174 35.0063V44.9935C73.8174 46.7009 75.2013 48.0848 76.9087 48.0848C78.6161 48.0848 80 46.7009 80 44.9935V35.0063C80 33.2989 78.6161 31.915 76.9087 31.915Z" fill="white"/>
                      </svg>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="blog__content__blogs__cards__text">
                      @php
                        $show = $audio->title .' : '.strip_tags($audio->description)
                      @endphp
                      <h3>{{ $audio->title }}</h3>
                      {{-- <p>{{ $audio->created_at->format('d M Y') }}</p> --}}
                    </div>
                  </div>
                </div>
              </a>
              @endforeach
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  @include('Frontend.includes.footer')
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection