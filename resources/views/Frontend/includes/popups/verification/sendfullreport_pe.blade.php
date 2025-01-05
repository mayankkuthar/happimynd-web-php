<?php 
    $user = Auth::user(); 
    $isUserFromOrg =  App\Models\UserToken::where('user_id' , $user->id)->first();
?>


@if($isUserFromOrg)
  <div class="modal fade" id="sendfullreport" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true"  >
@else
<div class="modal fade" id="sendfullreport" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" >
@endif


<!-- <div class="modal fade" id="sendfullreport" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->
  <div class="modal-dialog sendfullreport__popup">
    <div class="modal-content">
      @if($isUserFromOrg)
        <div class="sendfullreport__popup__close" data-dismiss="modal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      @endif
      <div class="sendfullreport__popup__content">
        <h1 id="send_full_report_modal_h1">Send my HappiLIFE Screening Summary</h1>
        <p id=send_full_report_modal_p>Please enter Email address and phone number to get free summary</p>
        <!-- <form class="sendfullreport__popup__form" method="post" action="{{route('user.updateEmail')}}" id="update_email_popup_form"> -->
        <form class="sendfullreport__popup__form" method="post" action="" id="update_email_popup_form">

          @csrf

              <input type="hidden" name="user_id" value="{{ $user->id }}">


          <div class="sendfullreport__popup__form__input">
            <h2>Email</h2>
            @if($user->email)
              <input type="text" name="email" placeholder="Email" id="email" require value="{{$user->email}}">
            @else
              <input type="text" name="email" placeholder="Email" id="email" require>
            @endif
          </div>
          <div class="sendfullreport__popup__form__button" id="generate_otp_email">
            <button disabled id="generate_email_otp_button" onclick="generateOtpEmail()"  >Generate OTP</button>
          </div>
          <div class="sendfullreport__popup__form__input" id="email_otp_field_div" style="display: none;">
            <input type="text" name="email_otp" placeholder="Email OTP" require>
          </div>
          <p style="display: none;">Invalid OTP</p>


          <div class="sendfullreport__popup__form__input">
            <h2>mobile</h2>
            @if($user->mobile)
              <input type="number" name="mobile" placeholder="Mobile" id="mobile" require value="{{$user->mobile}}">
            @else
              <input type="number" name="mobile" placeholder="Mobile" id="mobile" require>
            @endif
          </div>
          <div class="sendfullreport__popup__form__button" id="generate_otp_mobile">
            <button disabled id="generate_mobile_otp_button" onclick="generateOtpBoth('mobile')" >Generate OTP</button>
          </div>
          <div class="sendfullreport__popup__form__input" id="mobile_otp_field_div" style="display: none;">
            <input type="text" name="mobile_otp" placeholder="Mobile OTP" require>
          </div>
          <p style="display: none;">Invalid OTP</p>

          



          <div class="sendfullreport__popup__form__button">
            <!-- <button type="submit" id="conitnue_email_popup_form" disabled>Continue</button> -->
            <button type="submit" id="" disabled>Continue</button>
          </div>



          <div class="sendfullreport__popup__form__maybe">
            <h3 onclick="mayBeLater('sendfullreport')">Maybe later</h3>
          </div>
          <div class="sendfull__report__checkbox">
            <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="useemail" name="useemail" checked disabled>
              <label for="useemail"><span>Use Email in case I forget my password.</span></label>
            </div>
            <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="subscribe" name="subscribe" checked disabled>
              <label for="subscribe"><span>Subscribe me to blogs/articles/newsletters or any other subscriptions from HappiMynd for my updation.</span></label>
            </div>
            <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="coupon" name="coupon" checked disabled>
              <label for="coupon"><span>Send HappiMynd Gift Voucher/Discount Coupon on my mail ID.</span></label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    var email = $('#email').val();
    if(email){
      $('#generate_email_otp_button').removeAttr('disabled')
    }

    var mobile = $('#mobile').val();
    if(mobile){
      $('#generate_mobile_otp_button').removeAttr('disabled')
    }


    $('#email').on('keyup' , function(){
      var email_value = $(this).val();
        if(email_value.length == 0){
          $('#generate_email_otp_button').attr('disabled','disabled');
          $('#email_otp_field_div').val('');
          $('#email_otp_field_div').css('display' , 'none');

        }else{
          $('#generate_email_otp_button').removeAttr('disabled')
        }
    })


    $('#generate_email_otp_button').on('click' , function(){
      $('#email_otp_field_div').css('display' , 'block');
    })

    $('#mobile').on('keyup' , function(){
      var mobile_value = $(this).val();
        if(mobile_value.length == 0){
          $('#generate_mobile_otp_button').attr('disabled','disabled');
        }else{
          $('#generate_mobile_otp_button').removeAttr('disabled')
        }
    })

  })


  // function generateOtpBoth(type){
  //     // alert(type);
  //     url = "generate-otp-both-"+ type;
  //     var email = $('#email').val();
  //       $.ajax({
  //          type:'POST',
  //          url:url,
  //          data:{email:email},
  //          success:function(data){
  //             console.log(data.success);
  //          }
  //       });    
  // }
</script>



<script>

  function generateOtpEmail(){

    alert('ss');

      let email = $("#email").val();
      let _token: "{{ csrf_token() }}";

      url = '{{ route('generateOTPEmail') }}';

      $.ajax({
        url: url,
        type:"POST",
        data:{
            email:email,
            _token: _token
        },
        success:function(response){
          console.log(response);
          // if(response) {
          //   $('.success').text(response.success);
          //   // $("#ajaxform")[0].reset();
          // }
        },
        error: function(error) {
         console.log(error);
        }
       });
  };
</script>






