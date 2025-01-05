<x-backend-layout>


<x-slot name="title">
    Assessment Detail
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
  <div class="continer">
    <div class="row">
        <div class="col-sm-12">
            @if(isset($assessment))
            <div class="card-box table-responsive">
                <h3 class="text-muted text-center font-13 my-10">
                    Assessment Detail
                </h3>
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>Assessment ID</th>
                          <th>Username</th>
                          <th>Organization</th>
                          <th>Token</th>
                          <th>Report</th>
                          <th>CallTime</th>
                          <th>Slot</th>
                          <th>Assessment Start time</th>
                          <th>Assessment End Time </th>
                          <th># Question Attempt</th>
                          <th>Score data</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ $assessment->id }}</td>
                          <td>{{ $assessment->user->username }}</td>
                          <td>{{ $assessment->organization ?? '' }}</td>
                          <td>{{ $assessment->token ?? '' }}</td>
                          <td>
                          @if($assessment->report)
                            <a href="{{ $assessment->report }}">Report</a>
                          @else
                            <a href="{{ route('calculateAssessmentScore', ['assessment_id'=> ($assessment->id)]) }}">Get Report</a>
                          @endif
                          </td>
                          <td>
                          @if($assessment->approve)
                            {{ $assessment->approve->available_date }}
                          @else
                            -
                          @endif
                          </td>
                          <td>
                          @if($assessment->approve)
                            {{ $assessment->approve->slot }}
                          @else
                            -
                          @endif
                          </td>
                          <td>
                            @if($assessment->started_at)
                            {{ $assessment->started_at->format('M d,Y h:i a') }}
                            @endif
                          </td>
                          <td>
                            @if($assessment->ended_at)
                            {{ $assessment->ended_at->format('M d,Y h:i a') }}
                            @endif
                          </td>
                          <td>
                              {{ $assessment->score->attempts ?? '' }}
                          </td>
                          <td>
                              {{ json_encode($assessment->score->scores) ?? '' }}
                         </td>
                        </tr>
                      </tbody>
                    </table>
            </div>
            @else
                <div class="text-center my-10">
                Assessment Detail Not avaiable
                </div>
            @endif
        </div>
        </div>
    </div>
  </div>
</x-slot>
</x-backend-layout>
