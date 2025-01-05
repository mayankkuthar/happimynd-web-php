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
    User's Credit By Username
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      

      <div class="page-title">
          <div class="title_left">
            <h3 style="margin-bottom: 20px;">User's Credit By Username</h3>
          </div>
        </div>
            
          <div class="clearfix"></div>
          


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
                     <label for="reg_price" style="font-size: 17px;">Username:</label>
                     @if(count($list) > 0)
                      <input type="text" class="form-control" name="username" id="username" value="{{$list[0]->user->username}}" required/>
                     @else
                      <input type="text" class="form-control" name="username" id="username" value="" required/>
                     @endif
                     <div id="reg_price_vali" style="color:red;"></div>
                </div>


                <div class="form-group">
                  <button type="submit" class="btn btn-primary terms__update__btn">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </form>



                @if(count($list) > 0)
                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th> 
                          <th>Psychologist Name</th>
                          <th>Username</th>
                          <th>Total Credit</th>
                          <th>Remaining Credit</th>

                          <th>Notes</th>



                          <th style="text-align: center;">Action</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($list as $row)
                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>{{$row->psychologist->first_name ?? '-'}}</td> 
                          <td>{{$row->user->username ?? '-'}}</td> 
                          <td>{{$row->total_no_of_session ?? '-'}}</td> 
                          <td>{{$row->remaining_session ?? '-'}}</td> 

                          <td>{{$row->notes ?? '-'}}</td> 


                          <td style="text-align:center;">
                            <a href="{{url('admin/edit-users-credit') .'/'.$row->id}}" class="btn btn-primary">Edit Credit</a> 
                          </td> 

                          
                        </tr>
                        @endforeach

                      </tbody>
                    </table>
                  </div>
                @endif

    

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
