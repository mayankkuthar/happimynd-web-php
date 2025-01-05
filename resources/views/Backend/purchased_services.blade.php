<x-backend-layout>
  <x-slot name="title">
    Add Available Date
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
    <!-- page content -->
    <style>
      .plan-checkbox{
        margin-bottom: 5px;
      }
    </style>
    <div class="right_col" role="main">
      <div class="row">
        <form action="{{ route('admin.allBookedDate.post') }}" method="get">
          <div class="col">
            <div class="col-md-12">

            </div>
          </div>
        </div>
        <div class="row">

        </div>
      </form>
    @isset($purchasedServices)
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
                <h4 class="font-13 m-b-30 text-center text-primary">
                    Paid Services
                </h4>
                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>S.NO</th>
                      <th>Date</th>
                      <th>Email</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Title</th>
                      <th>Service Type</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($purchasedServices as $service )
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        {{ date('d-M-Y', strtotime($service->created_at)) }}
                      </td>
                      <td>
                        {{ $service->email }}
                      </td>
                      <td>
                        {{ $service->name}}
                      </td>
                      <td>
                        {{ $service->mobile}}
                      </td>
                      <td>
                        {{ $service->otherService->title}}
                      </td>
                      <td>
                        {{ $service->otherService->type->type->name}}
                      </td>
                      <td>
                        {{ $service->receipt->amount ?? ''}}
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
        </div>
        @endisset
      </div>



    <x-slot name="js">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
      <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src=""></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      {{-- https://code.jquery.com/jquery-3.5.1.js --}}


    </x-slot>
  </x-slot>
</x-backend-layout>
<script>
  $(document).ready(function(){

          $(".dateslot").datepicker({
          });
  })
</script>
