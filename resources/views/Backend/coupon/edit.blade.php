<x-backend-layout>
  <x-slot name="title">
    Edit Coupon
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Edit Coupon</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <br>
                <form class="form-horizontal form-label-left" id="add-psychologist-form" enctype="multipart/form-data" method="POST" action="{{ route('admin.coupon.update' ,['id' => $coupon->id]) }}">
                  @csrf
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Code<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Enter code" name="code" value="{{$coupon->code}}"required>
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">discount percent<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="number" class="form-control" placeholder="Enter discount percentage" name="discount_percent" value="{{$coupon->discount_percent}}" required>
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">max uses</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="number" class="form-control" placeholder="Enter max uses" name="max_uses" value="{{isset($coupon->max_uses)?$coupon->max_uses:1}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">description
                    </label>
                    <div class="col-md-9 col-sm-9 ">
                      <textarea class="form-control" rows="3" placeholder="Description" name="description" required>{{isset($coupon->description)?$coupon->description:""}}</textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Plans to be applied <br> *<b>Note</b>: if selected bundle plans then this coupon isn't applicable for B2B users</label>
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


                        @if(in_array($plan->id, $coupon->couponPlan->pluck('plan_id')->toArray()))
                        <label class="form-check-label custom-input-field" for="plans{{$plan->id}}" >
                            <input class ="checkbox-input" type="checkbox" name="plans[]" id="plans{{$plan->id}}" value="{{ $plan->id }}" checked> {{ $name. $plan->printDuration() }}
                        </label>
                        </br>
                        @else
                        <label class="form-check-label custom-input-field" for="plans{{$plan->id}}" >
                            <input class ="checkbox-input" type="checkbox" name="plans[]" id="plans{{$plan->id}}" value="{{ $plan->id }}"> {{ $name. $plan->printDuration() }}
                        </label>
                        </br>
                        @endif
                        @endforeach
                        @else
                        <b>{{$expert_level}}</b>
                        </br>
                        @foreach($plans as $plan)
                        @if(in_array($plan->id, $coupon->couponPlan->pluck('plan_id')->toArray()))
                        <label class="form-check-label custom-input-field" for="plans{{$plan->id}}" >
                            <input class ="checkbox-input" type="checkbox" name="plans[]" id="plans{{$plan->id}}" value="{{ $plan->id }}" checked> {{ $plan->package->name.' '.'price'.' '.$plan->price . ' '. $plan->printDuration() }}
                        </label>
                        </br>
                        @else
                        <label class="form-check-label custom-input-field" for="plans{{$plan->id}}" >
                            <input class ="checkbox-input" type="checkbox" name="plans[]" id="plans{{$plan->id}}" value="{{ $plan->id }}"> {{ $plan->package->name.' '.'price'.' '.$plan->price.$plan->printDuration() }}
                        </label>
                        </br>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">ends at</label>
                    <div class="col-md-9 col-sm-9 ">
                    <input type="date" class="form-control" placeholder="Enter Date to be valid" name="ends_at" value="{{isset($coupon->expired_at)?$coupon->expired_at->format('Y-m-d'):''}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">status</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="form-control" name="status">
                        <option></option>
                        <option value="0" @if($coupon['status'] == 0) selected @endif>inactive</option>
                        <option value="1" @if($coupon['status'] == 1) selected @endif>active</option>
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
 var t= {!! json_encode($coupon) !!}
 console.log(t)
</script>