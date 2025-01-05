<x-backend-layout>
  <x-slot name="title">
    Import HappiSelf Library
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Import HappiSelf Library</h3>
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
          <form method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                    <input type="file" name="import_happiself_library" class="form-control" style="height:50px;padding-top: 9px;">
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
<script>
  var msg = '{{Session::get('alert')}}';
  var exist = '{{Session::has('alert')}}';
  if(exist){
    alert(msg);
  }
</script>