<x-backend-layout>
  <x-slot name="title">
    All Coupons
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2>Coupons</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-sm-12">
                  <button class="btn btn-primary btn-round" onclick="location.href='{{ route('admin.coupon.add') }}'">Add</button>
                  <div class="card-box table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dataTable no-footer dtr-inline" style="width: 100%;" role="grid" aria-describedby="datatable-buttons_info">
                      <thead>
                        <tr role="row">
                          <th class="" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;" aria-label="">id</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;">Code</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 177px;">Applied to</th>
                          <th class="sorting_desc" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 81px;">Discount</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 75px;" >No. of users used / limit</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 75px;">Expiry Time</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 60px;">Status</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 60px;">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($coupons as $coupon)
                        <tr role="row">
                            <td>{{ $coupon->id }}</td>
                            <td class="">{{ $coupon->code }}</td>
                            <td>
                              @foreach($coupon->couponPlan as $couponPlan)

                              <?php  

                                $name = $couponPlan->plan->package->name;
                                if($name == 'HappiLIFE Summary Reading'){
                                  $name = 'HappiLEARN';
                                }
                                if($name == 'HappiLIFE Screening'){
                                  $name = 'HappiLIFE Awareness Tool';
                                }
                                
                              ?>


                                {{ $loop->iteration }}) {{ $name ?? '-'}} {{ $couponPlan->plan->printDuration() }} @if($couponPlan->plan->isHappiTALKPlan()) {{ $couponPlan->plan->expertLevel->name }} @endif <br>
                              @endforeach
                            </td>
                            <td>{{ $coupon->discount_percent }} %</td>
                            <td><b>Used Count:</b> {{ $coupon->couponReceipt->count() ?? '-'}} <br>
                                <b>Limit:</b> {{ $coupon->max_uses ?? 'NA'}} </td>
                            <td>{{$coupon->expired_at ?? 'NA'}}</td>
                            <td>{{$coupon->getStatus()}}</td>
                            <td>
                              <a href="{{ route('admin.coupon.edit', ['id' => $coupon->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                              <a href="#" class="btn btn-danger btn-xs" onclick="deleteCoupon('{{ route('admin.coupon.delete', ['id' => $coupon->id]) }}')"><i class="fa fa-trash-o"></i> Delete </a>
                            </td>
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
      </div>
    </x-slot>
    <x-slot name="js">
      <script>
        function deleteCoupon(url){
          var check = confirm('Confirm to delete');
          if(check){
            location.href=url;
          }
        }
      </script>
    </x-slot>
  </x-backend-layout>