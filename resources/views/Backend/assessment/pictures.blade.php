<x-backend-layout>
  <x-slot name="title">
    Rating Pictures
  </x-slot>
  <x-slot name="content">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Add/Delete Pictures</h3>
          </div>
          <div class="title_right">

          </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Upload Rating picture
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadPicture">+</button>
                </h2>
                <!-- modal for picture upload -->
                <div class="modal fade" id="uploadPicture" tabindex="-1" role="dialog" aria-labelledby="uploadPictureLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="uploadPictureLabel">Upload Picture</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="x_content">
                          <p>Drag multiple files to the box below for multi upload or click to select files. This is for demonstration purposes only, the files are not uploaded to any server.</p>
                          <form action="{{ route('admin.scoreRatingPictureUpload.post') }}" class="dropzone" id="campaign-form" method="POST"  enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="file" name="file" multiple>
                            <br />
                            <br />
                            <br />
                            <br />
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">

                      </div>
                    </div>
                  </div>
                </div>
                <!-- modal for picture upload -->
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="flash-message">
                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                  @if(Session::has( $msg))
                  <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
                  @endif
                  @endforeach
                </div>
                <div class="x_content">

                  <div class="row">
                    @foreach($pictures as $picture)
                    <div class="col-md-40">
                      <div class="thumbnail">
                        <div class="image view view-first">
                          <img style="width: 150px; display: block; height: auto;" src="{{ $picture->image }}" alt="image">
                        </div>
                        <div class="caption">
                          <a href="{{ route('admin.deleteRatingImage.get',['id' => $picture->id]) }}">Delete   <i class="fa fa-times"></i></a>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </x-slot>
  <x-slot name="js">
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>


    <script>
      $(document).ready(function(){
        $('#uploadPicture').on('hidden.bs.modal', function(){
          location.reload();
        })
      })
      FilePond.registerPlugin(

      // encodes the file as base64 data
      FilePondPluginFileEncode,

      // validates the size of the file
      FilePondPluginFileValidateSize,

      // corrects mobile image orientation
      FilePondPluginImageExifOrientation,

      // previews dropped images
      FilePondPluginImagePreview,
      FilePondPluginFileValidateType,
      );
      FilePond.setOptions({
        acceptedFileTypes: ['image/png','image/jpeg','image/jpg'],
        allowDrop: false,
        allowReplace: false,
        instantUpload: false,
        allowImagePreview: true,
        server: {
          url: "{{ route('admin.scoreRatingPictureUpload.post') }}",
          headers: {'X-CSRF-TOKEN': csrf_token},
        }
      });
      const inputElement = document.querySelector('input[type="file"]');
      const pond = FilePond.create( inputElement );
    </script>
  </x-slot>
</x-backend-layout>
