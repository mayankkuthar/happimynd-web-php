<x-backend-layout>
  <x-slot name="title">
    Create User
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <br />
      <h3 align="center">Upload Terms and Services</h3>
      <br />
      @if($termServicesContents)
        @foreach($termServicesContents->content as $termService)
          <form method="POST" action="{{ route('admin.staticData.saveTerms') }}" id="term-{{ $termService->id }}">
          @csrf
            <input type="hidden" name="id" value="{{ $termService->id }}">
            <h1 class="terms__addtitle">Title:</h1>
            <input class="terms__input_field" type="text" name="title" value="{{ $termService->title }}">
            <h1 class="terms__addcontent">Content:</h1>
            <textarea  id="Content{{ $termService->id }}" name="content">{{ $termService->content }}</textarea>

            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
            <button type="button" class="btn btn-primary terms__update__btn" onclick="deleteContent({{ $termService->id }})">Delete</button>
          </form>
          @endforeach
      @endif
      <div class="new-section">

      </div>
        <div class="terms__addtitle-content__btn">
          <button type="button" class="btn btn-success" onclick="addInputFields();this.disabled=true;">
            Add new title and content
          </button>
        </div>
    </div>
    <x-slot name="js">
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script type="text/javascript">
        function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }
        @if($termServicesContents)
          @foreach($termServicesContents->content as $termService)
            initializeCKEditor("Content{{ $termService->id }}")
          @endforeach
        @endif

        function addInputFields() {
          var id = 'a'+Math.random().toString(36).substring(7);
          $('<form>', { action: "{{ route('admin.staticData.saveTerms') }}", method: 'POST' }).append(
              $('<input>', {type: 'hidden', id: 'id', name: 'id', value: "new"}),
              $('<input>', {type: 'hidden', name: '_token', value: '{{ csrf_token() }}'}),
              $('<h1>', {class: 'terms__addtitle', }).text('Title:'),
              $('<input>', {class: 'terms__input_field',type: 'text', name: 'title'}),
              $('<h1 class="terms__addcontent">Content:</h1>'),
              $('<textarea>', {class: 'ckeditor', name: 'content', id: id}),
              $('<button>', {class: 'btn btn-primary terms__update__btn'}).text('Add')
          ).appendTo('.new-section');
          initializeCKEditor(id);
        }

        function deleteContent(id) {
          $.post(
            {
              url: "{{ route('admin.staticData.deleteContent') }}",
              data: {"id": id, "_token": "{{ csrf_token() }}" },
              success: function(data){
                if(data.message == 'true'){
                  $('#term-'+id).remove();
                }
              }
            }
          )
        }
      </script>
    </x-slot>
  </x-slot>
</x-backend-layout>
