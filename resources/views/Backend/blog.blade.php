@php  
use Illuminate\Support\Str;
@endphp

<style type="text/css">
  .col-sm-12.col-md-5 {
    display: none;
}
.col-sm-12.col-md-7 {
    display: none;
}
</style>
<x-backend-layout>
  <x-slot name="title">
    Create User
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="p-5">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @elseif((session('error')))
        <div class="alert alert-danger alert-dismissible fade show terms__addtitle" role="alert">
          {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
      </div>
          <form method="POST" action="{{ route('admin.staticData.blogFormSave') }}" id="term" enctype="multipart/form-data">
          @csrf
            <input type="hidden" name="id" value="">
            <h1 class="terms__addtitle">Title:</h1>
            <input class="terms__input_field @error('title') is-invalid @enderror" type="text" name="title" value="{{ old('title') ?? '' }}">
            @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="form-group">
              <label for="" class="font-bold">Thumbnail :</label>
              <input type="file" name="thumbnail" id="" placeholder = "thumbnail" class="@error('thumbnail') is-invalid @enderror" value="{{ old('thumbnail') ??  ''}}">
              @error('thumbnail')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <span class="text-danger mb-4 py-4">Image size 400 X 300</span>
            <h1 class="terms__addcontent mt-4">Content:</h1>
            <div>
              <textarea  id="Content" name="content" class="@error('content') is-invalid @enderror">
                {{ old('content') ?? '' }}
              </textarea>
            </div>
            @error('content')
              <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            @if (isset($post))
                @if ($post->post_category_id == 1)
                    <div class="profile__app-download__content">
                      <img src="{{ $post->getContentWithS3Url('blog') }}">
                    </div>
                @elseif($post->post_category_id == 2)
                <video width="400" poster="" controls preload="none">
                  <source src="{{ $introVideoLink ?? '' }}" type="video/mp4">
                </video>
                @else 
                <div>show audio</div>
                @endif
            @endif
            <div class="form-group mt-4">
              <label for="" class="font-bold">Media : </label>
              <input type="file" name="media" id="" class="@error('media') is-invalid @enderror" value="{{ $post->media ?? '' }}">
            </div>
            @error('media')
              <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="">
              <h1 class="terms__addtitle">Restrict Content</h1>
              <div class="form-group @error('accessibility') is-invalid @enderror">
                @if (isset($post))             
                  @if ($post->restricted_content )
                  <input type="radio" value=1 name="accessibility" checked>
                  <label for="" class="form-check-label mr-3">Paid</label>
                  <input type="radio" value=0 name="accessibility">
                  <label for="" class="form-check-label mr-3">Free</label>
                  @else
                  <input type="radio" value=1 name="accessibility">
                  <label for="" class="form-check-label mr-3">Paid</label>
                  <input type="radio" value=0 name="accessibility" checked>
                  <label for="" class="form-check-label mr-3">Free</label>
                  @endif
                @else
                <input type="radio" value=1 name="accessibility">
                <label for="" class="form-check-label mr-3">Paid</label>
                <input type="radio" value=0 name="accessibility" checked>
                <label for="" class="form-check-label mr-3">Free</label>
                @endif
              </div>
              @error('accessibility')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
              
            </div>
            <div class="">
              <h1 class="terms__addtitle">Publish</h1>
              <div class="form-group @error('publish_status') is-invalid @enderror">
                @if (isset($post))
                    
                  @if ($post->publish_status)
                  <input type="radio" value=1 name="publish_status" checked>
                  <label for="" class="form-check-label mr-3">Yes</label>
                  <input type="radio" value=0 name="publish_status">
                  <label for="" class="form-check-label mr-3">No</label>
                  @else
                  <input type="radio" value=1 name="publish_status">
                  <label for="" class="form-check-label mr-3">Yes</label>
                  <input type="radio" value=0 name="publish_status" checked>
                  <label for="" class="form-check-label mr-3">No</label>
                  @endif
                @else
                <input type="radio" value=1 name="publish_status" checked>
                <label for="" class="form-check-label mr-3">Yes</label>
                <input type="radio" value=0 name="publish_status" >
                <label for="" class="form-check-label mr-3">No</label>
                @endif

              </div>
              @error('publish_status')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>

          <div class="form-group ">
            @if (isset($post))
              @if ($post->featured)
              <label for="package" class="form-check-label mr-3">Featured</label>
              <input type="checkbox" name="featured" id="featured" checked>
              @else
              <label for="package" class="form-check-label mr-3">Featured</label>
              <input type="checkbox" name="featured" id="featured">
              @endif
            @else
              <label for="package" class="form-check-label mr-3">Featured</label>
              <input type="checkbox" name="featured" id="featured">
            @endif

        </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary terms__update__btn">Save</button>
            </div>
          </form>

      <div class="new-section">
        @isset($posts)
          <div class="col">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                      Blogs
                  </p>
                  <div class="x_content">
                  <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>content</th>
                        <th>Media</th>
                        <th>Published</th>
                        <th>Featured</th>
                        <th>Restricted</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{!! Str::words($post->description, 10) !!}</td>
                        <td>{{ $post->media }}</td>
                        <td>{{ $post->publish_status == 1 ? 'Published':'Draft' }}</td>
                        <td>{{ $post->featured == 1 ? 'True':'False' }}</td>
                        <td>{{ $post->restricted_content == 1 ? 'True':'False' }}</td>
                        <td>
                            <a href="{{ route('admin.staticData.blogFormEdit',['slug'=>$post->slug]) }}"><i class="fa fa-edit"></i> </span></a>
                            <a href="{{ route('admin.staticData.blogFormDelete',['id'=>$post->id]) }}"><i class="fa fa-trash-o"></i></a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <x-pagination-dropdown :paginator="$posts" />
                </div>
                </div>
              </div>
            </div>
          </div>
        @endisset
      </div>

    </div>
    <x-slot name="js">
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
      <script src=""></script>


      <script type="text/javascript">

        function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }
        initializeCKEditor("Content")
        function addInputFields() {
          var id = 'a'+Math.random().toString(36).substring(7);
          $('<form>', { action: "{{ route('admin.staticData.post') }}", method: 'POST' }).append(
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
