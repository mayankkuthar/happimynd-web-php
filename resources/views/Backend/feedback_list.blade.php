<x-backend-layout>
  <x-slot name="title">
    Feedback List
  </x-slot>
  <x-slot name="css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
  </x-slot>
  <x-slot name="js">
      <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
  </x-slot>



<x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="x_panel">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                    Feedback List
                  </p>

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
                  

                  

                  <div class="x_content">

                    <div class="excel-button">
                      <form action="{{route('admin.downloadFeedbackListxl')}}">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" >

                        <button type="submit" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
                          <span id="buttonText">Get Excel</span>
                        </button>
                      </form>
                  </div>



                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th style=" width: 40px;">S. No</th>
                          <th style=" width: 90px;">User Name</th>
                          <th style=" width: 90px;">B2B/B2C</th>
                          <th style=" width: 90px;">Organization</th>
                          <th style=" width: 90px;">Emoji</th>
                          <th style=" width: 90px;">Emoji Name</th>
                          <th style=" width: 150px;">Date & Time</th> 
                          <th>Additional Message</th> 

                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        
                        @foreach($list as $row)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{$row->user->username ?? ''}}</td>
                          <td>
                            @if($row->user->isOrganizationUser()) B2B @else B2C @endif
                          </td>
                          <td>
                            {{ ($row->user->isOrganizationUser())? $row->user->userToken->token->organization()->withTrashed()->first()->name : 'Individual' }}
                          </td>
                          <td> 
                            <img src="{{$row->applicationRateEmoji->image}}"> 
                          </td>
                          <td>{{$row->applicationRateEmoji->name}}</td>
                          <td>{{ $row->created_at}}</td> 
                          <td>{{ $row->feedback_message}}</td> 

 
                        </tr>
                        @endforeach

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>
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



</x-backend-layout>
