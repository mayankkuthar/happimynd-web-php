/** Defines constants */
const sendfullreportmodal = "sendfullreport";
const sendfullreportmodalFormId = "update_email_popup_form";
const sendfullreportmodalContinueButtonId = "conitnue_email_popup_form";

const reportreadingModal = "reportreading";
const reportreadingFormId = "update_mobile_popup_form";
const reportreadingFormIdContinueButtonId = "conitnue_mobile_popup_form";
const reportreadingLaterButtonId = "later_mobile_popup_form";

const verifyaccountModal = "verifyaccount";
const verifyaccountSendEmailButtonId = "verify_email_code";
const verifyaccountSendMobileButtonId = "verify_mobile_code";

const calltimeModal = "calltime";
const calltimeFormId = "calltime_popup_form";
const saveCalltimeButtonId = "save_calltime_popup_form";

const confirmcodeModal = "confirmcode";
const confirmcodePopupFormId = "confirmcode_popup_form";
const verifyEmailCode = "verify_email_code";
const verifyMobileCode = "verify_mobile_code";
const verifyConfirmcodePopupFormButtonId = "verify_confirmcode_popup_form";

const assessmentSuccessModal = "assessment_success";
const assessmentSuccessH1Id = "assessment_success_h1";
const assessmentSuccessPId = "assessment_success_p";
const assessmentSuccessMessageH1 = "Congratulations !";
const assessmentSuccessMessageP = "Your HappiLIFE Awareness Tool Summary is ready for download. It’s also available in your Dashboard & will be shared on your shared mail ID.";
const appointmentBookMessageH1 = "Your Appointment is successfully booked!";
const appointmentBookMessageP = "We will contact you soon.";
const emailVerifiedMessage = "Email is successfully verified !!";
const mobileVerifiedMessage = "Mobile is successfully verified !!";
const assessmentCompletionMessage = "Please complete the screening !!";
const otpMessage = "Confirmation code has been sent to your ";


var otpType = null;
var assessment = null;
var user = {
  user: null,
  assessment: null,
  plans: null,
  psychologistAppointmentStatus: null,
  popups: function () {
    if (sessionStorage.getItem('show_happichat_instruction') || sessionStorage.getItem('show_happitalk_instruction')) {
      if (sessionStorage.getItem('show_happichat_instruction')) {
        dontShowMobileForm = true;
        showHappiChatInstructionMessage();
      }
      else if (sessionStorage.getItem('show_happitalk_instruction')) {
        if (user.psychologistAppointmentStatus == "pending") {
          startPsychologistAppointmentBooking();
        } else {
          dontShowMobileForm = true;
          showHappiTalkInstructionMessage();
        }
      }
    }
    else {
      if (sessionStorage.getItem('show_screening_complete_congrats_popup') == "true") {
        checkScreeningCompletionPopup();
        $('#' + assessmentSuccessModal).on('hidden.bs.modal', function () {
          initiatePopups();
        });
      } else {
        initiatePopups();
      }
    }
  },
  set setUser(user) {
    this.user = user
    this.psychologistAppointmentStatus = user.psychologist_appointment_status
    this.popups();
  },
  get isEmailVerified() {
    return this.user['verify']['email_verified'];
  },
  set emailVerify(value) {
    this.user['verify']['email_verified'] = value;
    this.popups();
  },
  get isMobileVerified() {
    return this.user['verify']['mobile_verified'];
  },
  set mobileVerify(value) {
    this.user['verify']['mobile_verified'] = value;
  },
  set appointmentStatus($value) {
    this.user['appointment_status'] = $value;
  },
  get appointmentStatus() {
    return this.user['appointment_status'];
  },
  set setPlans(plans) {
    this.plans = plans;
  },
  set setAssessment(assessment) {
    this.assessment = assessment;
  }
};
var plans = [];
$(document).ready(function () {
  if (sessionStorage.getItem('open_thrivecode_popup') == "true") {
    sessionStorage.removeItem('open_thrivecode_popup');
  }
  if (sessionStorage.getItem('show_screening_complete_congrats_popup') == "true") {
    show_congrats_popup = true;
    checkScreeningCompletionPopup();
    sessionStorage.removeItem('show_screening_complete_congrats_popup')
  }
  else {
    verificationData();
  }
});
$('#modal_template').on('hidden.bs.modal', function () {
  $('#modal_template_description').empty();
  $('#modal_template_h1').empty();
})
function verificationData() {
  $.ajax({
    type: "GET",
    url: $('base').attr('href') + '/verificationData',
    success: function (data) {
      console.log(data);
      if (data.error == false) {
        user.setAssessment = data.message.assessment;
        user.setPlans = data.message.plans;
        user.setUser = data.message.user;
        // user.psychologistAppointmentStatus = data.message.user.psychologistAppointment.status
      }
    }
  });
}
function showOnlyMailVerify() {
  $('#send_full_report_modal_h1').text('Verify your email')
  $('#send_full_report_modal_p').empty();

  showEmailVerifyModal();

}
function showHappiChatInstructionMessage() {
  var message = 'If you are already availing HappiTALK Services, use the same access for HappiCHAT.';
  if (!user.isEmailVerified) {
    message += ' Else, verify your mail ID to receive access details in 48 hrs';
    $('#modal_template_button').text('Verify Email').attr('onclick', 'showOnlyMailVerify()');
  }
  else {
    message += 'Else, you will receive a mail from HappiMynd in next 48 Hrs with link to access HappiCHAT.'
    $('#modal_template_button').text('Ok');
  }
  $('#modal_template_description').html('<p>'+message+'</p>')
  $('#modal_template').modal('show');
  sessionStorage.removeItem('show_happichat_instruction');
}

function showHappiTalkInstructionMessage() {
  var message = "If you are already availing HappiCHAT Services, use the same access for HappiTALK.";
  if (!user.isEmailVerified) {
    message += ' Else, verify your mail ID to receive access details in 48 hrs';
    $('#modal_template_button').text('Verify Email').attr('onclick', 'startPsychologistAppointmentBooking()');
  }
  else {
    message += 'Else, you will receive a mail from HappiMynd in next 48 Hrs with link to access HappiTALK.'
    $('#modal_template_button').text('Ok');
  }
  $('#modal_template_description').html('<p>' + message + '</p>');
  $('#modal_template').modal('show');
  sessionStorage.removeItem('show_happitalk_instruction');
}

function otpPopups() {
  if (user.assessment != null) {
    //if assessment is given by user
    if (user.assessment['isCompleted']) {
      triggerPopup();
    }
    else {
      // alert(assessmentCompletionMessage);
      showToast(assessmentCompletionMessage)
    }
  }
}


function triggerPopup() {
  if ((sessionStorage.getItem('show_happichat_instruction') != "true" && sessionStorage.getItem('show_happitalk_instruction') != "true")) {
    if (!user.plans.hasOwnProperty('HappiLIFE Screening') && !user.plans.hasOwnProperty('HappiLIFE Summary Reading')) {
      showEmailVerifyModal();
    }
    else if (user.plans.hasOwnProperty('HappiLIFE Screening') && !user.plans.hasOwnProperty('HappiLIFE Summary Reading')) {
      showEmailVerifyModal();
    }
    else if (user.plans.hasOwnProperty('HappiLIFE Screening') && user.plans.hasOwnProperty('HappiLIFE Summary Reading')) {
      showReportReadingPopup();
    }
  }
}
function showEmailVerifyModal() {
  console.log('email')
  if (!user.isEmailVerified) {
    $('#' + sendfullreportmodal).modal('toggle');
    $('#'+verifyEmailCode).show();
    $('#'+verifyMobileCode).hide();

    // var popup_disable_status = $('#popup_disable_status').val();
    // if(popup_disable_status == 1){
    //     $('#' + sendfullreportmodal).unbind("click");
    // }

  }
  else if(!user.isMobileVerified){
    showMobileVerifyModal();
  }
}

function showMobileVerifyModal() {
  if ($('#' + verifyaccountModal).is(":hidden") && $('#' + verifyMobileCode).is(":hidden")) {
    //if already verify mobile code is open dont show mobile verify modal
    if (!user.isMobileVerified) {
      $('#' + reportreadingModal).modal('toggle');
      $('#' + verifyMobileCode).show();
      $('#' + verifyEmailCode).hide();

      // var popup_disable_status = $('#popup_disable_status').val();
      // if(popup_disable_status == 1){
      //     $('#' + reportreadingModal).unbind("click");
      // }

    }
  }
}

function showEmailMobileVerifyModal() {
  if (!user.isEmailVerified && !user.isMobileVerified) {
    $('#' + verifyEmailCode).show();
    $('#' + verifyMobileCode).show();
    $('#' + sendfullreportmodal).modal('toggle');
  }
}

function showReportReadingPopup() {

  if(!user.isMobileVerified && user.isEmailVerified) {
    showMobileVerifyModal();
  }
  else if (user.isMobileVerified && !user.isEmailVerified) {
    showlMobileVerifyModal();
  }
  else if (!user.isEmailVerified && !user.isMobileVerified) {
    showEmailMobileVerifyModal();
  }
}

function showCallTimePopup() {
  if (user.isEmailVerified && user.isMobileVerified && user.appointmentStatus == false) {
    $('#' + calltimeModal).modal('toggle');
  }
}
function showCongratsPopup() {
  if (sessionStorage.getItem('show_screening_complete_congrats_popup') == "true" || (user.plans.hasOwnProperty('HappiLIFE Screening') && show_congrats_popup)) {
    $('#' + verifyaccountModal).modal('hide');
    $('#' + assessmentSuccessH1Id).html(assessmentSuccessMessageH1);
    $('#' + assessmentSuccessPId).html(assessmentSuccessMessageP);
    $("#" + assessmentSuccessModal).modal('show');
    show_congrats_popup=false
  }
}

function showAppointmentSuccessPopup() {
  if (show_appointment_success) {
    $('#'+assessmentSuccessH1Id).html(appointmentBookMessageH1);
    $('#'+assessmentSuccessPId).html(appointmentBookMessageP);
    $("#" + assessmentSuccessModal).modal('show');
    show_appointment_success = false;
  }
}
/**
* show Congrats popup after screening completion
* session item is set on assessment completion
*/
function checkScreeningCompletionPopup() {
  if (sessionStorage.getItem('show_screening_complete_congrats_popup') == "true") {
    showCongratsPopup();
    sessionStorage.setItem('show_screening_complete_congrats_popup', false);
  }
}

function appointmentData() {
  if (user.plans.hasOwnProperty('HappiLIFE Summary Reading') && user.appointmentStatus == false) {


    // console.log(booked_dates);
    booked_dates_array = Array();
    disableDates = Array(); //TODO: api for disabled and enabled
    booked_dates = Array();
    $.ajax({
      type: "GET",
      url: $('base').attr('href') + '/booked-dates',
      success: function (data) {
        console.log(data);
        if (data.error == false) {
          booked_dates = data.message.booked_dates;
          disableDates = data.message.disableDates;
          booked_dates_array = booked_dates;
          $('#dateslot').datepicker({
            // minDate: 4,
            maxDate: "+1M +10D",
            dateFormat: 'mm-dd-yy',
            constrainInput: true,
            beforeShowDay: noWeekendsOrHolidays,
            onSelect: callAvailableDates
          });
          $('#calltime_assessment_id').val(user.assessment['id']);
          showCallTimePopup();
        }
      }
    });
    for (var i = 0; i < booked_dates.length; i++) {
      let pre_book_date = booked_dates[i]['available_date'].replaceAll('/', '-');
      pre_book_date = pre_book_date.substring(2);
      pre_book_date = String(parseInt(booked_dates[i]['available_date'].replaceAll('/', '-').slice(0, 2), 10)).concat(pre_book_date);

      //if day havign 01 - 09 day then we need to convert it to 1-9
      if (pre_book_date.length == 9 && pre_book_date[2] == '0') {
        let day = String(parseInt(pre_book_date.substring(2, 4), 10));
        pre_book_date = pre_book_date.replace(pre_book_date.substring(2, 4), day);
      }

      if (!booked_dates_array.includes(pre_book_date)) {
        booked_dates_array.push(pre_book_date);
      }
    }

    console.log(disableDates);

    for (var i = 0; i < disableDates.length; i++) {
      let pre_book_date = disableDates[i]['date'].replaceAll('/', '-');
      pre_book_date = pre_book_date.substring(2);
      pre_book_date = String(parseInt(disableDates[i]['date'].replaceAll('/', '-').slice(0, 2), 10)).concat(pre_book_date);

      if (pre_book_date.length == 9 && pre_book_date[2] == '0') {
        let day = String(parseInt(pre_book_date.substring(2, 4), 10));
        pre_book_date = pre_book_date.replace(pre_book_date.substring(2, 4), day);
        // console.log("--> ",pre_book_date);
      }

      if (!booked_dates_array.includes(pre_book_date)) {
        booked_dates_array.push(pre_book_date);
      }
    }
  }
}

/* create an array of days which need to be disabled */
//   var disabledDays = ["04-21-2021", "04-24-2021", "02-27-2021", "2-28-2021", "3-3-2021", "3-10-2021", "3-17-2021", "4-2-2021", "4-3-2021", "4-4-2021", "4-5-2021"];

/* utility functions */
function nationalDays(date) {
  var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
  //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
  for (i = 0; i < booked_dates_array.length; i++) {
    if ($.inArray((m + 1) + '-' + d + '-' + y, booked_dates_array) != -1 || new Date() > date) {
      //console.log('bad:  ' + (m+1) + '-' + d + '-' + y + ' / ' + booked_dates_array[i]);
      return [false];
    }
  }
  //console.log('good:  ' + (m+1) + '-' + d + '-' + y);
  return [true];
}
function noWeekendsOrHolidays(date) {
  console.log('---------> ',date);
  var noWeekend = $.datepicker.noWeekends(date);
  return noWeekend[0] ? nationalDays(date) : noWeekend;
}


function callAvailableDates(){
  var selectedDate = $('#dateslot').val();
  $.ajax({
    type: "get",
    url: $('base').attr('href')+"/get-available-dates",
    data: {date : selectedDate },
    success: function (data) {
      var result = "";
      data.forEach(element=>{
        result += `
        <button type="button" class="time time1" onclick="selectTimeSlot(${element.id});" id="${element['id']}">${element['time']}</button>
        `
      })
      $('.calltime__popup__form__input__time__content').html(result)

    }
  });

}




$(document).ready(()=>{
  if($("[name='email']").val()!=""){
    $('#'+sendfullreportmodalContinueButtonId).prop('disabled',false);
  }
  if($("[name='mobile']").val().length > 8 && $("[name='mobile']").val().length < 20){
    $('#'+reportreadingFormIdContinueButtonId).prop('disabled',false);
  }

  if($("[name='country_id']").val() != ''){
    $('#'+reportreadingFormIdContinueButtonId).prop('disabled',false);
  }

});

function initiatePopups() {

  //check and show screening completed congrats popus
  checkScreeningCompletionPopup();

  //check if email and mobile verified and show popups
  otpPopups();

  appointmentData();


}

$('#' + assessmentSuccessModal).on('hidden.bs.modal', function () {
  if (user.user == null) {
    verificationData();

  }
  else if (user.plans.hasOwnProperty('HappiLIFE Summary Reading') && user.isEmailVerified && !user.isMobileVerified) {
    if (show_mobile_verify) {
      showMobileVerifyModal();
    } else {
      $('#'+verifyEmailCode).hide();
      $('#'+verifyMobileCode).show();
      $('#' + verifyaccountModal).modal('show');
    }
  }
})

/**  For email updation */
$("[name='email']").keyup(()=>{
  if($("[name='email']").val()!=""){
    $('#'+sendfullreportmodalContinueButtonId).prop('disabled',false);
  }else{
    $('#'+sendfullreportmodalContinueButtonId).prop('disabled',true);
  }
});

$("#" + sendfullreportmodalFormId).submit(function (e) {

  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $('#'+sendfullreportmodalFormId);
  $.ajax({
    type: $('#' + sendfullreportmodalFormId).attr('method'),
    url: $('#' + sendfullreportmodalFormId).attr('action'),
    data: form.serialize(),
    dataType: 'json',
    beforeSend: function () {
      $('.error-message').remove(); //remove errors before ajax call
      $('.error').removeClass('error');
      showLoader(sendfullreportmodalFormId);
      $('#' + sendfullreportmodalContinueButtonId).prop('disabled', true);
    },
    success: function (data) {
      disableLoader(sendfullreportmodalFormId)
      $('#' + sendfullreportmodal).modal('toggle');
      if (!user.isMobileVerified && user.plans.hasOwnProperty('HappiLIFE Summary Reading') && typeof dontShowMobileForm === "undefined") {
        $('#' + reportreadingModal).modal('toggle');
      } else {
        $('#' + verifyaccountModal).modal('toggle');

        // var popup_disable_status = $('#popup_disable_status').val();
        // if(popup_disable_status == 1){
        //     $('#' + verifyaccountModal).unbind("click");
        // }

      }

      $('#' + sendfullreportmodalFormId).trigger("reset");
    },
    error: function (data) {
      populateErrors(data);
      disableLoader(sendfullreportmodalFormId);
    },
  });
})
/** End email update form */

/**  For mobile No updation */
$("[name='mobile']").keyup(() => {
  if($("[name='mobile']").val().length > 8 && $("[name='mobile']").val().length < 20 && $("[name='country_id']").val() != ''){
    $('#'+reportreadingFormIdContinueButtonId).prop('disabled',false);
  }else{
    $('#'+reportreadingFormIdContinueButtonId).prop('disabled',true);
  }
});

$("[name='country_id']").change(() => {
  if($("[name='mobile']").val().length > 8 && $("[name='mobile']").val().length < 20 && $("[name='country_id']").val() != ''){
    $('#'+reportreadingFormIdContinueButtonId).prop('disabled',false);
  }else{
    $('#'+reportreadingFormIdContinueButtonId).prop('disabled',true);
  }
});

show_mobile_verify = false;
$("#"+reportreadingLaterButtonId).click(function(){
  $("#"+reportreadingModal).modal("toggle");
  if(user.plans.hasOwnProperty('HappiLIFE Summary Reading')){
    if(!user.isEmailVerified){
      $('#'+verifyMobileCode).hide();
      $('#' + verifyaccountModal).modal('show');
      show_mobile_verify = true;
    }
  }
})

$("#" + reportreadingFormId).submit(function (e) {

  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $('#'+reportreadingFormId);
  $.ajax({
    type: $('#' + reportreadingFormId).attr('method'),
    url: $('#' + reportreadingFormId).attr('action'),
    data: form.serialize(),
    dataType: 'json',
    beforeSend: function () {
      $('.error-message').remove(); //remove errors before ajax call
      $('.error').removeClass('error');
      // showLoader(reportreadingFormId);
      // alert('ss');
      $('#'+reportreadingFormIdContinueButtonId).prop('disabled',true);
    },
    success: function (data) {
      disableLoader(reportreadingFormId)
      $("#"+reportreadingModal).modal('toggle');
      $('#'+verifyaccountModal).modal('toggle');

      $('#'+reportreadingFormId).trigger("reset");

      // var popup_disable_status = $('#popup_disable_status').val();
      // if(popup_disable_status == 1){
      //     $('#' + verifyaccountModal).unbind("click");
      // }

    },
    error: function (data) {
      populateErrors(data);
      errorCallback;
      disableLoader(reportreadingFormId);
    },
  }).done(function () {
    removeErrorMsgClassOnTypeEvent(reportreadingFormId);
  });
})
/** End mobile update form */

/** Verify email and Otp */
/** OTP verification form submission */
$("[name='otp']").keyup(()=>{
  if($("[name='otp']").val().length==6){
    $('#'+verifyConfirmcodePopupFormButtonId).prop('disabled',false);
  }else{
    $('#'+verifyConfirmcodePopupFormButtonId).prop('disabled',true);
  }
});

$("#"+verifyConfirmcodePopupFormButtonId).click(()=>{
  if(otpType=="email"){
    $("#confirmcode_popup_form").prop("action", $('base').attr('href')+"/verify-email-otp");
  }else if(otpType=="mobile"){
    $("#confirmcode_popup_form").prop("action", $('base').attr('href')+"/verify-mobile-otp");
  }
});

$("#" + confirmcodePopupFormId).submit(function (e) {

  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $('#'+confirmcodePopupFormId);
  $.ajax({
    type: $('#' + confirmcodePopupFormId).attr('method'),
    url: $('#' + confirmcodePopupFormId).attr('action'),
    data: form.serialize(),
    dataType: 'json',
    beforeSend: function () {
      $('.error-message').remove(); //remove errors before ajax call
      $('.error').removeClass('error');
      showLoader(confirmcodePopupFormId);
      $('#'+verifyConfirmcodePopupFormButtonId).prop('disabled',true);
    },
    success: function (data) {
      disableLoader(confirmcodePopupFormId)
      console.log('datafsdf:', data)
      if (data['message'] == "Successfully, Mobile OTP is verified.!" || data['message'] == "Successfully, Email OTP is verified.!") {
        if (otpType == "email") {
          showToast(emailVerifiedMessage);
          user.emailVerify = true;

          //for mobile popup
          window. location. reload();

        }
        else if (otpType == "mobile") { showToast(mobileVerifiedMessage); user.mobileVerify = true; $('#' + verifyaccountModal).modal('hide'); }
        $("#" + confirmcodeModal).modal('hide');

        if($('#' + verifyMobileCode).is(":hidden") != false){

          if (!user.plans.hasOwnProperty('HappiLIFE Screening') &&
            sessionStorage.getItem('show_happichat_instruction') != "true" &&
            sessionStorage.getItem('show_happitalk_instruction') != "true"
          ) {
            if (user.assessment.isCompleted) {
              // window.location = $('base').attr('href') + "/report-preview?assessment_id=" + user.assessment['id'];
              location.reload();
            }
          } else if (user.plans.hasOwnProperty('HappiLIFE Screening') && !user.plans.hasOwnProperty('HappiLIFE Summary Reading')) {
            show_congrats_popup = true;
            showCongratsPopup();
          } else if (user.plans.hasOwnProperty('HappiLIFE Summary Reading')) {
            // console.log(data);
            if (user.isEmailVerified && user.isMobileVerified) {   // if both verified
              $('#' + verifyaccountModal).modal('hide');
              if (!user.user.appointmentStatus) {
                show_appointment_success = true;
                appointmentData();
              }
            } else if (user.isEmailVerified && !user.isMobileVerified) { // if email verified & mobile not verified
              // $('#' + verifyaccountModal).modal('hide');
              if (show_mobile_verify) {
                $('#' + verifyaccountModal).modal('hide');
                // showMobileVerifyModal();
              } else {
                $('#' + verifyMobileCode).show();
                $('#' + verifyEmailCode).hide();
                $("#" + reportreadingModal).modal('hide');
                $('#' + verifyaccountModal).modal('show');
              }
            }
            else if (!user.isEmailVerified && user.isMobileVerified) { // if email verified & mobile not verified
              $('#' + verifyaccountModal).modal('hide');
              showEmailVerifyModal();
            }
          }
        }
        else if (user.plans.hasOwnProperty('HappiLIFE Summary Reading')) {
          // console.log(data);
          if (user.isMobileVerified) {   // if both verified
            $('#' + verifyaccountModal).modal('hide');
            if (!user.user.appointmentStatus) {
              show_appointment_success = true;
              appointmentData();
            }
          }
        }
        else {
          $('#' + verifyEmailCode).hide();
        }
      }
    },
    error: function (data) {
      populateErrors(data);
      // errorCallback;
      disableLoader(confirmcodePopupFormId);
    },
  }).done(function () {
    removeErrorMsgClassOnTypeEvent(confirmcodePopupFormId);
  });
})

$("#" + calltimeFormId).submit(function (e) {

  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $('#' + calltimeFormId);
  var formData = $(form).serialize() + '&assessment_id=' + user.assessment['id'];
  $.ajax({
    type: $('#' + calltimeFormId).attr('method'),
    url: $('#' + calltimeFormId).attr('action'),
    data: formData,
    dataType: 'json',
    beforeSend: function () {
      $('.error-message').remove(); //remove errors before ajax call
      $('.error').removeClass('error');
      showLoader(calltimeFormId);
      $('#'+saveCalltimeButtonId).prop('disabled',true);
    },
    success: function (data) {
      disableLoader(calltimeFormId)
      $("#"+calltimeModal).modal('toggle');
      $('#'+calltimeFormId).trigger("reset");
      user.appointmentStatus = true;
      show_appointment_success = true;
      showAppointmentSuccessPopup();
    },
    error: function (data) {
      populateErrors(data);
      disableLoader(calltimeFormId);
    },
  }).done(function () {
    removeErrorMsgClassOnTypeEvent(calltimeFormId);
  });
})

/** Method for generating OTP. */
function generateOTP(){
  var url = "";
  if(otpType=="email") url = $('base').attr('href')+"/generate-otp-email";
  else if(otpType=="mobile") url = $('base').attr('href')+"/generate-otp-mobile";

  if(url!=""){
    $.ajax({
      type: "get",
      url: url,
      success: function (data) {
        showToast(otpMessage+otpType);
        console.log(data);
      }
    });
  }
}

/** Method for verifying OTP modal opens */
function verifyOtp(type){
  if(type=="email"){
    $('#'+confirmcodeModal).modal("show");
    otpType = "email";
    generateOTP();

    $('#'+confirmcodePopupFormId).trigger("reset");
  }else if(type=="mobile"){
    $('#'+confirmcodeModal).modal("show");
    otpType = "mobile";
    generateOTP();

    $('#'+confirmcodePopupFormId).trigger("reset");
  }

  // var popup_disable_status = $('#popup_disable_status').val();
  // if(popup_disable_status == 1){
  //     $('#' + confirmcodeModal).unbind("click");
  // }

}

function generateGuardianOTP(otpType){
  var url = "";
  console.log($('base').attr('href'));
  if(otpType=="email")
  {
    var input = $("#email").val();
    var popUp = "emailInput"
    url = $('base').attr('href')+"/generate-guardian-otp-email";
  }

  else if(otpType=="mobile")
  {
    var input = $("#phone").val();
    var popUp = "phoneInput"
    url = $('base').attr('href')+"/generate-guardian-otp-mobile";
  }
  if(url!=""){
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      type: "post",
      url: url,
      data: {input:input},
      success: function (data) {
        $('#'+popUp).modal('toggle');
        $("#phone").val('');
        $("#email").val('');
        $('#confirmparentcode').modal("show");
        $('#otp').val('');
        $("#confirmcodeparent").val(otpType);
        $("#sessionId").val('');
        $("#sessionId").val(data['message']['session_id']);
      },

      error:function(data){
        var error = data.responseJSON;
        showToast("Something went wrong. Try again.");
      }

    });
  }
}
