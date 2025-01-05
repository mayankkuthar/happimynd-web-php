<div class="modal fade" id="raisequery" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog raisequery__popup">
    <div class="modal-content">
      <div class="raisequery__popup__content">
        <h1>Raise a Query</h1>
        <form class="raisequery__popup__content__form" method="POST" action="{{ route('user.raiseQuery') }}" id="raised_query_form">
          <div >
            @csrf
            <h2>Select a catagory</h2>
            <select name="category" id="category">
              <option value="">Select a category</option>
              <option value="screening">Screening</option>
              <option value="payment">Payment</option>
              <option value="service">Service</option>
              <option value="others">Others</option>
            </select>
            <h2>Describe your query</h2>
            <textarea placeholder="Describe" name="query" id="query_message"></textarea>
            <input type="hidden" name="user_id" value="{{ auth('user')->user()->id }}">
          </div>
          <div class="raisequery__popup__content__form__btn">
            <button class="raisequery__popup__content__form__btn__submit" type="submit" id="btn_submit_raised_query" disabled>Submit</button>
            <button class="raisequery__popup__content__form__btn__cancel" type="button" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
