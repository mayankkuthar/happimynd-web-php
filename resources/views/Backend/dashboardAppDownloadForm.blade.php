<x-backend-layout>
    <x-slot name="title">
        Bitrix Form | Upload
    </x-slot>
    <x-slot name="content">
      <!-- page content -->
      <div class="right_col" role="main">
        <div class="container" align="center">
          <br />
          <h3 align="center">Upload download Link for App download for dashboard</h3>
          <br />
          <div class="row">
            <div class="col-md-12">
              <div class="image_area">
                <form method="post" action='{{ route('admin.staticData.dashboardAppDownload') }}'>
                  @csrf
                  <div class="field item form-group @error('androidLink') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">Andriod Link<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" id="androidLink" placeholder="paste link here" name="androidLink" required="required" value="{{ $androidLink ?? '' }}">
                    </div>
                    @error('androidLink')
                    <div class="alert" id="androidLink-error"> {{ $message }}</div>
                    @enderror
                  </div>
                  <button type="submit" class="btn btn-primary">
                    <span>Save</span>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span><span class="" style="display: none">  Saving...</span>
                  </button>
                </form>
              </div>
              </div>
            </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="image_area">
                  <div style="display:none;" class="alert alert-dismissible" role="alert" id ="errorMsg">
                  </div>
              <form method="post" id = "form" class="form-group">
                @csrf
                <div class="field item form-group @error('iosLink') bad @enderror">
                  <label class="col-form-label col-md-3 col-sm-3  label-align">IOS Link<span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6">
                      <input class="form-control" id="iosLink" placeholder="paste link here" name="iosLink" required="required" value="{{ $iosLink ?? '' }}">
                  </div>
                  @error('iosLink')
                  <div class="alert" id="iosLink-error"> {{ $message }}</div>
                  @enderror
                </div>
                <div class="col text-center">
                  <button type="submit" class="btn btn-primary" id="upload">
                    <span id="buttonText">Save</span>
                    <span class="spinner-border spinner-border-sm loader" id="loader" role="status" aria-hidden="true" style="display: none;"></span><span class="loader" style="display: none">  Saving...</span>
                  </button>
                </div>
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
            $(document).ready(function() {
              $('#form').submit(function(e){
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                e.preventDefault();
                var formdata = new FormData(this)

                $.ajax({
                  url:'{{ route("admin.staticData.saveDashboardIosLink") }}',
                  method:'POST',
                  processData: false,
                  contentType: false,
                  dataType:'JSON',
                  data:formdata,
                  beforeSend: function(){
                    $('#buttonText').hide();
                    $('.loader').show();
                  },
                  success:function(data)
                  {
                    $('#iosLink').html(data.message.iosLink)
                    $('#buttonText').show();
                    $('.loader').hide();
                    $('#errorMsg').show()
                    $('#errorMsg').removeClass('alert-danger')
                    $('#errorMsg').addClass('alert-success')
                    $('#errorMsg').html('Link saved successfully')
                  },
                  error: function (xhr, status, error) {
                    var err = JSON.parse(xhr.responseText);
                    var errArray = err.errors
                    $('#errorMsg').show()
                    $('#errorMsg').html('')
                    $('#errorMsg').addClass('alert-success')
                    $('#errorMsg').addClass('alert-danger')
                    Object.values(errArray).forEach(element => {
                        var errmsg = `<li> ${element[0]} </li>`
                        $('#errorMsg').append(errmsg)
                        $('#buttonText').show();
                        $('.loader').hide();
                    });
                  }
                });
              });
          });

        </script>
    

    </x-slot>
</x-backend-layout>
