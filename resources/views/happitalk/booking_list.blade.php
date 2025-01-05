<x-backend-layout>
   <x-slot name="title">
      HappiTalk Booking List
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
                        HappiTalk Booking List
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
                           <form action="{{route('admin.downloadtalkListxl')}}">
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
                           <div class="row">
                              <div class="col-sm-12">
                                 <div class="card-box table-responsive custom-change-table">
                                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                                       <thead>
                                          <tr>
                                             <th>S.NO</th>
                                             <th>Psychologist Name</th>
                                             <th>User Name</th>
                                             <th>B2B/B2C</th>
                                             <th>Organization</th>
                                             <th>Date</th>
                                             <th>Time</th>
                                             <th>Status</th>
                                             <th>Psy joined Time</th>
                                             <th>Psy leave Time</th>

                                             <th>User's Feedback Emoji Name</th>
                                             <th>User's Feedback Reason</th>

                                             <th>Session Status</th>

                                             <th>Recording Permission</th>



                                             <th style="text-align: center;">Action</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <?php  
                                             $i=0;
                                             ?>
                                          @foreach($booking as $row)
                                          <tr>
                                             <td>{{ ++$i }}</td>
                                             <td>{{ $row->psychologistDetail->first_name }} {{ $row->psychologistDetail->last__name }}</td>
                                             <td>{{ $row->userDetail->username }}</td>
                                             <td>
                                                @if($row->userDetail->isOrganizationUser()) B2B @else B2C @endif
                                             </td>
                                             <td>
                                                {{ ($row->userDetail->isOrganizationUser())? $row->userDetail->userToken->token->organization()->withTrashed()->first()->name : 'Individual' }}
                                             </td>
                                             <td>{{ $row->date }}</td>
                                             <td>{{ $row->time }}</td>
                                             <?php
                                                if($row->is_cancel == 1){
                                                    $status = 'Session cancel by'.$row->cancel_by;
                                                }
                                                elseif($row->is_req_accepted == 0){
                                                    $status = 'Request pending by psychologist';
                                                }
                                                elseif($row->is_req_accepted == 2){
                                                    $status = 'Request rejected by psychologist';
                                                }
                                                elseif($row->is_req_accepted == 1){
                                                  if($row->is_cancel == 1){
                                                    $status = 'Session cancel by '.$row->cancel_by;
                                                  }
                                                  elseif($row->is_cancel == 0 && $row->is_end == 0){
                                                    $status = 'Request accepted by psychologist';
                                                  }
                                                  elseif($row->is_cancel == 0 && $row->is_end == 1){
                                                      $status = 'Session completed';
                                                  } 
                                                }
                                                ?>
                                             <td>{{ $status ?? ''}}</td>
                                             <td>{{$row->psy_joined_time ?? '-'}}</td>
                                             <td>{{$row->psy_leave_time ?? '-'}}</td>

                                             <td>{{$row->userOpinion->Emoji->name ?? '-'}}</td>
                                             <td>{{$row->userOpinion->reason ?? '-'}}</td>

                                             <td>{{$row->psychologistOpinion->session_status ?? '-'}}</td>

                                             <td>{{ $row->user_recording_permission == "0" ? 'No' : 'Yes' }}</td>

                                             <td style="text-align: center;">
                                                <a href="{{url('admin/talk-notes-detail').'/'.$row->id}}" class="btn btn-primary">Notes & Opinion</a>
                                             </td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                           {{$booking->links()}}
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