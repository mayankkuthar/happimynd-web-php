<x-backend-layout>
    <x-slot name="title">
        HappiGUIDE Notes List
    </x-slot>
    <x-slot name="css">
        <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
        <link href="{{ asset('assets/Backend/css/plugins/nprogress.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/buttons.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/responsive.bootstrap.min.css') }}" rel="stylesheet">
    </x-slot>
    <x-slot name="js">
        <script src="{{ asset('assets/Backend/js/plugins/fastclick.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/nprogress.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/icheck.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/responsive.bootstrap.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/Backend/js/plugins/vfs_fonts.js') }}"></script>
    </x-slot>
    <x-slot name="content">
        <div class="right_col" role="main">
        <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>HappiGUIDE Notes List</h2>
                <div class="clearfix"></div>

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
              <div class="x_content">
                  <div class="row">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive custom-change-table">

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Username</th>
                      <th>Psychologist Name</th>
                      <th>Date</th>
                      <th>case_history</th>
                      <th>time</th>
                      <th>duration</th>
                      <th>name_of_therapist</th>
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
                      @foreach($guide_notes as $rows)

                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $rows->guideSessionDetail->userDetail->username ?? '' }}</td>
                        <td>{{ $rows->guideSessionDetail->psychologistDetail->first_name ??'-'}}</td>
                        <td>{{ $rows->guideSessionDetail->date ??'-'}}</td>
                        <td>{{ $rows->case_history ??'-'}}</td>
                        <td>{{ $rows->time ??'-'}}</td>
                        <td>{{ $rows->duration ??'-'}}</td>
                        <td>{{ $rows->name_of_therapist ??'-'}}</td>
                        <td>{{ $rows->occupation ??'-'}}</td>
                        <td>{{ $rows->qualification ??'-'}}</td>
                        <td>{{ $rows->presenting_complaints ??'-'}}</td>
                        <td>{{ $rows->past_psychology_history ??'-'}}</td>
                        <td>{{ $rows->medical_history ??'-'}}</td>
                        <td>{{ $rows->family_psychological_histroy ??'-'}}</td>
                        <td>{{ $rows->session_summary ??'-'}}</td>
                        <td>{{ $rows->diagnosis ??'-'}}</td>
                        <td>{{ $rows->plan_for_therpy_treatment ??'-'}}</td>
                      </tr>

                      @endforeach
                  </tbody>
                  
                </table>

              </div>
            </div>
          </div>
            </div>
    </x-slot>
</x-backend-layout>
