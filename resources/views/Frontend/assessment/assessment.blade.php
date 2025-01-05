@extends('layouts.app')

@section('title', 'Happimynd | Screening')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">

  @include('Frontend.includes.dashboard.header')
  @include('Frontend.includes.popups.assessment_instruction')
  <div class="assessment">
    <div class="container">
      <div class="assessment__content" id="assessment-content">
        <h1>Screening</h1>
        <div class="assessment__content__progress">
          <div>
            <div class="assessment__content__progress__tooltip-percent">
              <p></p>
            </div>
          </div>
          <div class="assessment__content__progress-bar">
            <div class="assessment__content__progress-bar__fill"></div>
          </div>
          <div>
            <div class="assessment__content__progress__tooltip-text">
              <p></p>
            </div>
          </div>
        </div>
        <div class="assessment__content__questions" id="questions__container">

        </div>
      </div>
    </div>
  </div>
</div>
@section('js')
<script type="text/javascript">
$('#nextButton').on('click', function(){
  if($('#nextButton').text()=="Submit"){
    sessionStorage.setItem('show_screening_complete_congrats_popup', true);
    window.location = "{{ route('user.dashboard') }}";
  }
});
function getQuestions(assessment_id){
    if($('#nextButton').text()=="Submit"){
      sessionStorage.setItem('show_screening_complete_congrats_popup', true);
    }else{
      $.ajax({
        type: 'GET',
        url: "{{ route('user.getQuestions') }}",
        dataType: 'json',
        data: {'assessment_id': assessment_id},
        beforeSend: function () {

        },
        success: function (data) {
          selectedOptionCount = 0;
          console.log(data);
          if(data.current_page === 1) {
            $(".assessment__content__progress").css("display", "none");
            scrollToTop();
          }
          else if(data.current_page === 2) {
            showProgress("block", 20, "1st section ends");
            scrollToTop();
            assessmentTooltipWidth();
          }
          else if(data.current_page === 3) {
            showProgress("block", 40, "2nd section ends");
            scrollToTop();
            assessmentTooltipWidth();
          }
          else if(data.current_page === 4) {
            showProgress("block", 60, "3rd section ends");
            scrollToTop();
            assessmentTooltipWidth();
          }
          else if(data.current_page === 5) {
            showProgress("block", 80, "Just 1 more section to go");
            $('#nextButton').text("Submit");
            if(data.total === data.answered){
              showProgress("block", 100, "You're almost there, Keep Going");
              assessmentTooltipWidth();
            }
            // scoreUrl = '{{ route('calculateAssessmentScore')}}?assessment_id=';
            // console.log('"'+scoreUrl+'"')
            // $('#nextButton').click(function() {
            //   $(window.location).attr('href', scoreUrl+assessment_id);
            // });
            scrollToTop();
            assessmentTooltipWidth();
          }
          else {
            showProgress("block", 100, "You're almost there, Keep Going");
            scrollToTop();
            assessmentTooltipWidth();
          }
          if(data.total == data.answered){
            sessionStorage.setItem('show_screening_complete_congrats_popup', true);
            window.location = "{{ route('user.dashboard') }}";
          }
          if((data.answered+data.perPage) >= data.total){
            $('#nextButton').text('Submit');
          }
          var c=1;
          var question = '<form>';
          $.each(data.data, function(k,v){
            var options = createOptions(v['options'],c)
            var classs = 'assessment__question';
            if(c==1){
              classs+=' active';
            }
            // <h2>`+v['question']+`(Q.No `+v['id']+`)`+`(`+v['category']+`)</h2>
            @if(env('ASSESSMENT_DEBUG'))
              question += `<div class="`+classs+`" id="assessment__question`+(c++)+`">
                  <h2>`+v['question']+`(${v['debugData']})</h2>
                  <div class="assessment__question__options">`+options+`</div></div>`;
            @else
              question += `<div class="`+classs+`" id="assessment__question`+(c++)+`">
                  <h2>`+v['question']+`</h2>
                  <div class="assessment__question__options">`+options+`</div></div>`;
            // console.log(question);
            @endif
          })
          question += `<div class="assessment__question__nextbtn">
              <button type="button" onclick="getQuestions($('#assessment_id').val())" id="nextButton" disabled>
                Next
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4.16671 10L15.8334 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M10 4.16683L15.8334 10.0002L10 15.8335" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div></form>`
          $('#questions__container').html(question)
          if(data.current_page >= 5) {
            $('#nextButton').text("Submit");
            // $('#nextButton').on('click', function(){
            //   window.location="{{ route("calculateAssessmentScore")}}?assessment_id="+assessment_id;
            // });
          }
        },
        error: function (data) {
        },
      });
    }
    }
    function createOptions(options, k) {
      var option = '';
      options.forEach(element => {
        var id = makeid(4);
        // <label for="`+id+`"><span>`+element['option']+`(score:`+element['score']+`)(optionId:`+element['option_id']+`)</span></label>
        @if(env('ASSESSMENT_DEBUG'))
          option += `<div class="question__checkbox">
          <input class="qcheckbox" type="radio" id="`+id+`" name="qcheckbox`+k+`" onclick="scrollToNext('`+id+`', 'assessment__question`+k+`', 'assessment__question`+(k+1)+`',`+element['id']+`);">
          <label for="`+id+`"><span>`+element['option']+`  <b>${element['debugData']}</b></span></label>
        </div>`
      @else
          option += `<div class="question__checkbox">
          <input class="qcheckbox" type="radio" id="`+id+`" name="qcheckbox`+k+`" onclick="scrollToNext('`+id+`', 'assessment__question`+k+`', 'assessment__question`+(k+1)+`',`+element['id']+`);">
          <label for="`+id+`"><span>`+element['option']+`</span></label>
        </div>`;
      @endif
      });
      return option;
    }
    function makeid(l){
      var text = "";
      var char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      for(var i=0; i < l; i++ ){
        text += char_list.charAt(Math.floor(Math.random() * char_list.length));
      }
      return text;
    }
    function showProgress(display, width, text) {
      $(".assessment__content__progress").css("display", display);
      $(".assessment__content__progress-bar__fill").css("width", width+"%");
      $(".assessment__content__progress__tooltip-text p").text(text);
    }
    function scrollToTop() {
      $('html, body').animate({
        scrollTop: $("#assessment-content").offset().top - $(".navbar").outerHeight()
      }, 800);
    }
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
@endsection
