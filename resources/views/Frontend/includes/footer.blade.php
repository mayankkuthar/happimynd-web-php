<footer>
  <div class="footer">
    <div class="container">
      <div class="row">


        <div class="col-lg-3 col-md-4 col-sm-6">
          <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="footer__lists">
              <h1 style="margin-bottom: 4px;">Get the app now</h1>
            <!-- <a href=" " target="_blank"> -->

            <?php
              $android_app_link = Session::get('play_store_link');
            ?>
            <a href="{{$android_app_link}}">
              <img src="{{ asset('assets/Frontend/images/play_store.png') }}" style="height: 50px; margin-bottom: 15px;">
            </a>
            <br>
            <?php
              $ios_app_link = Session::get('app_store_link');
            ?>
            <!-- <a href=" " target="_blank"> -->
            <a href="{{$ios_app_link}}">
              <img src="{{ asset('assets/Frontend/images/app_store.png') }}" style="height: 50px;">
            </a>
          </div>
          
        </div>

        <div class="col-lg-9">
          <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="footer__lists">
                <h1>About</h1>
                <ul>
                  <li><a href="{{ route('ourteam') }}" >Join the Network</a></li>
                  <li><a href="{{ route('faq') }}">FAQ</a></li>
                  <li><a href="{{ route('blog') }}">Blog</a></li>
                  {{-- <li><a href="javascript:void(0);" onclick="showCommingSoonPop();">Experts</a></li> --}}
                  <li><a href="{{ route('ourteam') }}/#teams">Our Team</a></li>
                  <li><a href="{{ route('aboutus') }}">About Us</a></li>
                </ul>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="footer__lists">
                <h1>Services</h1>
                <ul>
                  <li><a href="{{ route('services') }}/#happilife" onclick="getHashValue('happilife');"><span id="life1">HappiLIFE</span></a></li>
                  <li><a href="{{ route('services') }}/#happiapp" onclick="getHashValue('happiapp');"><span id="app1">HappiSELF</span></a></li>
                  <li><a href="{{ route('services') }}/#happichat" onclick="getHashValue('happichat');"><span id="chat1">HappiBUDDY</span></a></li>
                  <li><a href="{{ route('services') }}/#happitalk" onclick="getHashValue('happitalk');"><span id="talk1">HappiTALK</span></a></li>
                  <li><a href="{{ route('services') }}/#happispace" onclick="getHashValue('happispace');"><span id="space1">HappiLEARN</span></a></li>
                  <li><a href="{{ route('services') }}/#happiguide" onclick="getHashValue('happiguide');"><span id="guide1">HappiGUIDE</span></a></li>

                </ul>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="footer__lists">
                <h1>Useful links</h1>
                <ul>
                  <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                  <li><a href="{{ route('getTerms') }}">Terms of Service</a></li>
                  {{-- <li><a href="javascript:void(0);" onclick="showCommingSoonPop();">Contact Us</a></li>
                  <li><a href="javascript:void(0);" onclick="showCommingSoonPop();">Raise a query</a></li> --}}
                </ul>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="footer__lists">
                
                <h1>Contact Us</h1>
                <p>Sec-49,<br>Gurgaon, India <br>Offices at Gurgaon & London</p>
                <p>Contact No: 9136899581</p>

                <div class="footer__social-icons" style="display: flex;">
                  <a href="https://www.facebook.com/HappiMynd/" target="_blank">
                    <img src="{{ asset('assets/Frontend/images/facebook_img.png') }}" >
                  </a>
                  <a href="https://twitter.com/happi_mynd" target="_blank">
                    <img src="{{ asset('assets/Frontend/images/twitter_img.png') }}" >
                  </a>
                  <a href="https://www.instagram.com/happimynd/" target="_blank">
                    <img src="{{ asset('assets/Frontend/images/insta_img.png') }}" >
                  </a>
                  {{-- <a href="{{ url('/') }}" target="_blank">
                    <img src="{{ asset('assets/Frontend/images/pinterest_img.svg') }}" >
                  </a> --}}
                  <a href="https://www.linkedin.com/company/happimynd/" target="_blank">
                    <img src="{{ asset('assets/Frontend/images/linkedin_img.png') }}" >
                  </a>
                  <a href="https://in.pinterest.com/happi_mynd/_created/" target="_blank">
                    <img src="{{ asset('assets/Frontend/images/pinterest.png') }}" >
                  </a>
                </div>


              </div>
            </div>
          </div>
        </div>




      </div>
      <p class="footer__copyright__text" style="text-align: center;">Copyright &copy; <script>document.write(new Date().getFullYear())</script> HappiMynd, All rights reserved</p>
    </div>
  </div>
</footer>
@php
    $cdnlink = getCdnLink();
@endphp
@section('js')

  <script type="text/javascript">
    (function(w,d,u){
          var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
          var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
  })(window,document,"{{ $cdnlink }}");


  $.ajax({
      type: "get",
      url:'{{route("getServiceButtonData")}}',
      dataType: "json",
      success: function(result) {
        if(result.success==1){
          document.getElementById("life1").innerHTML=result.data[4].title;
          document.getElementById("app1").innerHTML=result.data[0].title;
          document.getElementById("chat1").innerHTML=result.data[3].title;
          document.getElementById("talk1").innerHTML=result.data[1].title;
          document.getElementById("space1").innerHTML=result.data[2].title;
        }
      }
    })
  </script>
@endsection

