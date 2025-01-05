<x-backend-layout>
  <x-slot name="title">
    Add Coupon
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Add Coupon</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                @include('Backend.includes.flash_message')
                <br>
                <form class="form-horizontal form-label-left" id="add-coupon-form" enctype="multipart/form-data" method="POST" action="{{ route('admin.coupon.post-store') }}">
                  @csrf
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Code<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Enter code" name="code" required value="{{ old('code') }}">
                      @error('code')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">discount percent<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="number" class="form-control" placeholder="Enter discount percentage" name="discount_percent" required value="{{ old('discount_percent') }}">
                      @error('discount_percent')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Usage Limit</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="number" class="form-control" placeholder="Enter max uses" name="max_uses" value="{{ old('max_uses') }}">
                      @error('max_uses')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">description
                    </label>
                    <div class="col-md-9 col-sm-9 ">
                      <textarea class="form-control" rows="3" placeholder="Description" name="description" required>{{ old('description') }}</textarea>
                      @error('description')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Plans to be applied <br> </label>
                    <div class="col-md-9 col-sm-9 ">
                        @foreach($categorical_plans as $expert_level=> $plans)
                          @if($expert_level == 'uncategorized')
                            @foreach($plans as $plan)

                            <?php  

                              $name = $plan->package->name;
                              if($name == 'HappiLIFE Summary Reading'){
                                $name = 'HappiLEARN';
                              }
                              if($name == 'HappiLIFE Screening'){
                                $name = 'HappiLIFE Awareness Tool';
                              }
                              
                            ?>

                            @if($name != 'HappiAPP' && $name != 'HappiBUDDY+ HappiAPP')
                              <label class="form-check-label custom-input-field" for="plans{{ $plan->id }}" >
                                  <input class ="checkbox-input" type="checkbox" name="plans[]" id="plans{{ $plan->id }}" value="{{ $plan->id }}"> {{ $name}}
                              </label>
                              </br>
                            @endif

                            @endforeach
                          @else
                            <b>{{$expert_level}}</b>
                            </br>
                            @foreach($plans as $plan)
                              <label class="form-check-label custom-input-field" for="plans{{ $plan->id }}" >
                                  <input class ="checkbox-input" type="checkbox" name="plans[]" id="plans{{ $plan->id }}" value="{{ $plan->id }}"> {{ $plan->package->name.' for '.$plan->duration->frequency.' sessions' }}
                              </label>
                              </br>
                            @endforeach
                          @endif
                        @endforeach
                        @error('plans')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Coupon expires on</label>
                    <div class="col-md-9 col-sm-9 ">
                    <input type="date" class="form-control" placeholder="" name="ends_at" value="{{ old('ends_at') }}">
                    @error('ends_at')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">status</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="form-control" name="status">
                        <option></option>
                        <option value="0">inactive</option>
                        <option value="1" selected>active</option>
                      </select>
                    </div>
                  </div>
                  <div class="ln_solid"></div>
                  <div class="form-group">
                    <div class="col-md-9 col-sm-9  offset-md-3">
                      <button type="reset" class="btn btn-primary">Reset</button>
                      <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
<script>
</script>