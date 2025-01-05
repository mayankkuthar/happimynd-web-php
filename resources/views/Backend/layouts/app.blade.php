<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script type="text/javascript">
            csrf_token = document.getElementsByName('csrf-token')[0].content;
        </script>

        <title>{{ 'Admin | '.$title ?? '' }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <link href="{{ asset('assets/Backend/css/plugins/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/font-awesome.min.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/Backend/css/plugins/skins/all.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('assets/Backend/css/plugins/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/jqvmap.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/animate.min.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('assets/Backend/css/plugins/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('assets/Backend/css/plugins/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/buttons.bootstrap.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/cropper.min.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('assets/Backend/css/plugins/daterangepicker.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('assets/Backend/css/plugins/dropzone.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('assets/Backend/css/plugins/fixedHeader.bootstrap.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/fullcalendar.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/fullcalendar.print.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('assets/Backend/css/plugins/ion.rangeSlider.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/jquery.mCustomScrollbar.min.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('assets/Backend/css/plugins/mocha.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/morris.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/normalize.css') }}" rel="stylesheet"> --}}
            <!-- PNotify -->
        <link href="{{ asset('assets/Backend/css/plugins/pnotify/pnotify.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/pnotify/pnotify.buttons.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/pnotify/pnotify.nonblock.css') }}" rel="stylesheet">

        {{-- <link href="{{ asset('assets/Backend/css/plugins/prettify.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/responsive.bootstrap.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ asset('assets/Backend/css/plugins/starrr.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('assets/Backend/css/plugins/style.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/switchery.min.css') }}" rel="stylesheet">
        <!-- Custom styling plus plugins -->
        <link href="{{ asset('assets/Backend/css/custom.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
        <link href="{{ asset('assets/Backend/css/plugins/nprogress.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/buttons.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/responsive.bootstrap.min.css') }}" rel="stylesheet">

        {{ $css ?? '' }}
        <style>
             .compulsory:before {
              color: red;
              content: " *";
              }
        </style>
        @laravelPWA

        <!-- Google tag (gtag.js) Last updated on 2024/04/04 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-11151349334">
        </script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'AW-11151349334');
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @if(auth('admin')->user())
                <!-- Sidebar -->
                @include('Backend.includes.sidebar')
                <!-- /Sidebar -->
                <!-- top navigation -->
                @include('Backend.includes.top_navbar')
                <!-- /top navigation -->
            @endif
            {{ $content ?? ''}}
        </div>

        <!-- jQuery -->
        <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/bootstrap.bundle.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/fastclick.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/nprogress.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/Chart.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/gauge.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/bootstrap-progressbar.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/icheck.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/skycons.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.pie.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.time.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.stack.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.resize.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.orderBars.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.flot.spline.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/curvedLines.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.vmap.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.vmap.world.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.vmap.sampledata.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/moment.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/daterangepicker.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/autosize.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/bootstrap-colorpicker.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/bootstrap-datetimepicker.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/bootstrap-wysiwyg.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/date.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/cropper.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/dropzone.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/echarts.common.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/echarts.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/echarts.simple.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/eve.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/fullcalendar.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/ion.rangeSlider.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/buttons.bootstrap.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.colVis.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/responsive.bootstrap.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.autocomplete.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.easypiechart.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.hotkeys.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.inputmask.bundle.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/jquery.knob.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/jquery.mCustomScrollbar.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/jquery.mousewheel.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.smartWizard.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.sparkline.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/jquery.tagsinput.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/jszip.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/mocha.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/morris.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/parsley.min.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/vfs_fonts.js') }}"></script>
                <!-- PNotify -->
        <script src="{{ asset('assets/Backend/js/plugins/pnotify/pnotify.js')}}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/pnotify/pnotify.buttons.js')}}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/pnotify/pnotify.nonblock.js')}}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/prettify.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/raphael.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/select2.full.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/starrr.js') }}"></script> --}}
        <script src="{{ asset('assets/Backend/js/plugins/switchery.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/Backend/js/plugins/transitionize.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/validator.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/Backend/js/plugins/require.js') }}"></script> --}}

        <!-- Custom Theme Scripts -->
        <script src="{{ asset('assets/Backend/js/main.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/custom.js') }}"></script>
        <script type="text/javascript">
          $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrf_token
            }
        });
        $(document).ajaxSuccess(function(event, request, settings) {
          console.log('from common success')
          var response = request.responseJSON;
          if(response){
            if(response.message.notify){
              var notify = response.message.notify;
              new PNotify({title: notify.type,text: notify.message,type: notify.type,styling: 'bootstrap3'});
            }
          }
        });
        $(document).ajaxError(function(event, request, settings) {
          var response = request.responseJSON;
          console.log('from common error')
          if(response.message){
            if(response.message.notify){
              var notify = response.message.notify;
              new PNotify({title: 'Error',text: notify.message,type: 'error',styling: 'bootstrap3'});
            }
          }
          else{
            new PNotify({title: 'Error',text: "Some problem occured, Please try again",type: 'error',styling: 'bootstrap3'});
          }
        });
        </script>
        <script src="{{ asset('assets/Backend/js/plugins/fastclick.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/nprogress.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/icheck.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/responsive.bootstrap.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/vfs_fonts.js') }}"></script>
        {{ $js ?? '' }}
    </body>
</html>
