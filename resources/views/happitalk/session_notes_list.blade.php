<x-backend-layout>
  <x-slot name="title">
    HappiTalk Summary
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


                  <p class="text-muted font-13 m-b-30">
                    HappiTalk Session Notes
                  </p>
                  
                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th> 
                          <th>case_history</th> 
                          <th>time</th> 
                          <th>duration</th> 
                          <th>name_of_therapist</th> 
                          <th>age</th> 
                          <th>gender</th> 
                          <th>occupation</th> 
                          <th>qualification</th> 
                          <th>presenting_complaints</th> 
                          <th>past_psychology_history</th> 
                          <th>medical_history</th> 
                          <th>family_psychological_histroy</th> 
                          <th>session_summary</th> 
                          <th>diagnosis</th> 
                          <th>plan_for_therpy_treatment</th> 


                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($psy_notes_for_user as $row)

                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>{{ $row->case_history }}</td> 
                          <td>{{ $row->time }}</td>  
                          <td>{{ $row->duration }}</td>  
                          <td>{{ $row->name_of_therapist }}</td>  
                          <td>{{ $row->age }}</td>  
                          <td>{{ $row->gender }}</td>  
                          <td>{{ $row->occupation }}</td>  
                          <td>{{ $row->qualification }}</td>  
                          <td>{{ $row->presenting_complaints }}</td>  
                          <td>{{ $row->past_psychology_history }}</td>  
                          <td>{{ $row->medical_history }}</td>  
                          <td>{{ $row->family_psychological_histroy }}</td>  
                          <td>{{ $row->session_summary }}</td>  
                          <td>{{ $row->diagnosis }}</td>  
                          <td>{{ $row->plan_for_therpy_treatment }}</td>  


                        </tr>
                        @endforeach

                      </tbody>
                    </table>
                  </div>


                  <br><br><br>

                  <p class="text-muted font-13 m-b-30">
                    HappiTalk Session Psychologist opinion
                  </p>

                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th> 
                           <th>Session status</th> 
                          <th>Presenting complaints</th> 
                          <th>Session summary</th> 
                          <th>Homework asigned</th> 
                          <th>Plan for next session / Remarks</th> 
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($psy_opinion as $row)

                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>{{ $row->session_status }}</td> 
                          <td>{{ $row->presenting_complaints }}</td>  
                          <td>{{ $row->session_summary }}</td>  
                          <td>{{ $row->hardword_asigned }}</td>  
                          <td>{{ $row->plan_for_next_session }}</td>  
                        </tr>
                        @endforeach

                      </tbody>
                    </table>
                  </div>





                  <p class="text-muted font-13 m-b-30">
                    HappiTalk Session User opinion
                  </p>

                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th> 
                          <th>Emoji</th> 
                          <th>reason</th> 
                          <th>additional_comment</th> 
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($user_opinion as $row)

                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>
                            <img src={{$row->emoji->image }}>
                          </td> 
                          <td>{{ $row->reason }}</td>  
                          <td>{{ $row->additional_comment }}</td>  
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
