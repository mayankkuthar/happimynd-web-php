<x-backend-layout>
  <x-slot name="title">
    Edit Reward Points
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Edit Reward Points</h3>
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
                    <label>Points</label>
                    <input type="number" name="points_to_be_given" min="0" max="1000" value="{{$data->points_to_be_given}}" class="form-control">

                    <br>

                    <button type="submit" id="submit" class="btn btn-primary">Update</button>
              </div>



            </div>
          </div>
        </form>
        </div>
      </div>
  </x-slot>
  <x-slot name="js">
  </x-slot>
</x-backend-layout>




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js" integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/additional-methods.min.js" integrity="sha256-vb+6VObiUIaoRuSusdLRWtXs/ewuz62LgVXg2f1ZXGo=" crossorigin="anonymous"></script>




