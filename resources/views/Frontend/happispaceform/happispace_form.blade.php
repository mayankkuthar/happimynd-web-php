<?php $curentPageUrl = $_SERVER['REQUEST_URI'] ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance." />
        <link rel="canonical" href="{{ url('/') }}{{ $curentPageUrl }}" />
        <base href="{{ url('/') }}" />

        <title>Happimynd | HappiSpace Form</title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/Frontend/css/report.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/landing.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Frontend/css/main.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
        <style>
          .whtsapp-contact-block {
    max-width: 40%;
    margin: 0 auto;
    padding: 40px;
    box-shadow: 0px 1px 50px #0000001a;
    background: #ffffff;
    border-radius: 20px;
}

.whtsapp-contact-block h4 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 24px;
    line-height: 1.3;
}

.whtsapp-contact-block .detailsbar {
    display: flex;
    align-items: center;
    justify-content: center;
}

.whtsapp-contact-block .detailsbar img {
    max-width: 70px;
}

.whtsapp-contact-block .detailsbar .rightbar {
    margin-left: 20px;
}

.whtsapp-contact-block .detailsbar .rightbar h6 a {
    font-size: 16px;
    color: #5bc4cb;
    text-decoration: underline;
}

.whtsapp-contact-block .detailsbar .rightbar h6 {
    margin-bottom: 10px;
}

        </style>

      </head>
    <body class="font-sans antialiased">
      @include('Frontend.includes.header')
      <div class="happispace__contact__form">
        <!-- <script type="text/javascript" data-b24-form="inline/5/jwtx5o" data-skip-moving="true">
          (function(w,d,u){
            var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
            var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
          })(window,document,"{{ $happySpace_cdnlink ? $happySpace_cdnlink:'' }}");
        </script> -->
        <div class="whtsapp-contact-block">
          <h4>Click to connect with us</h4>
          <div class="detailsbar">
            <!-- <img src="{{ asset('assets/Frontend/images/whatsapp.png') }}" alt='whatsapp-icon'/> -->
            <div class="rightbar">
              <h6><a href='https://wa.me/919136899581' target="blank"><img src="{{ asset('assets/Frontend/images/whatsapp.png') }}" alt='whatsapp-icon'/></a></h6>
              <!-- <h6><a href="tel:+91 91105 99581">91105 99581</a></h6> -->
            </div>
          </div>
        </div>
      </div>
      @include('Frontend.includes.footer')
      <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src="{{ asset('assets/Backend/js/plugins/bootstrap.bundle.min.js') }}"></script>
      <script src="{{ asset('assets/Frontend/js/main.js') }}"></script>
      <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
      <script>
        AOS.init();
      </script>
    </body>
</html>