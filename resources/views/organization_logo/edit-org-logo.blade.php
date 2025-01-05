<style>
  .client-img {
    width: 75px;
    height: 75px;
  }
  .member-img {
    width: 200px;
    height: 200px;
  }
  .input_radio{
      margin-right: 5px;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    Edit Organization Logo
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      

      <div class="page-title">
          <div class="title_left">
            <h3>Edit WhiteLabelling</h3>
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


          <form action="" method="post" enctype="multipart/form-data">
            @csrf

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

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">


                  <div class="form-group">
                       <label for="reg_price" style="font-size: 20px;">Name:</label>
                       <input type="text" class="form-control" name="name" id="name" value="{{$org_detail->name ??''}}" disabled required/>
                       <div id="reg_price_vali" style="color:red;"></div>
                  </div>



                  <div class="form-group">
                       <label for="reg_price" style="font-size: 20px;">Main Logo:</label>
                        <br>
                        <div style="display: flex;">
                          <input type="radio" id="html" name="main_logo" value="1" class="input_radio" @if($org_detail->main_logo == '1') checked @endif>
                          <label for="html" style="margin-right: 20px;">ON</label><br>
                          <input type="radio" id="css" name="main_logo" value="0" class="input_radio" @if($org_detail->main_logo == '0') checked @endif>
                          <label for="css">OFF</label><br>
                        </div>
                  </div>


                  <div class="form-group">
                       <label for="reg_price" style="font-size: 20px;">Powered By:</label>
                        <br>
                        <div style="display: flex;">
                          <input type="radio" id="html" name="powered_by" value="1" class="input_radio" @if($org_detail->powered_by == '1') checked @endif>
                          <label for="html" style="margin-right: 20px;">ON</label><br>
                          <input type="radio" id="css" name="powered_by" value="0" class="input_radio" @if($org_detail->powered_by == '0') checked @endif>
                          <label for="css">OFF</label><br>
                        </div>
                  </div>



                  <div class="form-group">
                    @if($org_detail->organization_logo)
                      <label for="Image" style="font-size: 20px;">previous Image</label>
                      <img class="member-img" src="{{$org_detail->organization_logo}}" alt="{{$org_detail->name}}">
                    @endif
                      <label for="Image" style="font-size: 20px;">Image</label>
                      <input type="file" class="form-control" name="image" id="image"/>
                      <div id="discount_vali" style="color:red;"></div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary terms__update__btn">Edit</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
    

    </div>
    <x-slot name="js">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
    </x-slot>
  </x-slot>
</x-backend-layout>
<style type="text/css">
.ui-sortable tr {
	cursor:pointer;
}
.ui-sortable tr:hover {
	background:rgba(244,251,17,0.45);
}
</style>
