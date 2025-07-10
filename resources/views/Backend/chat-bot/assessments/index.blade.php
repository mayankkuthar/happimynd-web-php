<x-backend-layout>
  <x-slot name="title">
    Assessments
  </x-slot>

  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="page-title">
        <div class="title_left">
          <h3>Assessments</h3>
        </div>
      </div>

      <div class="clearfix"></div>

      <div class="row">
        <div class="col-sm-12">
          <div class="flash-message">
            @if (Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
          </div>

          <div class="x_panel">
            <div class="x_content">
              <div class="excel-button mb-4">
                <form action="{{ route('admin.chat-bot.assessments.download') }}" method="post">
                  @csrf

                  <div class="row gap-y-4">
                    <div class="col-sm-12 col-md">
                      <input type="date" class="form-control" id="from" name="from">
                    </div>

                    <div class="col-sm-12 col-md">
                      <input type="date" class="form-control" id="to" name="to">
                    </div>

                    <div class="col-sm-12 col-md-auto">
                      <button type="submit" class="btn btn-primary">Download</button>
                    </div>
                  </div>
                </form>
              </div>

              <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                  <th>No.</th>
                  <th>Assessment ID</th>
                  <th>Username</th>
                  <th>Nickname</th>
                  <th>Email</th>
                  <th>Mobile</th>
                  <th>Organization</th>
                  <th>Coupons used</th>
                  <th>Category</th>
                  <th>Score</th>
                  <th>Report</th>
                  <th>Completed on</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($assessments as $assessment)
                    <tr>
                      <th>{{ $loop->iteration }}</th>
                      <td>{{ $assessment->id }}</td>
                      <td>{{ $assessment->user->username }}</td>
                      <td>{{ $assessment->user->nickname }}</td>
                      <td>{{ $assessment->user->email ?? '-' }}</td>
                      <td>{{ $assessment->user->mobile ?? '-' }}</td>

                      @if ($assessment->user->isOrganizationUser())
                        <td>{{ $assessment->user->userToken->token->organization->name ?? '-' }}</td>
                      @else
                        <td>-</td>
                      @endif

                      <td>{{ $assessment->user->getUsedCouponCodes() ?? '-' }}</td>
                      <td>{{ $assessment->category->name }}</td>
                      <td>{{ $assessment->score }}</td>
                      <td>{{ $assessment->report->interpretation }}</td>
                      <td>{{ $assessment->created_at->format('M d,Y h:i a') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              <x-pagination-dropdown :paginator="$assessments" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>

  <x-slot name="css">
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

  <x-slot name="js">
    <script>
      function validateDate(from, to) {
        // One day in milliseconds
        const DAYINMS = 1000 * 60 * 60 * 24;
        const FROM = new Date(`"${start.value}"`);
        const TO = new Date(`"${end.value}"`);

        const utc1 = Date.UTC(FROM.getFullYear(), FROM.getMonth(), FROM.getDate());
        const utc2 = Date.UTC(TO.getFullYear(), TO.getMonth(), TO.getDate());

        if(Math.floor((utc2 - utc1) / DAYINMS) > 31) {
          alert('End Date must be atmost 30 days from start date');
          return(false);
        }
      }
    </script>
  </x-slot>
</x-backend-layout>
