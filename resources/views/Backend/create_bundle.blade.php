<x-backend-layout>
  <x-slot name="title">
    Create Bundles
  </x-slot>
  <x-slot name="css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/Backend/css/plugins/select2.min.css') }}" rel="stylesheet">
  </x-slot>
  <x-slot name="js">
      <script src="{{ asset('assets/Backend/js/plugins/select2.full.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
      <script>
        $(document).ready(function () {
          $(".select2_multiple").select2("destroy").select2({
            maximumSelectionLength: -1,
            placeholder: "Select plans",
            allowClear: true
          });
        });
      </script>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="x_panel">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                    Create Bundles
                  </p>


                  @if(Session::has("error"))
                  <div class="alert alert-danger">{{Session::get("error")}}</div>
                  @endif
                  @if(Session::has("success"))
                  <div class="alert alert-success">{{Session::get("success")}}</div>
                  @endif
                  @if ($errors->any())
                  <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{$error}}
                        @endforeach
                  </div>
                  @endif

                  <div class="x_content">
                      
          <form method="POST" id="validate-form" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">


                  <label>Bundle Name</label>
                  <input type="text" class="form-control" name="name" required>
                  <br> 

                  <label>Bundle Description</label>
                  <textarea class="form-control" name="description" required></textarea>
                  <br> 


                  <label>Plan to be mapped</label>
                  <select class="select2_multiple form-control"  multiple="multiple" tabindex="-1" name="plans[]" required>
                        @foreach($single_plans as $row)

                        <?php
                        $name = $row->name;
                        if($name == "HappiLIFE Summary Reading"){
                          $name = "HappiLEARN";
                        }
                        ?> 
                        <option value="{{ $row->plan[0]->id }}" >{{ $name }}</option>
                        @endforeach
                      </select>
                  <br>
 

                  <label>Price</label>
                  <input type="text" class="form-control" name="price" required>
                  <br> 


                  <label>Discount percentage</label>
                  <input type="text" class="form-control" name="discount_percentage" required>
                  <br> 



                  <label>Discounted Price</label>
                  <input type="text" class="form-control" name="discounted_price" required>
                  <br> 

                  <label>Validity (in days)</label>
                  <input type="number" class="form-control" name="validity" min="1" placeholder="e.g. 30 for 1 month. Leave blank for lifetime">
                  <br> 
  
                  <br>
                  <br>

                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
              </div>

            </div>
          </div>
          </form>

                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
 