<x-backend-layout>
  <x-slot name="title">
    Manage Bundles & Status
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
          @if(isset($bundleStatus))            
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                  Manage Bundles & Status
                  </p>
                  <div class="x_content">
                    <table id="datatable-button" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Username</th>
                          <th>Package Number</th>
                          <th>Valid</th>
                          <th>Percentage Covered</th>
                          <th>Payment Id</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach($bundleStatus as $bStatus)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $bStatus->user->username }}</td>
                          <td>
                            <a href="{{ route('admin.bundleDetail') }}">{{ $bStatus->package_id }}</a>
                          </td>
                          <td>{{ $bStatus->valid }}</td>
                          <td>{{ $bStatus->percentage_covered }}</td>
                          <td>
                          @if($bStatus->receipt_id)
                            <a href="{{ route('admin.paymentDetail') }}">{{ $bStatus->receipt_id }}</a>
                          @else
                            No Payment required.
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
</x-backend-layout>