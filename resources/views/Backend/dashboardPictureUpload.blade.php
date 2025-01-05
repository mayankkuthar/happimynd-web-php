<x-backend-layout>
    <x-slot name="title">
        Dashboard Picture | Upload
    </x-slot>
    <x-slot name="content">
      <!-- page content -->
      <div class="right_col" role="main">
        <div class="container" align="center">
          <br />
          <h3 align="center">Upload cover image for dashboard</h3>
          <br />
          <div class="row">
            <div class="col-md-12">
              <div class="image_area">
                <form method="post" id="uploadForm">
                  <div class="field item form-group ">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">Hyperlink<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" id="hyperlink" placeholder="paste link here" name="hyperlink" required="required" value="{{ $hyperlink ?? '' }}">
                    </div>
                  </div>
                  <div class="field item form-group ">
                    <label for="upload_image" class="col-form-label col-md-3 col-sm-3  label-align">Upload Image<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6">
                        <input type="file" name="cover_pic" id="upload_image" class="image">
                    </div>
                  </div>
                  <button type="submit" id="crop" class="btn btn-primary">
                    <span id="buttonText">Save</span>
                    <span class="spinner-border spinner-border-sm loader" id="loader" role="status" aria-hidden="true" style="display: none;"></span><span class="loader" style="display: none">  Uploading...</span>
                  </button>
                  <img src="{{ $dashboardPic ?? '' }}" id="uploaded_image" class="img-responsive" />
                </form>
              </div>
              </div>
            </div>
        </div>
      </div>
    </x-slot>
    <x-slot name="css">
      <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
    </x-slot>
    <x-slot name="js">
      <script src="https://unpkg.com/cropperjs"></script>
      <script>

        $(document).ready(function(){
          $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
          $('#uploadForm').submit(function(e){
            e.preventDefault();
            var formdata = new FormData(this)
            console.log(formdata);
            var link = $('#hyperlink').val();
            if(is_url(link)){
              $.ajax({
                  url:'{{ route("admin.staticData.uploadDashboardCoverPic.post") }}',
                  method:'POST',
                  data:formdata,
                  contentType: false,
                  processData: false,
                  beforeSend: function(){
                    $('#buttonText').hide();
                    $('.loader').show();
                  },
                  success:function(data)
                  {
                    console.log(data);
                    $('#buttonText').show();
                    $('.loader').hide();
                    $('#uploaded_image').attr('src', data.message.cover_pic);
                    $('#hyperlink').val(data.message.hyperlink);
                  }
                });
            }
            else{
              $('#hyperlink').addClass('is-invalid');
            }
          });
        });
        function is_url(str){
          regexp =   /^(https?|ftp|torrent|image|irc @if( env('APP_ENV') == 'http') |localhost @endif ):\/\/(-\.)?([^\s\/?\.#-]+\.?)+(\/[^\s]*)?$/i;;
          if (regexp.test(str)) {
            return true;
          }
          else {
            return false;
          }
        }
        </script>
    </x-slot>
</x-backend-layout>