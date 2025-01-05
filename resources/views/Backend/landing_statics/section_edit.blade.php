<style>
  .file-dis {
    display: block;
    margin-top: 20px;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    edit landing cms
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    
    <div class="right_col" role="main">
      <br />
      <h3 align="center">edit landing cms</h3>
      <br />
        <form method="POST" action="{{ route('admin.staticData.saveContent') }}" id="term", enctype="multipart/form-data">
          @csrf
            <input type="hidden" name="id" value="{{$section->id}}">
            <input type="hidden" name="section" value="{{$content_section}}">
            <h1 class="terms__addtitle">Title:</h1>

            <input class="terms__input_field" type="text" name="title" value="{{$section->title}}">
            <h1 class="terms__addcontent">Content:</h1>
            <textarea  id="Content" name="content">{!! $section->content !!}</textarea>
            <input  type="file" name="image" class="file-dis">
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
      <div class="new-section">

    <x-slot name="js">
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script type="text/javascript">
        function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }
      
            initializeCKEditor("Content")
       

      </script>
    </x-slot>
  </x-slot>
</x-backend-layout>
