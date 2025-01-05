<?php 
    $user = Auth::user(); 
    $isUserFromOrg =  App\Models\UserToken::where('user_id' , $user->id)->first();
?>

<div></div>
<!--
@if($isUserFromOrg)
  <div style='display : none !important' class="modal fade" id="sendfullreport" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true"  >
@else
<div style='display : none !important' class="modal fade" id="sendfullreport" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" >
@endif

  <div style='display : none' class="modal-dialog sendfullreport__popup">
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
        <h1 id="send_full_report_modal_h1">Send my HappiLIFE Awareness Summary</h1>
        <p id=send_full_report_modal_p>Summary will be shared on your mail ID</p>
        <form class="sendfullreport__popup__form" method="post" action="{{route('user.updateEmail')}}" id="update_email_popup_form">
          @csrf
          <div class="sendfullreport__popup__form__input">
            <h2>Email</h2>
            @if($user->email)
              <input type="email" name="email" placeholder="Email" require value="{{$user->email}}">
            @else
              <input type="email" name="email" placeholder="Email" require>
            @endif
              <input type="hidden" name="user_id" value="{{ $user->id }}">
          </div>
          <div class="sendfullreport__popup__form__button">
            <button type="submit" id="conitnue_email_popup_form" disabled>Continue</button>
          </div>


            <?php 
                $user = Auth::user(); 
                $isUserFromOrg =  App\Models\UserToken::where('user_id' , $user->id)->first();
                // dd($isUserFromOrg); die();
            ?>

            @if($isUserFromOrg)
              <div class="sendfullreport__popup__form__maybe">
                <h3 onclick="mayBeLater('sendfullreport')">Maybe later</h3>
              </div>
            @endif
          
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
            #commented
             <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="coupon" name="coupon" checked disabled>
              <label for="coupon"><span>Report will be provided for free if you verify both email and phone number.</span></label>
            </div> 
            #commented
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
-->