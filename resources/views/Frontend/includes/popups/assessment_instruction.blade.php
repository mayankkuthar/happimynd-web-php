
<style type="text/css">
  .assestment-modal-btn-list ul {

    text-align: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;

  }
  .assestment-modal-btn-list ul li {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}
</style>

<div class="modal fade" id="assessment_instruction" tabindex="-1" data-show="true" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog assessment__popup">
    <div class="modal-content">
      <diV class="assessment__popup__content pt-3 pb-5">


       <!-- <div class="assestment-modal-btn-list">
        <ul>
          <li>
              <div class="assessment__popup__content__button">
                <a href="{{url('subscribedservices')}}">
                  <button type="button">Continue To Payment</button>
                </a>
            </div>
          </li>
          <li>
              <div class="assessment__popup__content__button">
                <a href="{{url('services')}}">
                  <button type="button">Explore Other Services</button>
              </a>
            </div>
          </li>
          <li>
              #commented 
              <div class="assessment__popup__content__button">
                    <button type="button" data-dismiss="modal" onclick="start_assessment()">Start HappiLIFE Tool
                </button>
              </div>
              #commented
          </li>
        </ul>
       </div> -->



        <h1 style="font-size: 20px;">Instructions:</h1>
        <p style="font-size: 14px;" class="assessment__popup__content__description pb-2">To get maximum benefit of HappiLIFE Awareness Tool please follow below</p>
        <div class="assessment__popup__content__instructions">
          <div class="d-flex align-items-start">
            <div class="assessment__popup__content__instructions__number">
              <span style="font-size: 16px;">1</span>
            </div>
            <div class="assessment__popup__content__instructions__text">
              <h2 style="font-size: 16px;">Give only answers that are true for you.</h2>
              <p style="font-size: 14px;">It is best to say what you really think.</p>
            </div>
          </div>
          <div class="d-flex align-items-start">
            <div class="assessment__popup__content__instructions__number">
              <span style="font-size: 16px;">2</span>
            </div>
            <div class="assessment__popup__content__instructions__text">
              <h2 style="font-size: 16px;">Try to go fairly fast.</h2>
              <p style="font-size: 14px;">It’s best to give the first answer that comes to you and not spend too much time on any one question.</p>
            </div>
          </div>
          <div class="d-flex align-items-start">
            <div class="assessment__popup__content__instructions__number">
              <span style="font-size: 16px;">3</span>
            </div>
            <div class="assessment__popup__content__instructions__text">
              <h2 style="font-size: 16px;">Don’t skip any item.</h2>
              <p style="font-size: 14px;">Answer every item one way or the other. </p>
            </div>
          </div>
          <div class="d-flex align-items-start">
            <div class="assessment__popup__content__instructions__number">
              <span style="font-size: 16px;">4</span>
            </div>
            <div class="assessment__popup__content__instructions__text">
              <h2 style="font-size: 16px;">Respond to all questions in one sitting.</h2>
              <p style="font-size: 14px;">If required resume where you left within 15 mins to avoid losing punched answers.</p>
            </div>
          </div>
        </div>
        <p style="font-size: 14px;" class="assessment__popup__content__description1 pb-0">These HappiMynd Awareness Tool statements are inspired by ICD-10(WHO) & DSM-5® guidelines.</p>
        



        <div class="assessment__popup__content__button">
                  <button type="button" data-dismiss="modal" onclick="start_assessment()">Start HappiLIFE Tool
              </button>
            </div>


      </div>
    </div>
  </div>
</div>
<input type="hidden" value="" id="assessment_id">
<script type="text/javascript">
  function start_assessment() {
    $.ajax({
      type: 'POST',
      url: "{{ route('user.startAssessment') }}",
      data: {user_id:"{{ auth('user')->user()->id }}", _token: "{{ csrf_token() }}"},
      dataType: 'json',
      async: false,
      beforeSend: function () {

      },
      success: function (data) {
        $('#assessment_id').val(data.message.assessment_id);
        assessment_id = data.message.assessment_id;
        getQuestions(data.message.assessment_id);
      },
      error: function (data) {
        console.log('error')
      },
    });
  }
</script>

