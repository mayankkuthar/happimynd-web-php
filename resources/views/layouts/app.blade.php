<?php $curentPageUrl = $_SERVER['REQUEST_URI'] ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="p:domain_verify" content="1537664bb82efdd62d8e8c7d51d32a85"/>
        
        <meta name="facebook-domain-verification" content="h4zd8pmpramc122gdr4gjxvt3t3k2b"/>
        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '879732019488970');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=879732019488970&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->

        <link rel="canonical" href="{{ url('/') }}{{ $curentPageUrl }}" />
        <base href="{{ url('/') }}" />
        @if(App::environment('production'))
        {{-- only on production environment --}}
          <!-- Global site tag (gtag.js) - Google Analytics -->
          <script async src="https://www.googletagmanager.com/gtag/js?id=UA-190255057-1"></script>
          <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-190255057-1');
          </script>

          <!-- Google Tag Manager -->
          <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-P3VGFBK');
          </script>
          <!-- End Google Tag Manager -->


        <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '872856973568219');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=872856973568219&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Facebook Pixel Code -->
        @endif
        <meta name="description" content="@yield('description')" />
        <meta name="keywords" content="@yield('keywords')" />
        
        <title>@yield('title')</title>

        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> --}}
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Styles -->
        {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
        <link href="{{ asset('assets/Backend/css/plugins/bootstrap.min.css') }}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/font-awesome.min.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('assets/css/plugins/aos.css') }}" rel="stylesheet"> --}}
        <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

        <link href="{{ asset('assets/css/plugins/owl.carousel.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/plugins/owl.theme.default.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/landing.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/account.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/terms.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/ourteam.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/popups.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/dashboard.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/exploreservices.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/downloadreport.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/assessment.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/errors.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/organisation.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/report.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/payment_bundles.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/blog.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/psychologist.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/services.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/main.css') }}" rel="stylesheet">
        {{-- Jqurey Datepicker theme CSS --}}
        <link href="https://code.jquery.com/ui/1.12.1/themes/redmond/jquery-ui.css" rel="stylesheet">
        <link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/redmond/jquery-ui.css" rel="stylesheet">
        <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <!-- Scripts -->
        {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
        @laravelPWA
    </head>
    <body class="font-sans antialiased">
      @include('Frontend.includes.toast')
        <div class="min-h-screen bg-gray-100">
            {{-- @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main> --}}
            @yield('content')
        </div>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3VGFBK"
          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
          <!-- End Google Tag Manager (noscript) -->

        <script src="{{ asset('assets/Backend/js/plugins/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('assets/Frontend/js/landing.js') }}"></script>
        <script src="{{ asset('assets/Frontend/js/blog.js') }}"></script>
        <script src="{{ asset('assets/Frontend/js/main.js') }}"></script>

        {{-- <script src="{{ asset('assets/js/plugins/aos.js') }}"></script> --}}
        <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
        <script>
          AOS.init();
        </script>
        @yield('js')
    </body>
</html>
