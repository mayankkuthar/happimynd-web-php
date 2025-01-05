<div class="modal fade" id="happiAppPopup" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog commingsoon__popup">
    <div class="modal-content">
      <diV class="commingsoon__popup__content">
        <h1 id="happiAppPopup_message">Are you Sure !!!</h1>
        <div class="happiApp__popup_content_form_btn" id="happiAppPopup_btns">
          <form action="{{ route('user.checkForThriveCode') }}" method="post" id="happiAppPopup_form">
            @csrf
            <input type="hidden" name="avail" value="1">
            <div class="d-flex align-items-center">
              <button class="happiApp__popup_content_form_btn__submit" type="submit" id="happiAppPopup_yes_btn">Yes</button>
              <button class="happiApp__popup_content_form_btn__cancel ml-2" type="button" data-dismiss="modal">No</button>
            </div>
          </form>
          </div>
      </diV>
    </div>
  </div>
</div>
