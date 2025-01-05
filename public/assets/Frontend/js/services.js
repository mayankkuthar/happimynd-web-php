function OtherServiceMailList()
{
  var url = "";
  var other_service_id = $('#service_id').val();
  var name = $('#name').val();
  var email = $('#email').val();
  var mobile = $('#mobile').val();
  url = $('base').attr('href')+"/other-services-mail";

  if(url!=""){
    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

      $.ajax({
        type: "post",
        url: url,
        data: {
          other_service:other_service_id,
          name:name,
          email:email,
          mobile:mobile,

        },
        success: function (data) {
          console.log(data);
          paymentOrder(data);
          // window.location.href="/buy-bundles"
        },

        error:function(data){
          $( '#form-errors' ).hide();
          var error = data.responseJSON;
          console.log(error);
          if(error['message'] == "The given data was invalid."){
            errorsHtml = '<div class="alert alert-danger"><ul>';
            $.each( error['errors'] , function( key, value ) {
              console.log(key,value[0])
                errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
            });
            errorsHtml += '</ul></div>';

            $( '#form-errors' ).html( errorsHtml );
            $( '#form-errors' ).show();
            console.log(errorsHtml)
          }else{
            // alert("Something went wrong. Try again.");
            showToast("Something went wrong. Try again.");
          }
        }

    });
  }
}

function paymentOrder(data)
{
  url = $('base').attr('href')+"/other-services-payment";


  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

$.ajax({
  type: "post",
  url: url,
  data: {
    data
  },
  success: function (data) {
    paymentOrder(data);
    window.location.href="/buy-bundles"
  },

  error:function(data){
    $( '#form-errors' ).hide();
    var error = data.responseJSON;
    console.log(error);
    if(error['message'] == "The given data was invalid."){
      errorsHtml = '<div class="alert alert-danger"><ul>';
      $.each( error['errors'] , function( key, value ) {
        console.log(key,value[0])
          errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
      });
      errorsHtml += '</ul></div>';

      $( '#form-errors' ).html( errorsHtml );
      $( '#form-errors' ).show();
      console.log(errorsHtml)
    }else{
      // alert("Something went wrong. Try again.");
      showToast("Something went wrong. Try again.");
    }
  }

});
}

function addAuthor()
{
  var url = "";
  var author = $('#name').val();
  var host = window.location.origin;
  url= `${host}/admin/education-service-author`

  if(url!=""){
    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

      $.ajax({
        type: "post",
        url:url,
        data: {
          name:author,
        },
        success: function (data) {
          // window.location.href="/buy-bundles"
          getEducationalAuthors()
          alert("Author Added Successfully");
          $("#authorInput").modal('toggle');
        },

        error:function(data){
          var error = data.responseJSON;
          if(error['message'] == "The given data was invalid."){
            var errorMessage = error['errors']['name'][0]
            alert(errorMessage)
          }else{
            alert("Something went wrong. Try again.");
          }
        }

    });
  }

}

function getEducationalAuthors()
{
  var host = window.location.origin;
  url= `${host}/admin/education-service-author`

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })
  $.ajax({
      method:"GET",
      url: url,
      async:false,
    success:function(data){
      $("#author-select").html('')
      $("#author-select").html("<option value='0'>Select Author</option>");
     data.forEach(element => {
        console.log(element)
        $("#author-select").append(`<option value=${element.id}>${element.name}</option>`)
      });
      // console.log('XXXX',data);
      // $("#author-select").html('')
      // $("#author-select").append(data)
    },
    error:function(errors){

      var error = errors.responseJSON();
      alert(error)
    }
    });
}

function EducationServiceMailList()
{
  var url = "";
  var education_service_id = $('#service_id').val();
  var email = $('#email').val();
  url = $('base').attr('href')+"/other-services-mail";

  if(url!=""){
    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

      $.ajax({
        type: "post",
        url: url,
        data: {
          other_service:education_service_id,
          email:email,
        },
        success: function (data) {
          window.location.href="/buy-bundles"
        },

        error:function(data){
          var error = data.responseJSON;
          if(error['message'] == "The given data was invalid."){
            var errorMessage = error['errors']['email'][0]
            // alert(errorMessage)
            showToast(errorMessage)
          }else{
            // alert("Something went wrong. Try again.");
            showToast("Something went wrong. Try again.");
          }
        }

    });
  }
}

$('.other-services').click(function(){
  $('.other_service_option').show();
})
$('.happimynd-services').click(function(){
  $('.other_service_option').hide();
})
