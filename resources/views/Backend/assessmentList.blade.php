<x-backend-layout>
  <x-slot name="title">
    Assessment Lists
  </x-slot>
  <x-slot name="css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
         .dt-buttons{
           display:none;
         }

         div#datatable-buttons_paginate , #datatable-buttons_info{
              display: none;
          }
          .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {

            margin-bottom: 0px;
            margin-left: 15px;

          }


          div.dataTables_wrapper div.dataTables_length label { 
              display: none;
          }

        </style>
    </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="flash-message">
          @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has( $msg))
            <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
            @endif
          @endforeach
        </div>
        <div class="x_panel">
          @if(isset($assessments))
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                    Assessment Lists
                  </p>
                  <div class="x_content">
                  <div class="excel-button">
                    <form action="{{route('admin.downloadXL')}}">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" >
                    <!-- <label for="org">Organization:</label> -->
                    <!-- <label> Select Organizati  on / B2C </label> -->



                    <!-- <select id="organizations" name="organization_id">
                      <option value="">Select Organization</option>
                      <optgroup label="Organizations">
                      @foreach($organizations as $organization)
                        <option value = {{ $organization->id}}> {{ $organization->name }}(Assessments: {{ $organization->assessmentCount }})</option>
                        @endforeach
                      </optgroup>
                      <optgroup label="Others">
                        <option value="b2c">B2C(Assessments: {{ $b2cAssessmentCount }})</option>
                      </optgroup>
                    </select> -->



                    <button type="submit" id="downloadbtn" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
                    <span id="buttonText">Download Excel</span>

                    <span class="spinner-grow spinner-grow-sm loader" id="loader" role="status" style="display: none;" aria-hidden="true"></span><span class="loader" style="display: none">  Generating...</span>
                  </button>
                  </form>
                  </div>
                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Assessment ID</th>
                          <th>Username</th>
                          <th>Nickname</th>
                          <th>Organization</th>
                          <th>Token</th>
                          <th>Report</th>
                          <!-- <th>Delete</th> -->
                          <th>Email/Mobile  </th>

                          <th>Batch</th>
                          <th>Assessment Start time</th>
                          <th>Assessment End Time </th>
                          <th># Question Attempt</th>
                          <th>scores</th>
                          <th>Coupon used</th>
                          <th>CallTime</th>
                          <th>Slot</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach($assessments as $assessment)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $assessment->id }}</td>
                          <td>{{ $assessment->user->username }}</td>
                          <td>{{ $assessment->user->nickname }}</td>
                          <td>{{ $assessment->organization ?? '--' }}</td>
                          <td>{{ $assessment->token ?? '--' }}</td>
                          <td>
                          @if($assessment->report)
                            <a href="{{ $assessment->report }}">Report</a>
                            <a href="javascript:void(0)" class="badge badge-primary" onclick="reGenerateReport({{ $assessment->id }})">Re-Generate Report</a>
                            {{-- <a href="#" onclick="reGenerateReport({{ $assessment->id }})">Re-Generate Report </a> --}}
                          @else
                            @if($assessment->ended_at != null)
                              <a href="{{ route('calculateAssessmentScore', ['assessment_id'=> ($assessment->id)]) }}">Get Report</a><br>
                              <a href="javascript:void(0)" class="badge badge-primary" onclick="reGenerateReport({{ $assessment->id }})">Generate Report</a>
                            @else
                              Assessment not completed
                            @endif

                          @endif
                          </td>
                          <!-- <td>
                            <button class="btn btn-primary"><a href="{{ route('admin.assesmentApprove', ['assessment_id'=>base64_encode($assessment->id), 'status'=>1]) }}"><b>Approve</b></a></button>
                            <button class="btn btn-danger"><a href="{{ route('admin.assesmentApprove', ['assessment_id'=>base64_encode($assessment->id), 'status'=>0]) }}"><b>Reject</b></a></button>
                          </td> -->


                          <!-- <td>
                            <button class="btn btn-danger"><a href="{{ route('admin.deleteAssessment', ['assessment_id'=>base64_encode($assessment->id)]) }}" style="color:white;"><b>Delete</b></a></button>
                          </td> -->
                          <td>
                            {{ $assessment->user->email ?? '-' }}
                            {{ $assessment->user->mobile ?? '-' }}

                          </td>
                          <td>
                            {{ $assessment->batch->name ?? '-' }}
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
                              @if($assessment->score)
                                {{ $assessment->score->attempts ." / ". $assessment->batch->batchCategory->sum('questions_count') ?? '' }}
                              @endif
                          </td>
                          <td>
                              @if($assessment->score)
                              @foreach($assessment->score->scores as $score)
                                {{ json_encode($score) }} <br>
                                @endforeach
                              @else
                              {{ '-' }}
                              @endif
                         </td>
                         <td>
                           {{ $assessment->user->getUsedCouponCodes()??'-' }}
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

                         </td>

                        </tr>
                        @endforeach
                      </tbody>
                    </table>

                    <x-pagination-dropdown :paginator="$assessments" />
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </x-slot>
  <x-slot name="js">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {
          $('#organizations').select2();
      });
      function reGenerateReport(assessment_id) {
        url = "{{ url('/') }}/admin/regenerate-report/"+assessment_id;
        $.ajax({
          url: url,
          method: "GET",
          success: function(data){

          }
        });
      }

      function validateDate(start,end){
        if (!start.value || !end.value) {
            alert("Please select both start and end dates.");
            return false;
        }
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