<x-backend-layout>
  <x-slot name="title">
    Create Explore Services
  </x-slot>
  <x-slot name="content">
    <!-- page content -->

    <div class="right_col" role="main">
      <br />
      <h3 align="center">Edit Orientation Mail Content</h3>
      <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has( $msg))
        <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
        @endif
        @endforeach
      </div>
      <br />
      <form method="POST" action="{{ route('admin.staticData.saveOrientationEmail.post') }}" id="mail-form">
        @csrf
        <h1 class="terms__addtitle">Subject:</h1>
        <input class="form-control" type="text" name="subject" value="{{ $mailSubject }}">
        <h1 class="terms__addcontent">Content:</h1>
        <textarea  id="mail-body" name="body">{{ $mailBody }}</textarea>

        <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
        <button type="button" class="btn btn-primary terms__update__btn" onclick="showMailPreview()">preview</button>
      </form>

      <x-slot name="js">
        <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
        <script type="text/javascript">
          function showMailPreview() {
            $('#mail-body').html(editor.getData());
            window.open("{{ route('admin.staticData.orientationEmailPreview.get') }}?"+$('#mail-form').serialize(), "_blank");
          }
          let editor;
          function initializeCKEditor(id) {
            ClassicEditor.create( document.querySelector( '#'+id ) )
            .then( newEditor => {
              editor = newEditor;
            } )
            .catch( error => {
              console.error( error );
            } );
          }

          initializeCKEditor("mail-body")



        </script>
      </x-slot>
    </x-slot>
  </x-backend-layout>
