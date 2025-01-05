<x-backend-layout>
    <x-slot name="title">
        Landing page video | Upload
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
                            <form method="post" action="{{ route('admin.staticData.uploadLandingPageVideo.post') }}" enctype="multipart/form-data" id="formxx">
                                  Video: <input type="file" name="video" class="image" id="upload_video"/>
                                  Thumbnail: <input type="file" name="thumbnail" class="image" id="upload_image"/>
                                  <button type="submit" class="btn btn-primary" id="upload">
                                    <span id="buttonText">Save</span>
                                    <span class="spinner-border spinner-border-sm loader" id="loader" role="status" aria-hidden="true" style="display: none;"></span><span class="loader" style="display: none">  Uploading...</span>
                                  </button>
                                </label>
                            </form>
                            <h1>Video:</h1>
                            <video controls>
                              <source src="{{ $landingPageVideo ?? '' }}" type="video/mp4" id="video">
                            </video>
                            <h1>Thumbnail:</h1>
                            <img src="{{ $landingPageVideoThumbnail}}" id="thumbnail">
                        </div>
                    </div>
                </div>
            </div>
    </x-slot>
    <x-slot name="js">
        <script>
            $(document).ready(function() {
              $('#formxx').submit(function(e){
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                e.preventDefault();
                var formdata = new FormData(this)

                $.ajax({
                  url:'{{ route("admin.staticData.uploadLandingPageVideo.post") }}',
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
                    $('#video').attr('src', data.message.video_link)
                    $('#thumbnail').attr('src', data.message.thumbnail_link)
                    $('#buttonText').show();
                    $('.loader').hide();
                    window.location.reload();
                  }
                });
              });
          });

        </script>
    </x-slot>
</x-backend-layout>