
<style type="text/css">
  #message-error{
    color: red;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    TDS Pecentage
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>TDS Pecentage</h3>
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

                  <label>TDS Percentage (1-100%)</label>
                  <input type="text" class="form-control" value="{{$tds_Detail->tds_percentage}}" name="tds_percentage">
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
  </x-slot>
</x-backend-layout>





