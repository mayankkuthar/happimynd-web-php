<x-backend-layout>
  <x-slot name="title">
    Raised Queries
  </x-slot>
  <x-slot name="css">
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
          @if(isset($raisedQueries))
          <div class="row">
            <div class="col-sm-12">
              <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30">
                  <h3>Raised Queries</h3>
                  <h5>Open: {{ $openQueryCount }} Closed: {{ $closedQueryCount }}</h5>
                </p>
                <div class="x_content">
                  <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Username</th>
                        <th>Platform</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Query</th>
                        <th>Received at</th>
                        <th>Reply</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($raisedQueries as $query)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $query->user->username }}</td>
                        <td>{{ $query->platform }}</td>
                        <td>
                          @if($query->status)
                          <a href="#" class="badge badge-success" onclick="queryStatusChange(this)" data-id="{{ $query->id }}">Closed</a>
                          @else
                          <a href="javascript:void(0)" class="badge badge-danger" onclick="queryStatusChange(this)" data-id="{{ $query->id }}">Open</a>
                          @endif
                        </td>
                        <td>{{ $query->category }}</td>
                        <td>{{ $query->query }}</td>
                        <td>{{ $query->created_at->format('d-M-Y g:i a') }}</td>
                        <td>
                          @if($query->user->email)
                          <a href="mailto:{{ $query->user->email }}">Send Email</a>
                          @else
                          <a href="{{ route('admin.getSendNotificationView',['user_id' => $query->user_id]) }}">Notify</a>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
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
    <script type="text/javascript">
      function queryStatusChange(element){
        var isFixed = 1;
        if($(element).hasClass('badge-success')){
          isFixed = 0;
        }
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.changeRaisedQueryStatus.post') }}",
            data: {'query_id':$(element).data('id'), 'status' : isFixed },
            success: function(data)
            {
                if(data.error == false){
                  if(isFixed) {
                    $(element).removeClass('badge-danger').addClass('badge-success').text('Closed');
                  }
                  else{
                    $(element).addClass('badge-danger').removeClass('badge-success').text('Open');
                  }
                }
              }
            });
          };
        </script>
      </x-slot>
    </x-backend-layout>