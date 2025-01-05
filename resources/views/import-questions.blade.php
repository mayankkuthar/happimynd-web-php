


<!-- <form id="form-validate" method="POST" enctype="multipart/form-data">
	{{csrf_field()}}
	<label>Import question file</label>
	<br>
	<input type="text" name="demo" class="form-control">
	<br>
    <input type="file" name="import_question" class="form-control">
    <br>
	<input type="submit">
</form> -->


<x-backend-layout>
  <x-slot name="title">
    Create User Profile
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Import Questions</h3>
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
          

          <form id="form-validate" method="POST" enctype="multipart/form-data">
			     {{csrf_field()}}

          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
      			    <input type="file" name="import_question" class="form-control" style="height:50px;padding-top: 9px;">
        				<br>
        				<button type="submit" class="btn btn-primary">Submit</button>

              
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
