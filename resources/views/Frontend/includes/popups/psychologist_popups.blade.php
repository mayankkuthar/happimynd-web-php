
{{-- Email verification modal starts --}}
<div class="modal fade" id="psychologist_email" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered sendfullreport__popup">
    <div class="modal-content">
      <div class="sendfullreport__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="sendfullreport__popup__content">
        <h1 id="send_full_report_modal_h1">Email Required</h1>
        <p id=send_full_report_modal_p>We will send you the further instructions on your Email in 24 hours.</p>
        <form class="sendfullreport__popup__form" method="post" action="{{route('user.updateEmail')}}" id="psychologist_update_email_popup_form">
          @csrf
          <div class="sendfullreport__popup__form__input">
            <h2>Email</h2>
            <input type="email" name="email" placeholder="Email" require value="{{auth('user')->user()->email}}">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
          </div>
          <div class="sendfullreport__popup__form__button">
            <button type="submit" id="psychologist_conitnue_email_popup_form" @if(auth('user')->user()->email == '') disabled @endif>Continue</button>
          </div>
          <div class="sendfull__report__checkbox">
            <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="use-email" name="useEmail" checked>
              <label for="use-email"><span>Use Email in case I forget my password.</span></label>
            </div>
            <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="use-email-for-summary" name="useEmailForSummary" checked>
              <label for="use-email-for-summary"><span>Use Email to send copy of summary</span></label>
            </div>
            <div class="sendfull__report__checkbox__input">
              <input class="qcheckbox" type="checkbox" id="subscribe_newsletter" name="subscribe" checked>
              <label for="subscribe_newsletter"><span>Subscribe me to blogs/articles/newsletters or any other subscriptions from HappiMynd for my updation.</span></label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- Email verification modal ends --}}

{{-- Send code via email button only popup starts --}}
<div class="modal fade" id="psychologist_send_code_via_email" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog verifyaccount__popup">
    <div class="modal-content">
      <div class="verifyaccount__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="verifyaccount__popup__content">
        <h1>Verify Account</h1>
        <button type="button" onclick="sendVerificationCodeOnMail();">Send code via Email</button>
      </div>
    </div>
  </div>
</div>
{{-- send code via email button only popup ends --}}

{{-- confirm otp popup starts --}}
<div class="modal fade" id="psychologist_confirmcode" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog confirmcode__popup">
    <div class="modal-content">
      <div class="confirmcode__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="confirmcode__popup__content">
        <h1>Confirmation code</h1>
        <p>Please check your Inbox for 6-digit confimation code. </p>
        <form class="confirmcode__popup__form" method="post" id="psychologist_confirmcode_form" action="{{ route('verifyOtpByCode',['type'=>'email']) }}">
          @csrf
          <div class="confirmcode__popup__form__input">
            <h2>Confirmation code</h2>
            <input type="text" name="otp_code" placeholder="6-digit code" require>
            <input type="hidden" name="user_id" value="{{ auth('user')->user()->id }}">
          </div>
          <div class="confirmcode__popup__form__button">
            <button class="confirmcode__popup__form__button__continue" type="submit" disabled id="psychologist_otp_verify_button">Verify</button>
            <a href="javascript:void(0)" onclick="sendVerificationCodeOnMail();">Resend code</a>
            <span>0.54</span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- confirm otp popup ends --}}

{{-- appointment book starts --}}
<div class="modal fade" id="psychologist_calltime" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog calltime__popup">
    <div class="modal-content">
      <div class="calltime__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

      </div>
      <div class="calltime__popup__content">
        <h1>Your preferred schedule</h1>
        <p>Share your preferred time to talk with your selected Expert.</p>
        <form class="calltime__popup__form" method="post" id="psychologist_appointment_form" action="{{ route('user.updatePsychologistAppointment.post') }}">
          @csrf
          <div class="calltime__popup__form__input">
            <h2>Date</h2>
            <input type="text" name="date" id="psychologist-dateslot" placeholder="-" readonly require>
            <input type="hidden" name="user_id" value="{{ $user->id }}">
          </div>
          <div class="calltime__popup__form__input">
            <h2>Time slot</h2>
            <div class="dropdown">
              {{-- <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Dropdown button
              </button> --}}
              <input type="text" name="slot" id="psychologist-timeslot" placeholder="-" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" readonly require>
              <div class="dropdown-menu calltime__popup__form__input__time" aria-labelledby="dropdownMenuButton">
                <div class="calltime__popup__form__input__time__content d-flex flex-wrap" id="psychologist-time-slot-list">

                  <button type="button" class="time" onclick="selectSlot('12:00 AM - 1:00 AM');" id="" data-slot="12:00 AM - 1:00 AM">12:00 AM - 1:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('12:30 AM - 1:30 AM');" id="" data-slot="12:30 AM - 1:30 AM">12:30 AM - 1:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('1:00 AM - 2:00 AM');" id="" data-slot="1:00 AM - 2:00 AM">1:00 AM - 2:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('1:30 AM - 2:30 AM');" id="" data-slot="1:30 AM - 2:30 AM">1:30 AM - 2:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('2:00 AM - 3:00 AM');" id="" data-slot="2:00 AM - 3:00 AM">2:00 AM - 3:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('2:30 AM - 3:30 AM');" id="" data-slot="2:30 AM - 3:30 AM">2:30 AM - 3:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('3:00 AM - 4:00 AM');" id="" data-slot="3:00 AM - 4:00 AM">3:00 AM - 4:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('3:30 AM - 4:30 AM');" id="" data-slot="3:30 AM - 4:30 AM">3:30 AM - 4:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('4:00 AM - 5:00 AM');" id="" data-slot="4:00 AM - 5:00 AM">4:00 AM - 5:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('4:30 AM - 5:30 AM');" id="" data-slot="4:30 AM - 5:30 AM">4:30 AM - 5:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('5:00 AM - 6:00 AM');" id="" data-slot="5:00 AM - 6:00 AM">5:00 AM - 6:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('5:30 AM - 6:30 AM');" id="" data-slot="5:30 AM - 6:30 AM">5:30 AM - 6:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('6:00 AM - 7:00 AM');" id="" data-slot="6:00 AM - 7:00 AM">6:00 AM - 7:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('6:30 AM - 7:30 AM');" id="" data-slot="6:30 AM - 7:30 AM">6:30 AM - 7:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('7:00 AM - 8:00 AM');" id="" data-slot="7:00 AM - 8:00 AM">7:00 AM - 8:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('7:30 AM - 8:30 AM');" id="" data-slot="7:30 AM - 8:30 AM">7:30 AM - 8:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('8:00 AM - 9:00 AM');" id="" data-slot="8:00 AM - 9:00 AM">8:00 AM - 9:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('8:30 AM - 9:30 AM');" id="" data-slot="8:30 AM - 9:30 AM">8:30 AM - 9:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('9:00 AM - 10:00 AM');" id="" data-slot="9:00 AM - 10:00 AM">9:00 AM - 10:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('9:30 AM - 10:30 AM');" id="" data-slot="9:30 AM - 10:30 AM">9:30 AM - 10:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('10:00 AM - 11:00 AM');" id="" data-slot="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('10:30 AM - 11:30 AM');" id="" data-slot="10:30 AM - 11:30 AM">10:30 AM - 11:30 AM</button>
                  <button type="button" class="time" onclick="selectSlot('11:00 AM - 12:00 PM');" id="" data-slot="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('11:30 AM - 12:30 PM');" id="" data-slot="11:30 AM - 12:30 PM">11:30 AM - 12:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('12:00 PM - 1:00 PM');" id="" data-slot="12:00 PM - 1:00 PM">12:00 PM - 1:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('12:30 PM - 1:30 PM');" id="" data-slot="12:30 PM - 1:30 PM">12:30 PM - 1:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('1:00 PM - 2:00 PM');" id="" data-slot="1:00 PM - 2:00 PM">1:00 PM - 2:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('1:30 PM - 2:30 PM');" id="" data-slot="1:30 PM - 2:30 PM">1:30 PM - 2:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('2:00 PM - 3:00 PM');" id="" data-slot="2:00 PM - 3:00 PM">2:00 PM - 3:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('2:30 PM - 3:30 PM');" id="" data-slot="2:30 PM - 3:30 PM">2:30 PM - 3:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('3:00 PM - 4:00 PM');" id="" data-slot="3:00 PM - 4:00 PM">3:00 PM - 4:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('3:30 PM - 4:30 PM');" id="" data-slot="3:30 PM - 4:30 PM">3:30 PM - 4:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('4:00 PM - 5:00 PM');" id="" data-slot="4:00 PM - 5:00 PM">4:00 PM - 5:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('4:30 PM - 5:30 PM');" id="" data-slot="4:30 PM - 5:30 PM">4:30 PM - 5:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('5:00 PM - 6:00 PM');" id="" data-slot="5:00 PM - 6:00 PM">5:00 PM - 6:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('5:30 PM - 6:30 PM');" id="" data-slot="5:30 PM - 6:30 PM">5:30 PM - 6:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('6:00 PM - 7:00 PM');" id="" data-slot="6:00 PM - 7:00 PM">6:00 PM - 7:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('6:30 PM - 7:30 PM');" id="" data-slot="6:30 PM - 7:30 PM">6:30 PM - 7:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('7:00 PM - 8:00 PM');" id="" data-slot="7:00 PM - 8:00 PM">7:00 PM - 8:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('7:30 PM - 8:30 PM');" id="" data-slot="7:30 PM - 8:30 PM">7:30 PM - 8:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('8:00 PM - 9:00 PM');" id="" data-slot="8:00 PM - 9:00 PM">8:00 PM - 9:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('8:30 PM - 9:30 PM');" id="" data-slot="8:30 PM - 9:30 PM">8:30 PM - 9:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('9:00 PM - 10:00 PM');" id="" data-slot="9:00 PM - 10:00 PM">9:00 PM - 10:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('9:30 PM - 10:30 PM');" id="" data-slot="9:30 PM - 10:30 PM">9:30 PM - 10:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('10:00 PM - 11:00 PM');" id="" data-slot="10:00 PM - 11:00 PM">10:00 PM - 11:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('10:30 PM - 11:30 PM');" id="" data-slot="10:30 PM - 11:30 PM">10:30 PM - 11:30 PM</button>
                  <button type="button" class="time" onclick="selectSlot('11:00 PM - 12:00 AM');" id="" data-slot="11:00 PM - 12:00 AM">11:00 PM - 12:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('11:30 PM - 12:30 AM');" id="" data-slot="11:30 PM - 12:30 AM">11:30 PM - 12:30 AM</button>


                  <!-- <button type="button" class="time" onclick="selectSlot('12:00 AM - 1:00 AM');" id="" data-slot="12:00 AM - 1:00 AM">12:00 AM - 1:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('1:00 AM - 2:00 AM');" id="" data-slot="1:00 AM - 2:00 AM">1:00 AM - 2:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('2:00 AM - 3:00 AM');" id="" data-slot="2:00 AM - 3:00 AM">2:00 AM - 3:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('3:00 AM - 4:00 AM');" id="" data-slot="3:00 AM - 4:00 AM">3:00 AM - 4:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('4:00 AM - 5:00 AM');" id="" data-slot="4:00 AM - 5:00 AM">4:00 AM - 5:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('5:00 AM - 6:00 AM');" id="" data-slot="5:00 AM - 6:00 AM">5:00 AM - 6:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('6:00 AM - 7:00 AM');" id="" data-slot="6:00 AM - 7:00 AM">6:00 AM - 7:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('7:00 AM - 8:00 AM');" id="" data-slot="7:00 AM - 8:00 AM">7:00 AM - 8:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('8:00 AM - 9:00 AM');" id="" data-slot="8:00 AM - 9:00 AM">8:00 AM - 9:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('9:00 AM - 10:00 AM');" id="" data-slot="9:00 AM - 10:00 AM">9:00 AM - 10:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('10:00 AM - 11:00 AM');" id="" data-slot="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</button>
                  <button type="button" class="time" onclick="selectSlot('11:00 AM - 12:00 PM');" id="" data-slot="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('12:00 PM - 1:00 PM');" id="" data-slot="12:00 PM - 1:00 PM">12:00 PM - 1:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('1:00 PM - 2:00 PM');" id="" data-slot="1:00 PM - 2:00 PM">1:00 PM - 2:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('2:00 PM - 3:00 PM');" id="" data-slot="2:00 PM - 3:00 PM">2:00 PM - 3:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('3:00 PM - 4:00 PM');" id="" data-slot="3:00 PM - 4:00 PM">3:00 PM - 4:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('4:00 PM - 5:00 PM');" id="" data-slot="4:00 PM - 5:00 PM">4:00 PM - 5:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('5:00 PM - 6:00 PM');" id="" data-slot="5:00 PM - 6:00 PM">5:00 PM - 6:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('6:00 PM - 7:00 PM');" id="" data-slot="6:00 PM - 7:00 PM">6:00 PM - 7:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('7:00 PM - 8:00 PM');" id="" data-slot="7:00 PM - 8:00 PM">7:00 PM - 8:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('8:00 PM - 9:00 PM');" id="" data-slot="8:00 PM - 9:00 PM">8:00 PM - 9:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('9:00 PM - 10:00 PM');" id="" data-slot="9:00 PM - 10:00 PM">9:00 PM - 10:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('10:00 PM - 11:00 PM');" id="" data-slot="10:00 PM - 11:00 PM">10:00 PM - 11:00 PM</button>
                  <button type="button" class="time" onclick="selectSlot('11:00 PM - 12:00 AM');" id="" data-slot="11:00 PM - 12:00 AM">11:00 PM - 12:00 AM</button> -->


                </div>
              </div>
            </div>
          </div>
          <div class="calltime__popup__form__button">
            <button type="submit" id="save_calltime_popup_form">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- appointment book ends --}}

{{-- psychologist booking complete popups starts --}}
<div class="modal fade" id="psychologist_appointment_thanks" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog commingsoon__popup">
    <div class="modal-content">
      <diV class="commingsoon__popup__content">
        <h1>Thanks!</h1>
        <p>HappiMynd team will connect with you soon.</p>
        <button data-dismiss="modal" type="button">Close</button>
      </diV>
    </div>
  </div>
</div>
{{-- psychologist booking complete popups ends --}}
<script>

  function startPsychologistAppointmentBooking()
  {
    if(user.isEmailVerified){
      @if(!auth()->user()->isOrganizationUser())
      psychologistAppointment();
      $('#psychologist_calltime').modal('show');
      @endif
    }
    else{
      $('#psychologist_email').modal('show');
    }
  }
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }
  $(document).ready(function(){

    $('#psychologist_update_email_popup_form input[name="email"]').on('input', function(){
      if(isEmail($(this).val())){
        $('#psychologist_conitnue_email_popup_form').removeAttr('disabled');
      }
      else{
        $('#psychologist_conitnue_email_popup_form').attr('disabled', true);
      }
    })
  })

  $('#psychologist_update_email_popup_form').submit(function(e){
    e.preventDefault();
    var form  = e.target;
    $.ajax({
      url : $(form).attr('action'),
      method: $(form).attr('method'),
      data: $(form).serialize(),
      success: function(data){
        if(data.error == false){
          $('#psychologist_email').modal('hide');
          $('#psychologist_send_code_via_email').modal('show');
        }
      }
    })
  })

  //get dates and slot of psychologist for which user has paid and instantiate datepicker
  function psychologistAppointment() {

    $.ajax({
      type: "GET",
      url: $('base').attr('href') + '/psychologist-available-dates',
      success: function (data) {
        console.log(data);
        slots = data.message
        if (data.error == false) {
          available_dates = Object.keys(data.message);
          $('#psychologist-dateslot').datepicker({
            minDate: 3,
            maxDate: "+1M +10D",
            dateFormat: 'dd MM, yy',
            constrainInput: true,
            beforeShowDay: function(date){
              var date = jQuery.datepicker.formatDate('yy-mm-dd', date);
              return [ available_dates.indexOf(date) != -1 ]
            },
            onSelect: function(date ,instance){
              selected_date = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker('getDate'));
              $('#psychologist-time-slot-list button').each(function(ind, button){
                $(button).attr('disabled', true);
                $(button).hide();
                $(button).removeClass('active');
              });
              slots[selected_date].forEach(function(el, indx){
                $('#psychologist-time-slot-list button').each(function(ind, button){
                  if($(button).data('slot') == el){
                    $(button).removeAttr('disabled');
                    $(button).show();
                    $(button).addClass('active');
                  }
                })
              });
            }
          });
        }
      }
    });
  }

  //onlick for time slot
  function selectSlot(slot){
    $('button.time').removeClass('active');
    $("#psychologist-timeslot").val(slot);
    $('button[data-slot="'+slot+'"]').addClass('active');
  }

  //ajax call to save date and time slots for psychologist appointment
  $('#psychologist_appointment_form').submit(function(e){
    e.preventDefault();
    var form = e.target
    $.ajax({
      url: $(form).attr('action'),
      method: $(form).attr('method'),
      data: $(form).serialize(),
      success: function(data){
        if(data.error == false){
          $('#psychologist_calltime').modal('hide');
          $('#psychologist_appointment_thanks').modal('show');
        }
      }
    })
  })

  //send otp on users mail
  function sendVerificationCodeOnMail(){

    var url = $('base').attr('href')+"/generate-otp-email";

    if(url!=""){
      $.ajax({
        type: "get",
        url: url,
        success: function (data) {
          console.log(data);
        }
      });
      $('#psychologist_send_code_via_email').modal('hide');
      $('#psychologist_confirmcode_form').trigger('reset');
      $('#psychologist_confirmcode').modal('show');
    }
  }

  //verify otp entered by user
  $('#psychologist_confirmcode_form').submit(function(e){
    e.preventDefault();
    var form = e.target;
    $.ajax({
      url: $(form).attr('action'),
      method: $(form).attr('method'),
      data: {otp: $('input[name="otp_code"]').val()},
      success: function(data){
        console.log(data);
        if(data.error == false && data.message == 'Successfully, Email OTP is verified.!'){
          $('#psychologist_confirmcode').modal('hide');
          @if(auth()->user()->isOrganizationUser())
          $('#psychologist_appointment_thanks').modal('show');
          @else
          psychologistAppointment();
          $('#psychologist_calltime').modal('show');
          @endif
        }
      }
    })
  })

  //enabled button if otp entered is 6 digits
  $("[name='otp_code']").keyup(()=>{
    if($("[name='otp_code']").val().length==6){
      $('#psychologist_otp_verify_button').prop('disabled',false);
    }else{
      $('#psychologist_otp_verify_button').prop('disabled',true);
    }
  });
</script>
