<x-backend-layout>
  <x-slot name="title">
    HappiBUDDY List
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>HappiBUDDY List</h3>
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
            



              <div class="excel-button">
                 <form action="{{route('admin.downloadBuddyListxl')}}">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" >
                    <button type="submit" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
                    <span id="buttonText">Get Excel</span>
                    </button>
                 </form>
              </div>


 
              <div class="x_content">
                <div class="card-box table-responsive custom-change-table">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th>
                          <th>Username</th>
                          <th>Email</th>
                          <!-- <th>Profile</th> -->
                          <th>Gender</th>

                          <th>B2B/B2C</th>
                          <th>Organization</th>

                          <th style="text-align: center;">Action</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($data as $row)
                        <tr>
                          <td>{{++ $i}}</td>
                          <td>{{$row->user->username ?? 'N/A'}}</td>
                          <td>{{$row->user->email  ?? 'N/A'}}</td>
                          <!-- <td>{{ucfirst($row->user->profileType->name  ?? 'N/A')}}</td> -->
                          <td>{{ucfirst($row->user->gender  ?? 'N/A')}}</td>

                          <td>
                              @if($row->user && $row->user->isOrganizationUser()) B2B @else B2C @endif
                          </td>
                          <td>
                              {{ ($row->user && $row->user->isOrganizationUser())? $row->user->userToken->token->organization()->withTrashed()->first()->name : 'Individual' }}
                          </td>

                          <td style="text-align: center;">
                            <a href="{{url('admin/psy-list-based-on-user').'/'.$row->user_id}} " class="btn btn-primary">View Psychologists</a>
                            <a href="{{url('admin/change-buddy-psy').'/'.$row->user_id}} " class="btn btn-primary">Change Psychologist</a>

                            <a href="{{url('admin/monthly-report-of-buddy-user').'/'.$row->user_id}} " class="btn btn-primary">Monthly Report</a>


                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  </div>

                  {{$data->links()}}
 
        </div>
      </div>



      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
      <script>
         function validateDate(start,end){
           const _MS_PER_DAY = 1000 * 60 * 60 * 24;
           const start_date = new Date('"'+start.value+'"'),
           end_date = new Date('"'+end.value+'"');
           const utc1 = Date.UTC(start_date.getFullYear(), start_date.getMonth(), start_date.getDate());
           const utc2 = Date.UTC(end_date.getFullYear(), end_date.getMonth(), end_date.getDate());
           if(Math.floor((utc2 - utc1) / _MS_PER_DAY)>31){
               alert("End Date must be atmost 30 days from start date");
               return(false);
             }     
         }
      </script>



  </x-slot>
  <x-slot name="js">
    

  </x-slot>
</x-backend-layout>
