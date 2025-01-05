<x-backend-layout>
  <x-slot name="title">
    Create User
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <br />
      <h3 align="center">Upload FAQ General</h3>
      <br />
      @if($faqs)
        @foreach($faqs->content as $faq)
          <form method="POST" action="{{ route('admin.staticData.landingFaqPost') }}" id="term-{{ $faq->id }}">
          @csrf
            <input type="hidden" name="id" value="{{ $faq->id }}">
            <h1 class="terms__addtitle">Question:</h1>
            <input class="terms__input_field" type="text" name="title" value="{{ $faq->title }}">
            <h1 class="terms__addcontent">Answer:</h1>
            <textarea  id="Content{{ $faq->id }}" name="content">{{ $faq->content }}</textarea>

            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
            <button type="button" class="btn btn-primary terms__update__btn" onclick="deleteContent({{ $faq->id }})">Delete</button>
          </form>
          @endforeach
      @endif
      <div class="new-section">

      </div>
        <div class="terms__addtitle-content__btn">
          <button type="button" class="btn btn-success" onclick="addInputFields();this.disabled=true;">
            Add new Question and Answers
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
        @if($faqs)
          @foreach($faqs->content as $faq)
            initializeCKEditor("Content{{ $faq->id }}")
          @endforeach
        @endif

        function addInputFields() {

          var id = 'a'+Math.random().toString(36).substring(7);
          $('<form>', { action: "{{ route('admin.staticData.landingFaqPost') }}", method: 'POST' }).append(
              $('<input>', {type: 'hidden', id: 'id', name: 'id', value: "new"}),
              $('<input>', {type: 'hidden', name: '_token', value: '{{ csrf_token() }}'}),
              $('<h1>', {class: 'terms__addtitle', }).text('Question:'),
              $('<input>', {class: 'terms__input_field',type: 'text', name: 'title'}),

              $('<h1>', {class: 'terms__addtitle', }).text('Language:'),
              $('<select>', {class: 'terms__input_field', id:'select-lang' , name: 'language'},
                // $('<option>'English'</option>'),
                '<select>'),


              $('<h1 class="terms__addcontent">Answer:</h1>'),
              $('<textarea>', {class: 'ckeditor', name: 'content', id: id}),
              $('<button>', {class: 'btn btn-primary terms__update__btn'}).text('Add')
          ).appendTo('.new-section');

          var user_language = <?php echo $user_language; ?>;
          console.log('user_language',user_language);
          let option = "";
          for(i=0; user_language.length > i; i++){
              let cat_name = user_language[i]['name'];
              option += `<option value="`+user_language[i]['id']+`">`+cat_name+`</option>`;
          }
          $('#select-lang').append(option);

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
