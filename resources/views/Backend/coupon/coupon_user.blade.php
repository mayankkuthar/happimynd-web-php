<x-backend-layout>
    <x-slot name="title">
        Customer list
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
                <h2>Users</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                  <div class="row">
                      <div class="col-sm-12">
                        <!-- Search Form -->
                        <div class="card-box">
                            <form method="GET" action="{{ route('admin.coupon.coupon-user') }}" class="form-inline">
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" name="search" placeholder="Search by username, email, or coupon code" value="{{ $search ?? '' }}">
                                </div>
                                <button type="submit" class="btn btn-primary mb-2">Search</button>
                                @if($search)
                                    <a href="{{ route('admin.coupon.coupon-user') }}" class="btn btn-secondary mb-2 ml-2">Clear</a>
                                @endif
                            </form>
                        </div>
                        
                        <div class="card-box table-responsive">
                            <!-- Statistics -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="text-muted" style="font-size: 14px; line-height: 1.6; margin: 0; padding: 8px 0;">
                                        Showing 
                                        <span style="font-weight: 600; color: #333;">{{ $coupon_receipts->firstItem() ?? 0 }}</span> 
                                        to 
                                        <span style="font-weight: 600; color: #333;">{{ $coupon_receipts->lastItem() ?? 0 }}</span> 
                                        of 
                                        <span style="font-weight: 600; color: #333;">{{ number_format($coupon_receipts->total()) }}</span> 
                                        results
                                    </p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p class="text-muted" style="font-size: 14px; line-height: 1.6; margin: 0; padding: 8px 0;">
                                        Page 
                                        <span style="font-weight: 600; color: #333;">{{ $coupon_receipts->currentPage() }}</span> 
                                        of 
                                        <span style="font-weight: 600; color: #333;">{{ $coupon_receipts->lastPage() }}</span>
                                    </p>
                                </div>
                            </div>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Username</th>
                      <th>E-mail</th>
                      <th>Amount</th>
                      <th>Coupon Code</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                      @forelse($coupon_receipts as $coupon_receipt)
                        <tr>
                        <td>{{ $loop->iteration + (($coupon_receipts->currentPage() - 1) * $coupon_receipts->perPage()) }}</td>
                        <td>{{ $coupon_receipt->user->username ?? '-' }}</td>
                        <td>{{ $coupon_receipt->user->email ?? '-' }}</td>
                        <td>{{ $coupon_receipt->receipt->amount ?? 0 }}</td>
                        <td>{{ $coupon_receipt->coupon->code ?? '-' }}({{ $coupon_receipt->coupon->discount_percent ?? 0 }} % off)</td>
                        <td>{{ $coupon_receipt->created_at ? $coupon_receipt->created_at->format('M d,Y h:i a') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No coupon users found.</td>
                        </tr>
                        @endforelse
                  </tbody>
                </table>
                
                <x-pagination-dropdown :paginator="$coupon_receipts" />


              </div>
            </div>
          </div>
            </div>
    </x-slot>
</x-backend-layout>
