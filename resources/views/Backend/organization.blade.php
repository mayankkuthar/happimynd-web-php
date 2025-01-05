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
        @foreach($data as $member)
            <h2>{{$member->name}}</h2>
          <form action="{{ route('admin.staticData.organizationFormSave') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$member->id ??''}}" >
            <div class="form-group">
                 <label for="title">Title:</label>
                 <input type="text" class="form-control" name="title" id="title" value="{{$member->title ??''}}" required/>
            </div>
            @if($member->id>1)
            <div class="note">
            <p style="font-size:150%;color:red;">
                Note:Please start a new point with (*).
            </p>
           </div>
           @endif
            <div class="form-group">
                 <label for="description">Description</label>
                 <textarea rows="8" cols="50" class="form-control" name="description" id="description{{$member->id}}">{{$member->description ??''}}</textarea>    
            </div>
            <label for="image">Image:</label>
            <div class="icons">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-3">
               <div class="organisation__howcanwe__content__img">
                  <img src="{{$member->getImageWithS3Url('org')}}">
               </div>
              </div>
            </div>
            <div class="form-group">
                 <input type="file" class="form-control" name="image" id="image"/>
            </div>
            <div class="form-group">
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
            </div>
          </form>
          @endforeach
          @foreach($logos as $logo)
          <form action="{{ route('admin.staticData.organizationLogoSave') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$logo->id ??''}}" >
            <label for="image">Image:</label>
            <div class="icons">
              <div data-aos="fade-up" data-aos-duration="1200" data-aos-once="true" class="col-lg-3">
               <div class="organisation__howcanwe__content__img">
                  <img src="{{$logo->getImageWithS3Url('org')}}">
               </div>
              </div>
            </div>
            <div class="form-group">
                 <input type="file" class="form-control" name="image" id="image" required/>
            </div>
            <div class="form-group">
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
            </div>
          </form>
          @endforeach
       @isset($organisation_buttons)
       @foreach($organisation_buttons as $organisation_button)
        <form method="POST" action="{{ route('admin.staticData.saveEditableOrganisationButton') }}" id="term", enctype="multipart/form-data">
          @csrf
            <input type="hidden" name="id" value="{{ $organisation_button->id }}">
            <h1 class="terms__addtitle">{{ $organisation_button->button_name }}</h1>
            <input class="terms__input_field" type="text" name="button_content" value="{{ $organisation_button->button_content }}">
            <br>
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
        @endforeach
      @endisset
          
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
        @foreach($data as $member)
        initializeCKEditor("description{{$member->id}}");
        @endforeach
        </script>

    </x-slot>
  </x-slot>
</x-backend-layout>

