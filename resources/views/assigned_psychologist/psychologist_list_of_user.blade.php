<x-backend-layout>
  <x-slot name="title">
    Psychologist List of User
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Psychologist List of User</h3>
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
          
 
			     <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th>
                          <th>Psychologist Name</th>
                          <th>Psychologist Email</th>
                          <th>Language</th>
                          <th>Date & TIme</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($psychologists as $row)
                        <tr>
                          <td>{{++$i}}</td>
                          <td>{{$row->psychologist->username ?? 'N/A'}}</td>
                          <td>{{$row->psychologist->email  ?? 'N/A'}}</td>
                          <td>{{$row->language  ?? 'N/A'}}</td>
                          <td>{{$row->assigned_date_time  ?? 'N/A'}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
              
        </div>
      </div>

  </x-slot>
  <x-slot name="js">
    

  </x-slot>
</x-backend-layout>
