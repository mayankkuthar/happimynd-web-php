<x-backend-layout>
  <x-slot name="title">
    Create Explore Services
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    
    <div class="right_col" role="main">
      <br />
      <h3 align="center">Upload Explore Services for User dashboard</h3>
      <br />
      @if($exploreServiceContent)
        @foreach($exploreServiceContent->content as $services)
          <form action="{{ route('admin.staticData.saveExploreServices') }}" method="post" enctype="multipart/form-data" id="term-{{ $services->id }}">
          @csrf
            <input type="hidden" name="id" value="{{ $services->id }}">
            <input type="hidden" name="id1" value="{{ $data[$loop->iteration - 1]->id }}">
            <h1 class="terms__addtitle">Title:{{ $data[$loop->iteration - 1]->title }}</h1>
            <label for="overview">Overview:</label>
            <input type="text" name="overview" id="overview" value="{{ $data[$loop->iteration - 1]->overview }}"></br></br>
            <label for="Title">Title:</label>
            <input type="text" name="title1" id="title1" value="{{ $data[$loop->iteration - 1]->title }}"></br></br>
            <input class="terms__input_field" type="text" name="title" value="{{ $services->title }}">
            <h1 class="terms__addcontent">Content:</h1>
            <textarea  id="Content{{ $services->id }}" name="content">{{ $services->content }}</textarea>
            <label for="image">Image:</label>
            <div class="icons">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-3">
               <div class="organisation__howcanwe__content__img">
                  <img src="{{$data[$loop->iteration - 1]->getImageWithS3Url('services')}}">
               </div>
              </div>
            </div>
            <div class="form-group">
                 <input type="file" class="form-control" name="image" id="image"/>
            </div>

            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
          @endforeach
      @endif
      @if($button_contents)
        @foreach($button_contents as $button_content)
          <form method="POST" action="{{ route('admin.staticData.saveEditableServicesButton') }}" >
          @csrf
            <input type="hidden" name="id" value="{{ $button_content->id }}">
            <h1 class="terms__addtitle">Title:{{ $button_content->button_content }}</h1>
            <input class="terms__input_field" type="text" name="button_content" value="{{ $button_content->button_content}}">
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
          @endforeach
      @endif
      <h2>Bundle Names</h2>
      @if($packages)
        @foreach($packages as $package)
          <form method="POST" action="{{ route('admin.staticData.savePackageName') }}" >
          @csrf
            <input type="hidden" name="id" value="{{ $package->id }}">
            <h1 class="terms__addtitle">Title:{{ $package->name }}</h1>
            <input class="terms__input_field" type="text" name="package_name" value="{{ $package->name}}">
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
          @endforeach
      @endif
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
        @if($exploreServiceContent)
          @foreach($exploreServiceContent->content as $services)
            initializeCKEditor("Content{{ $services->id }}")
          @endforeach
        @endif

        function addInputFields() {
          var id = 'a'+Math.random().toString(36).substring(7);
          $('<form>', { action: "{{ route('admin.staticData.saveExploreServices') }}", method: 'POST' }).append(
              $('<input>', {type: 'hidden', id: 'id', name: 'id', value: "new"}),
              $('<input>', {type: 'hidden', name: '_token', value: '{{ csrf_token() }}'}),
              $('<h1>', {class: 'terms__addtitle', }).text('Question:'),
              $('<input>', {class: 'terms__input_field',type: 'text', name: 'title'}),
              $('<h1 class="terms__addcontent">Answer:</h1>'),
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
