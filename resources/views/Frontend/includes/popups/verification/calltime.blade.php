<div class="modal fade" id="calltime" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        <h1>What’s a good time to call you?</h1>
        <p>Happimynd’s Emotional Wellbeing Expert will call you to walk you through the Screening Summary.</p>
        <form class="calltime__popup__form" method="post" id="calltime_popup_form" action="{{ route('user.updateCalltime') }}">
          @csrf
          <div class="calltime__popup__form__input">
            <h2>Date</h2>
            <input type="text" name="date" id="dateslot" placeholder="-" readonly require>
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="hidden" name="assessment_id" id="calltime_assessment_id">
          </div>
          <div class="calltime__popup__form__input">
            <h2>Time slot</h2>
            <div class="dropdown">
              {{-- <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Dropdown button
              </button> --}}
              <input type="text" name="slot" id="timeslot" placeholder="-" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" readonly require>
              <div class="dropdown-menu calltime__popup__form__input__time" aria-labelledby="dropdownMenuButton">
                <div class="calltime__popup__form__input__time__content">
                  <button type="button" class="time time1" onclick="selectTimeSlot('time1');" id="10AM-12PM">10AM - 12PM</button>
                  <button type="button" class="time time2" onclick="selectTimeSlot('time2');" id="12PM-2PM">12PM - 2PM</button>
                  <button type="button" class="time time3" onclick="selectTimeSlot('time3');" id="2PM-4PM">2PM - 4PM</button>
                  <button type="button" class="time time4" onclick="selectTimeSlot('time4');" id="4PM-6PM">4PM - 6PM</button>
                  <button type="button" class="time time5" onclick="selectTimeSlot('time5');" id="6PM-8PM">6PM - 8PM</button>
                </div>
              </div>
            </div>
          </div>
          <div class="calltime__popup__form__button d-flex justify-content-between col-md-12">
            <div>
              <label for="phone">
                Phone Call
                <input type="radio" name="call_option" value=1 id="phone">
              </label>
            </div>
            <div>
              <label for="email">
                Zoom Call
                <input type="radio" name="call_option" value=2 id="email">
              </label>
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
