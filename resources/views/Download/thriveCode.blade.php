<x-backend-layout>


<x-slot name="title">
    Download HappiApp Code
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
            @if(isset($thriveCodes))
            <div class="card-box table-responsive">
                <h3 class="text-muted text-center font-13 my-10">
                    @if(count($thriveCodes)>0)
                        {{ $thriveCodes[0]->organization->name ?? ''}} HappiApp Code: : {{ count($thriveCodes) ?? ''}}
                    @endif
                </h3>
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                    <th>S.No</th>
                    <th>Organization</th>
                    <th>Code</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($thriveCodes as $code)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $code->organization->name ?? ''}}</td>
                        <td>{{ $code->code ?? '' }}</td>
                        <td>{{ $code->user->username ?? '' }}</td>
                        <td>{{ $code->user->email ?? '' }}</td>
                        <td>
                        @php
                            if($code->isUsable()) {
                                echo "Active";
                            }
                            elseif($code->isExpired()) {
                                echo "Used";
                            }
                            elseif($code->isDisabled()) {
                                echo "Disabled";
                            }
                        @endphp
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="text-center my-10">
                No HappiApp Code
                </div>
            @endif
        </div>
        </div>
    </div>
  </div>
</x-slot>
</x-backend-layout>
