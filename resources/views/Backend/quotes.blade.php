@php  
use Illuminate\Support\Str;
@endphp
<x-backend-layout>
  <x-slot name="title">
    OurTeam
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
          <form action="{{ route('admin.staticData.quotesFormSave') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$quotes->id ??''}}" >
            <input type="hidden" name="quote" value="{{'$quote->quote'}}" >
            <label for="image">Image:</label>
            <div class="icons">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-3">
               <div class="organisation__howcanwe__content__img">
                  <img src="{{$quotes->getImageWithS3Url('quotes')}}">
               </div>
              </div>
            </div>
            <div class="form-group">
                 <input type="file" class="form-control" name="image" id="image"/>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary terms__update__btn" onclick="return validation(quote)">Update</button>
            </div>
            
          </form>
          @if($button_contents)
          <form method="POST" action="{{ route('admin.staticData.saveEditableQuoteButton') }}" >
          @csrf
            <input type="hidden" name="id" value="{{ $button_contents->id }}">
            <h1 class="terms__addtitle">Title:{{ $button_contents->button_content }}</h1>
            <input class="terms__input_field" type="text" name="button_content" value="{{ $button_contents->button_content}}">
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
      @endif
            
      </div>

    </div>
    <x-slot name="js">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
      <script type="text/javascript">
         function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }
        initializeCKEditor("quote");

        function validation(quote){
            if(quote.value.length>300){
              alert("Quote must be of length less that 300 character");
              return(false);
            }
        }
        </script>

    </x-slot>
  </x-slot>
</x-backend-layout>
