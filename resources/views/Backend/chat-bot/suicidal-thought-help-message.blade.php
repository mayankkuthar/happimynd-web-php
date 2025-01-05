
<style type="text/css">
  #message-error{
    color: red;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    Suicidal Thought: Help Message
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Suicidal Thought: Help Message</h3>
          </div>
        </div>

          <div class="clearfix"></div>
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
                @if(Session::has("error"))
                <div class="alert alert-danger">{{Session::get("error")}}</div>
                @endif
                @if(Session::has("success"))
                <div class="alert alert-success">{{Session::get("success")}}</div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger">
                      @foreach ($errors->all() as $error)
                      {{$error}}
                      @endforeach
                </div>
                @endif
            </div>
          </div>
          <form method="POST" id="validate-form" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">

                <div class="form-group" >
                  <label>Message</label>

                  <textarea rows="8" cols="50" name="description" id="editor">{{$suicidalThought->description ?? ''}}</textarea>

                  <br>

                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
              </div>




            </div>
          </div>
        </form>
        </div>
      </div>
  </x-slot>

  <x-slot name="js">
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script type="text/javascript">
        function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }

        initializeCKEditor("editor")

      </script>
    </x-slot>

</x-backend-layout>




<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js" integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/additional-methods.min.js" integrity="sha256-vb+6VObiUIaoRuSusdLRWtXs/ewuz62LgVXg2f1ZXGo=" crossorigin="anonymous"></script>

<script type="text/javascript">

  $(document).ready(function(){

    $("form[id = 'validate-form']").validate({
      rules:{
        message: {
            required:true,
        },
      },
      messages:{
        message:{
          required: 'Please enter message.',
        },
      },

      submitHandler: function(form) {
          $("#submit").attr("disabled", true);
          form.submit();

      }
    })
  })

</script>



<script>
  var msg = '{{Session::get('alert')}}';
  var exist = '{{Session::has('alert')}}';
  if(exist){
    alert(msg);
  }
</script>



<script>
  var today = new Date().toISOString().slice(0, 16);
  document.getElementsByName("date_time")[0].min = today;
</script>

 -->


