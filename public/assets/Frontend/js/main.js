$(document).ajaxError(function (data, textStatus, jqXHR) {
  if(textStatus.status == 500)
    showToast('Opps, Something Went wrong!');
});
//common ajax call method and event definition starts
function showLoader(elementId) {
  var submitButton = $('#' + elementId + ' button[type="submit"]');
  existingText = submitButton.text();
  submitButton.attr('disabled', true)
  submitButton.html('<span id="buttonText" style="display:none;">' + existingText + '</span><div class="btn__loader1 loader"></div>');
}
function disableLoader(elementId) {
  var buttonText = $('#' + elementId + ' #buttonText').text();
  $('#' + elementId + ' button[type="submit"]').attr('disabled', false);
  $('#' + elementId + ' button[type="submit"]').text(buttonText);
}
function populateErrors(data) {
  if (data.status === 422 || data.status === 401) {
    var errors = $.parseJSON(data.responseText);
    $.each(errors, function (key, value) {
      if ($.isPlainObject(value)) {
        $.each(value, function (key, value) {
          $('[name="' + key + '"]').after('<span class="error-message" id="' + key + '_error"></div>');
          $('[name="' + key + '"]').addClass('error');
          $('#' + key + '_error').text(value);
        });
      }
    });
  }
}
var defaultFunction = function (data) { console.log('default') }
function removeErrorMsgClassOnTypeEvent(formId) {
  $("#" + formId + " :input").on('change keyup', function () {
    if ($(this).hasClass('error')) {
      $(this).removeClass('error');
      $('#' + $(this).attr('name') + '_error').text('');
    }
  })
}
function formSubmitAjaxEvent(formId, beforeSendCallback = defaultFunction, successCallback = defaultFunction, errorCallback = defaultFunction, isAsynchronous = false) {
  $("#" + formId).submit(function (e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    $.ajax({
      type: $('#' + formId).attr('method'),
      url: $('#' + formId).attr('action'),
      data: form.serialize(),
      dataType: 'json',
      async: isAsynchronous,
      beforeSend: function () {
        $('.error-message').remove(); //remove errors before ajax call
        $('.error').removeClass('error');
        showLoader(formId);
        beforeSendCallback();
      },
      success: function (data) {
        disableLoader(formId)
        successCallback(data);
      },
      error: function (data) {
        populateErrors(data);
        errorCallback;
        disableLoader(formId);
      },
    });
  });
  removeErrorMsgClassOnTypeEvent(formId);
}
//common ajax call method and event definition ends

//signup 1 process verification of happimynd code starts
var signup1SuccessCallback = function (data) {
  if (data.message) {
    $('[name="happimyndCode"]').val($('[name="happimyndCode"]').val())
    $('[name="organization_id"]').val($('[name="organization_id"]').val())
    $(".signup_form-fillsponser").hide();
    $(".signup_form-create-profile").show();
  }
}
formSubmitAjaxEvent('SponserSelectForm', defaultFunction, signup1SuccessCallback, defaultFunction);
//signup 1 process verification of happimynd code ends

//create profile form starts
var createProfileSuccessCallback = function (data) {

  if (!data.error) {
    $('#successDiv').html(data.message.status)
    $('#successDiv').addClass('alert-success')
    setTimeout(()=>{
      $('#successDiv').fadeOut(900)
      // $('#successDiv').html('')
      $('#successDiv').removeClass('alert-success')
    },1000)
    window.location = data.message.route
  }
}
formSubmitAjaxEvent('signupForm', defaultFunction, createProfileSuccessCallback, defaultFunction);
//create profile form ends

/// ===============================================================  ///

// Show Right MenuBar on mobile view
function showMenuBar() {
  if ($(".header__navbar__menu--btn").hasClass("open")) {
    $(".header__navbar__menu--btn").removeClass("open");
    $(".landingpage__navigation-menu").removeClass("showmenubar");
    $(".menubar-overlay").removeClass("show-overlay");
  }
  else {
    $(".header__navbar__menu--btn").addClass("open");
    $(".landingpage__navigation-menu").addClass("showmenubar");
    $(".menubar-overlay").addClass("show-overlay");
  }
}

/// ===============================================================  ///

// Show coming soon popup
function showCommingSoonPop() {
  $("#commingsoon").modal('show');
}

/// ===============================================================  ///

// Show raise a query popup
function showRaiseQueryPop() {
  $("#raisequery").modal('show');
}

function showForgetPassPop() {
  $("#usercheck").modal('show');
}



function showSendOtpPop() {
    $(".modal").modal('hide');
    $("#sendotp").modal('show');
}

function showNewPasswordPop() {
    $(".modal").modal('hide');
    $("#newpassword").modal('show');
}

/// ===============================================================  ///

$('#forget__otp_code_email').submit(function( event ){
    var formData = $('#forget__otp_code').serializeArray();
    var username = $('#entered_username_forgetpass').val();
    var form = $('#forget__otp_code_email');
    var formId = 'forget__otp_code_email';
    event.preventDefault();

    $.ajax({
        type: $('#' + formId).attr('method'),
        url: $('#' + formId).attr('action'),
        data: form.serialize(),
        dataType: 'json',
        success: function (data) {
            if (data.message == 'Successfully, Email OTP is verified.!') {
                showNewPasswordPop()
            }
            else if (data.message == 'Invalid Email OTP/OTP has Expired!' || data.message == 'Something went wrong!' || data.message == 'Successfully, Email OTP is already verified.!') {
                $('#otp__email_forget').addClass('error');
                $('#error-message-forgetpass-otp-email').css('display','block');
                $('#error-message-forgetpass-otp-email').text(data.message);
            }
        },
        error: function (data) {
            console.log(data);

        },
    });

});

$('#forget__password_reset_form').submit(function(event){

    var username = $('#entered_username_forgetpass').val();
    $('#username__forget_password_reset').val(username);

    var password1 = $('#password1').val();
    var password2 = $('#password2').val();
    if(password1 != password2){
        event.preventDefault();
        $('#error-password1').css('display','block');
        $('#error-password2').css('display','block');
        $('#error-password1').text('Password and Confirm password do not match!');
        $('#error-password2').text('Password and Confirm password do not match!');
        $('#password1').addClass('error');
        $('#password2').addClass('error');

    }

});


$('#forget__otp_code_mobile').submit(function( event ){
    var formData = $('#forget__otp_code').serializeArray();
    var username = $('#entered_username_forgetpass').val();
    var form = $('#forget__otp_code_mobile');
    var formId = 'forget__otp_code_mobile';
    event.preventDefault();

    $.ajax({
        type: $('#' + formId).attr('method'),
        url: $('#' + formId).attr('action'),
        data: form.serialize(),
        dataType: 'json',
        success: function (data) {
            console.log(data);
            showNewPasswordPop()
        },
        error: function (xhr, status, error) {
            var err =JSON.parse(xhr.responseText);
            $('#otp__mobile_forget').addClass('error');
            $('#error-message-forgetpass-otp-mobile').css('display','block');
            $('#error-message-forgetpass-otp-mobile').text(err.message);
        },

    });

});

// removing error class when user is typing
$('#forget__pass__username').keyup(function(){

  $('#forget__pass__username').removeClass('error');
  $('#error-message-forgetpass').css('display','none');
  $('#error-message-forgetpass').text('');

})

// Storing username for forget pass for forther use
$( "#forget__pass" ).submit(function( event ) {
    var username = $('#forget__pass__username').val();
    if (username.length == 0) {
        event.preventDefault();
        $('#forget__pass__username').addClass('error');
        $('#error-message-forgetpass').css('display','block');
        $('#error-message-forgetpass').text('The username field is required.');
    }
    else {
        $('#entered_username_forgetpass').val(username);
        $('#username__email_forget').val(username);
        $('#username__mobile_forget').val(username);
        var formId = 'forget__pass';
        var form = $('#forget__pass');
        event.preventDefault();
        $.ajax({
            type: $('#' + formId).attr('method'),
            url: $('#' + formId).attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {
                if(data.flag == true){
                    if (data.email_permission == 1) {
                        $('#email__permission').prop("disabled", false);
                        $('#entered_username_forgetpass').val(data.username);
                        $('#username__email_forget').val(data.username);
                        $('#username__mobile_forget').val(data.username);

                    }
                    else {
                        $('#email__permission').prop("disabled", true);;
                    }

                    if (data.mobile_permission == 1) {
                        $('#mobile__permission').prop("disabled", false);;
                        $('#entered_username_forgetpass').val(data.username);
                        $('#username__email_forget').val(data.username);
                        $('#username__mobile_forget').val(data.username);
                    }
                    else {
                        $('#mobile__permission').prop("disabled", true);;
                    }
                    showSendOtpPop()
                }
                else{
                    $('#forget__pass__username').addClass('error');
                    $('#error-message-forgetpass').css('display','block');
                    $('#error-message-forgetpass').text(data.status);
                }
            }
        });
    }
});

/// ===============================================================  ///

$(window).on("load resize", function () {
  if ($(window).width() <= 576) {
    // $(".raisequery__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".alreadypaid__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".assessment__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".verifyaccount__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".sendfullreport__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".reportreading__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".confirmcode__popup").css("margin-top", $(".dashboard__header").outerHeight());
    // $(".calltime__popup").css("margin-top", $(".dashboard__header").outerHeight());
    $(".dashboard__notification__options__overflow").css("height", $(window).height() - $(".dashboard__header").outerHeight() - $('.dashboard__notification__content').outerHeight());
  }
  else {
    // $(".raisequery__popup").css("margin-top", '15%');
    // $(".alreadypaid__popup").css("margin-top", '15%');
    // $(".assessment__popup").css("margin-top", '');
    // $(".verifyaccount__popup").css("margin-top", '');
    // $(".sendfullreport__popup").css("margin-top", '');
    // $(".reportreading__popup").css("margin-top", '');
    // $(".confirmcode__popup").css("margin-top", '');
    // $(".calltime__popup").css("margin-top", '');
    $(".dashboard__notification__options__overflow").css("height", "450px");
  }
  assessmentTooltipWidth();
  ourTeamHover();
  $(".assessment__question__nextbtn").css("padding-bottom", $(window).height() / 2 - 48);
});

$(window).on("load", function () {
  $('#assessment_instruction').modal('show');
});

/// ===============================================================  ///

// Assessment page tooltip progress bar width, left, display css change
function assessmentTooltipWidth() {
  var width = $('.assessment__content__progress-bar__fill').width();
  var parentWidth = $('.assessment__content__progress-bar__fill').offsetParent().width();
  var percent = Math.round(100 * width / parentWidth);
  if (percent > 80) {
    $(".assessment__content__progress__tooltip-text").addClass("final");
    $(".assessment__content__progress__tooltip-text").css("right", +100 - percent + "%");
    $(".assessment__content__progress__tooltip-text").css("left", 'auto');
  }
  else {
    $(".assessment__content__progress__tooltip-text").removeClass("final");
    $(".assessment__content__progress__tooltip-text").css("right", "auto");
    $(".assessment__content__progress__tooltip-text").css("left", percent - 3 + "%");
  }
  $(".assessment__content__progress__tooltip-percent p").text(percent + "%");
  $(".assessment__content__progress__tooltip-percent").css("left", percent - 3 + "%");
  if (percent === 20 || percent === 40 || percent === 60 || percent === 80 || percent === 100) {
    $(".assessment__content__progress__tooltip-percent").addClass("show");
    $(".assessment__content__progress__tooltip-text").addClass("show");
  }
  else {
    $(".assessment__content__progress__tooltip-percent").removeClass("show");
    $(".assessment__content__progress__tooltip-text").removeClass("show");
  }
  $(".assessment__content__questions").css("padding-top", $(".assessment__content__progress__tooltip-text").height());
}

/// ===============================================================  ///

// Our On hover show linkedin btn
function ourTeamHover() {
  $(".ourteam__sec2__content__linkedin").css("right", parseInt($(".ourteam__sec2__content__detail__hover").css("margin-right")) + 12 + 15);
  $(".ourteam__sec3__content__linkedin").css("right", parseInt($(".ourteam__sec3__content__detail__hover").css("margin-right")) + 12 + 15);
  $(".ourteam__sec4__content__linkedin").css("right", parseInt($(".ourteam__sec4__content__detail__hover").css("margin-right")) + 12 + 15);
}

/// ===============================================================  ///

// On click change avatar
function selectAvatar(v) {
  $(".edit__profile-choose-avatar1").removeClass("active");
  $(".edit__profile-choose-avatar2").removeClass("active");
  $(".edit__profile-choose-avatar3").removeClass("active");
  $(".edit__profile-choose-avatar4").removeClass("active");
  $(".edit__profile-choose-avatar5").removeClass("active");
  $("." + v).addClass('active');
}

/// ===============================================================  ///

// Thrive code copy to clipboard
function copyThriveCode(id) {
  var copyText = document.getElementById(id);
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
}

/// ===============================================================  ///

function mayBeLater(modelId){
    $(`#${modelId}`).modal('hide');
}

// Assessment page on answer checked hover to next question
$("input.qcheckbox:checkbox").on('click', function () {
  var $box = $(this);
  if ($box.is(":checked")) {
    var group = "input.qcheckbox:checkbox[name='" + $box.attr("name") + "']";
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});
selectedOptionCount = 0;
function scrollToNext(qid, current, next, selectedOptionId) {
  if ($("#" + qid).is(":checked")) {
    selectedOptionCount++;
    if ($('.assessment__question__options').length == selectedOptionCount) {
      $('#nextButton').attr('disabled', false);
    }
    console.log(selectedOptionCount);
    // console.log($("#" + next));
    $(".assessment__question__nextbtn").css("padding-bottom", $(window).height() / 2 - 48);
    if (next) {
      $.ajax({
        type: 'POST',
        data: { _token: $('[name="csrf-token"]').attr('content'), 'option_question_id': selectedOptionId, 'assessment_id': $('#assessment_id').val() },
        url: "save-option",
        success: function (data) {
          if (data.message == 'completed') {
            sessionStorage.setItem('show_screening_complete_congrats_popup', true);
            $('#nextButton').attr('onclick', 'window.location="' + $('base').attr('href') + '/dashboard"');
            $('#nextButton').text('Submit');
            showProgress("block", 100, "You're almost there, Keep Going");
            assessmentTooltipWidth();

          }
          if ($("#" + next).length !== 0) {
            $('html, body').animate({
              scrollTop: $("#" + next).offset().top - $(".navbar").outerHeight()
            }, 600);
          }
          else {
            $('html, body').animate({
              scrollTop: $("#nextButton").offset().top - $(".navbar").outerHeight()
            }, 600);
          }
          $(".assessment__question").removeClass('active');
          $(".assessment__question").removeClass('currentactive');
          $("#"+current).addClass("currentactive");
          $("#" + next).addClass("active");
        }
      });
    }
  }
}

/// ===============================================================  ///

// Send Full report Modal show
function sendFullReport() {
  $("#sendfullreport").modal('show');
}

/// ===============================================================  ///

// Already loggein in user modal popup show
$(function () {
  if (!sessionStorage.getItem('show_login_popup')) {
    sessionStorage.setItem("show_login_popup", true);
  }
  if (sessionStorage.getItem('show_login_popup') == "true") {
    $("#login__popup").modal('show');
    sessionStorage.setItem('show_login_popup', false);
  }

  $("#alreadypaid").modal('show');  // Already paid modal show
  // $("#reportreading").modal('show');  // Report Reading modal show
  // $("#verifyaccount").modal('show');
  // $("#dateslot").datepicker({ minDate: 4, maxDate: "+1M +10D" });
//   $("#sendFurtherIntruction").modal('show');
  //$("#dateslot").datepicker({ minDate: 4, maxDate: "+1M +10D" });
  // $("#reportfree").modal('show');
  // $("#reportreview").modal('show');
});

/// ===============================================================  ///

// individual signup page age verification

function onConfirmAge() {
  if($("#confirmage").is(":checked")){
    $("#sendotptoparent").modal('show');
  }
}

function sendViaEmail() {
  $("#sendotptoparent").modal('hide');
  $("#emailInput").modal('show');
}

function sendViaPhone() {
  $("#sendotptoparent").modal('hide');
  $("#phoneInput").modal('show');
}

$("#sendotptoparent").on('hidden.bs.modal', function(){
  $('#confirmage').prop('checked', false);
});

function inputAge() {
  if($("[name='age']").val() < 18 && $('#under_age').val() == 0  ){
    $('.signup_form__age__verifyage').css("display", 'block');
    $('.parent_approval_label').css("color","red");
    $('.approval_label').attr("data-before","red");
  }
  else if($("[name='age']").val() < 18 && $('#under_age').val() == 1 ){
    $('.signup_form__age__verifyage').css("display", 'block');
  }
  else {
    $('.signup_form__age__verifyage').css("display", 'none');
  }
}

function showEmailInput() {
  var service_id = $('#service_id').val();
  $('#other_service').val(service_id);
  $("#emailInput").modal('show');
}
function showCouponInfo(id) {
  url = $('base').attr('href')+"/other-services/"+ id;
  var result =""
  $.ajax({
    method:"get",
    url: url,
    success:function(data)
    {
      if(data.coupon == null){
        result = `Click on the link below to get ${data.discount}% discount`;
        $('.coupon_btn').hide();
        // $('#coupon_code').hide();
      }else{
        $('#coupon_code').val(data.coupon)

        $('.coupon_btn').show();

        if(data.buy_link == null || data.buy_link == '' ){
          result = `Please use coupon code ${data.coupon} to get ${data.discount}% discount`
        }else{
          result = `Please use coupon code ${data.coupon} to get ${data.discount}% discount click on the link below`;
        }
      }
      $('#info').html(result)
      if(data.buy_link == null || data.buy_link == '' ){
        $('#link').css('visibility', 'hidden')
      }else{
        $('#link').attr('href', data.buy_link);
      }
      // data.buy_link == '' ? $('#link').css('visibility', 'hidden') :$('#link').attr('href', data.buy_link);
      $("#couponInfo").modal('show');
    },

  })

}
function copyToClipboard() {
  /* Get the text field */
  var copyText = document.getElementById("coupon_code");

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */

  /* Copy the text inside the text field */
  document.execCommand("copy");


  /* Alert the copied text */
  $('#toast').css('visibility', 'visible')
  $("#toast").addClass("dashboard__toast__show");
  $('#messageInfo').html("Copied the code: " + copyText.value)
  setTimeout(()=>{
    $("#toast").removeClass("dashboard__toast__show");
  }, 2000);
  // alert("Copied the code: " + copyText.value);
}

function showToast(message) {
  $('#toast').css('visibility', 'visible')
  $("#toast").addClass("dashboard__toast__show");
  $('#toast-text').text(message);
  setTimeout(()=>{
    $("#toast").removeClass("dashboard__toast__show");
  }, 2000);
}
/// individual signup page age verification end here

function selectTimeSlot(e) {
  $("#" + e).removeClass('active');
  $(".time1").removeClass('active');
  $(".time2").removeClass('active');
  $(".time3").removeClass('active');
  $(".time4").removeClass('active');
  $(".time5").removeClass('active');
  $("#" + e).addClass('active');
  $("#timeslot").val($("#" + e).text());
}

// **  Blog page js code  ** //

$(".blog__carousel, .videos__carousel, .audios__carousel").each(function() {
  $(this).owlCarousel({
    loop:true,
    margin: 34,
    responsiveClass:true,
    dots: false,
    responsive:{
      0:{
        items:1,
        nav:true,
        loop:false
      },
      600:{
        items:2,
        nav:true,
        loop:false
      },
      991:{
        items:2,
        nav:true,
        loop:false
      },
      1200:{
        items:3,
        nav:true,
        loop:false
      }
    }
  });
});

$(document).on('ready', function() {
  $(".blog__content__blogs__cards .owl-carousel .owl-nav .owl-next").html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>');
  $(".blog__content__blogs__cards .owl-carousel .owl-nav .owl-prev").html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>');
});

// function nextPrevDisable() {
//   if($(".owl-next").hasClass("disabled")) {
//     $(".blog__content__blogs__cards__next-prev__btn__next").hide();
//   }
//   else {
//     $(".blog__content__blogs__cards__next-prev__btn__next").show();
//   }
//   if($(".owl-prev").hasClass("disabled")) {
//     $(".blog__content__blogs__cards__next-prev__btn__prev").hide();
//   }
//   else {
//     $(".blog__content__blogs__cards__next-prev__btn__prev").show();
//   }
// }
// $(".blog__content__blogs__cards__next-prev__btn__next").click(function(){
//   nextPrevDisable();
//   $(this).closest('.blog__content__blogs__cards').find('.owl-carousel').trigger('next.owl.carousel');
// });
// $(".blog__content__blogs__cards__next-prev__btn__prev").click(function(){
//   nextPrevDisable();
//   $(this).closest('.blog__content__blogs__cards').find('.owl-carousel').trigger('prev.owl.carousel');
// });
