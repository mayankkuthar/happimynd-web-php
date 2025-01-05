<x-backend-layout>
  <x-slot name="title">
    HappiTalk Session List
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
                    HappiTalk Session List
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
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th> 
                          <th>Date</th> 
                          <th>Time</th>
                          <th>Status</th> 
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($sessions as $row)
                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>{{ $row->date }}</td> 
                          <td>{{ $row->time }}</td> 
                          <?php
                            if($row->is_cancel == 1){
                                $status = 'Session cancel '.$row->cancel_by;
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
  </x-slot>


</x-backend-layout>
