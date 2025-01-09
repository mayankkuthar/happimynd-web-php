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

        <style>
                /* Modal Backdrop */
                #contact-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* Modal Content */
        #contact-modal .modal-content {
            background: #ffffff;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
            text-align: center;
        }

        /* Form Styles */
        #contact-modal h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        #contact-modal p {
            font-size: 14px;
            margin-bottom: 20px;
            color: #666;
        }

        #contact-modal form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #contact-modal .form-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        #contact-modal input,
        #contact-modal select,
        #contact-modal textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        #contact-modal textarea {
            resize: none;
            height: 80px;
        }

        #contact-modal button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        #contact-modal .submit-btn {
            background-color: #aaf2ee;
            color: #000;
            font-weight: bold;
        }

        #contact-modal .submit-btn:hover {
            background-color: #90e8e2;
        }

        #contact-modal .close-btn {
            background-color: #ff5c5c;
            color: white;
            margin-top: 10px;
        }

        #contact-modal .close-btn:hover {
            background-color: #ff2c2c;
        }

        /* Fade-in Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
        </style>

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
        <div id="contact-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <h2>Contact Us</h2>
                <p>Reach out and we'll get in touch with you.</p>
                <form id="contact-form">
                    <div class="form-group">
                        <input type="text" id="first-name" name="first_name" placeholder="First Name*" required />
                        <input type="text" id="last-name" name="last_name" placeholder="Last Name*" required />
                    </div>
                    <div class="form-group">
                        <input type="tel" id="phone-number" name="phone_number" placeholder="Phone Number*" required />
                    </div>
                    <div class="form-group">
                        <select id="reason" name="reason" required>
                            <option value="">Select Reason*</option>
                            <option value="query">General Query</option>
                            <option value="support">Support</option>
                            <option value="feedback">Feedback</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" placeholder="Enter Message" required></textarea>
                    </div>
                    <div class="form-group">
                        <select id="find-out" name="find_out">
                            <option value="">How did you find out about us?</option>
                            <option value="google">Google</option>
                            <option value="social-media">Social Media</option>
                            <option value="friend">Friend</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
                <button id="close-modal" class="close-btn">Close</button>
            </div>
        </div>
        <div 
            style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; width: 60px; height: 60px;">
            <a href="javascript:void(0)" id="contact-now-btn">
            <img 
                src="https://www.pngfind.com/pngs/m/671-6712663_png-logo-of-contact-us-transparent-png.png" 
                alt="Contact Us" 
                style="width: 100%; height: 100%; border-radius: 50%; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); cursor: pointer;">
            </a>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
            const contactNowBtn = document.getElementById("contact-now-btn");
            const contactNowBtn1 = document.getElementById("contact-now-btn-1");
            const contactNowBtn2 = document.getElementById("contact-now-btn-2");
            const modal = document.getElementById("contact-modal");
            const closeModalBtn = document.getElementById("close-modal");

            // Function to open modal
            function openModal(event) {
                event.preventDefault();
                modal.style.display = "flex";
            }

            // Attach event listener if button exists
            if (contactNowBtn) {
                contactNowBtn.addEventListener("click", openModal);
            }

            if (contactNowBtn1) {
                contactNowBtn1.addEventListener("click", openModal);
            }

            if (contactNowBtn2) {
                contactNowBtn2.addEventListener("click", openModal);
            }

            // Close modal on clicking 'Close' button
            if (closeModalBtn) {
                closeModalBtn.addEventListener("click", function () {
                    modal.style.display = "none";
                });
            }

            // Close modal when clicking outside the modal content
            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });

        document.getElementById("contact-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/submit-contact', {
            method: 'POST',
            headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
            alert(data.message);
            document.getElementById("contact-modal").style.display = "none";
            })
            .catch(error => console.error('Error:', error));
        });

        </script>
    </body>
</html>
