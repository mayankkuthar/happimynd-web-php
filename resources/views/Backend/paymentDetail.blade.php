<x-backend-layout>
  <x-slot name="title">
    Payment Detail
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="x_panel">
          @if(isset($reciepts))
          <div class="row">
            <div class="col-sm-12">
              <div class="card-box table-responsive custom-change-table">
                <p class="text-muted font-13 m-b-30">
                  Payment Details
                </p>
                <div class="x_content">
                  <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Marchant Name</th>
                        <th>Package Number</th>
                        <th>Username</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Transation ID</th>
                        <th>Transaction Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($reciepts as $reciept)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $reciept->marchant_name }}</td>
                        <td>
                          <!-- {{ implode(' | ', $reciept->plans->pluck('plan.package.name')->toArray())}} -->
                          <?php 
                          $array = $reciept->plans->pluck('plan.package.name')->toArray();
                          $search = 'HappiLIFE Summary Reading';
                          $replace = 'HappiLearn';
                          foreach ($array as $key => $value) {
                              if ($value == $search) {
                                  $array[$key] = $replace;
                                  break;
                              }
                          }
                        ?>
                        {{ implode(" || ", $array) }}
                        </td>
                        <td>{{ $reciept->user->username ?? '' }}</td>
                        <td>{{ $reciept->amount }}</td>
                        <td>{{ $reciept->status }}</td>
                        <td>{{ $reciept->order_id }}</td>
                        <td>{{ $reciept->created_at->format("d-M-y g:i a") }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>

                  <div class="custompaginationbar"> {{$reciepts->links()}}</div>

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