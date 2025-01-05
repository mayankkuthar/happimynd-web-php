@extends('layouts.app')

@section('title', 'Happimynd | Payment Bundles')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.loginsuccesspopup')
  @include('Frontend.includes.popups.commingsoon')
  @include('Frontend.includes.popups.campaign.usernamePopup')
  @include('Frontend.includes.popups.discount_coupon')
  <div class="bundles">
  <form action="{{ route('campaign.payment.orderBundle') }}" method="get">
    @csrf
    <div class="container">
      <div class="bundles__content">
        @foreach($packages as $package)
          <div class="bundles__content__paymentoptions">
            <div class="row">
              <div class="col-lg-6">
                <div class="bundles__content__paymentoptions__text">
                  <h1><span class="bundles__content__paymentoptions__text__name">{{ $package->name }}</span>
                  @if($package->bundle)
                    <span class="bundles__content__paymentoptions__text__offer">Bundle Deal</span>
                  @elseif($package->plan[0]->offer_max_discount != 0)

                    <span class="bundles__content__paymentoptions__text__offer">@if($package->name == 'HappiTALK') Upto @endif{{ $package->plan[count($package->plan)-1]->offer_max_discount}}% Off</span>
                  @endif
                  </h1>
                  <p>{{ $package->description }}</p>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="bundles__content__paymentoptions__price">
                  @foreach($package->plan as $plan)
                  <div class="row align-items-end">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                      <div class="bundles__content__paymentoptions__price__text">
                        <h2>@if($plan->offer)<span>Rs.{{ $plan->price }}</span>@endif
                        @if($plan->offer)
                          @if($plan->offer->price==0)
                            <b>Free</b>
                          @else
                          @if($package->name == 'HappiTALK') <b>Rs.{{ $plan->offer->price/($plan->duration->frequency) }}</b>
                          @else <b>Rs.{{ $plan->offer->price }}</b>@endif
                          @endif
                        @else
                          <b>Rs.{{ $plan->price }}</b>
                        @endif
                        </h2>
                        <span>@if($package->name == 'HappiTALK' && $plan->duration->frequency != '1'){{ $plan->offer->price }} for {{$plan->duration->name }}@else{{ ' '.$plan->duration->name }} @endif</span>
                        {{-- Show and hide this h4 tag when coupon applied or not --}}
                        <h4 class="coupon-applied">Coupon discount: ₹99</h4>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="bundles__content__paymentoptions__price__addbtn">
                      <input type="hidden" name="user_id" class="user_id">
                      <input type="checkbox" name="plan[]" value={{$plan->id}} id="hidden_checkbox_{{$plan->id}}" hidden/>
                      @if(!array_key_exists($plan->id, $campaign->plan_id) && !$plan->isActive())
                        <button class="bundles__content__paymentoptions__price__addbtn__add" type="button" id="addbtn_{{$loop->iteration}}" disabled>Add</button>
                      @elseif($plan->offer)
                        <button
                          class="bundles__content__paymentoptions__price__addbtn__add"
                          type="button" id="addbtn_{{$loop->iteration}}"
                          data-amount="{{ $plan->offer->price }}"
                          data-checkbox_id="hidden_checkbox_{{$plan->id}}">Add</button>
                      @else
                        <button class="bundles__content__paymentoptions__price__addbtn__add" type="button" id="addbtn_{{$loop->iteration}}" data-amount="{{ $plan->price }}" data-package_id="{{ $package->id }}" data-checkbox_id="hidden_checkbox_{{$plan->id}}">Add</button>
                      @endif
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    <div class="bundles__proceedpay">
      <div class="container">
        <div class="row bundles__proceedpay__content-new">
          <div class="col-lg-6 col-md-6 col-sm-12">
            {{-- Show and hide this below div when coupon applied or not --}}
            <div>
              <p class="coupon-applied-text">Coupon Applied 
                <span style="cursor: pointer;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </p>
              <h4 class="coupon-applied">You have saved additional  ₹99</h4>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 mt-md-0 mt-2">
            <div class="d-flex align-items-center justify-content-start justify-content-md-end">
              <div>
                <h1>Total amount: <span id="total_amount" data-total="0">0</span></h1>
                {{-- Apply coupon btn --}}
                <button type="button" class="apply-coupon" onclick="showApplyCouponPopup()" >Apply Coupon</button>
              </div>
              <button type="submit" id="proceedToPayButton" disabled=true onclick="$('#proceedToPayButton').html('<div class=\'btn__loader1\'></div>');">Proceed</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
</div>
@endsection

@section('js')
<script>
$(".bundles__content__paymentoptions__price__addbtn__add").click(function(){
  if($(this).hasClass("bundles__content__paymentoptions__price__addbtn__add--added")){  // if packages remove
    $(this).removeClass("bundles__content__paymentoptions__price__addbtn__add--added");
    $(this).text("Add");
    var amount_exists = $("#total_amount").data('total');
    var amount_this = $(this).data('amount');
    $("#total_amount").html(amount_exists - amount_this);
    $('#total_amount').data('total', amount_exists - amount_this);

    // Uncheck the checkbox
    console.log($(this).data('checkbox_id'))
    $("#"+$(this).data('checkbox_id')).prop('checked', false);

  }else{ // if package added
    $(this).addClass("bundles__content__paymentoptions__price__addbtn__add--added");
    $(this).text("Added");
    var amount_exists = $("#total_amount").data('total');
    var amount_this = $(this).data('amount');
    $("#total_amount").html(amount_exists+amount_this);
    $('#total_amount').data('total', amount_exists + amount_this);

    // check the checkbox
    $("#"+$(this).data('checkbox_id')).prop('checked', true);
  }
  if($('.bundles__content__paymentoptions__price__addbtn__add--added').length>0){
    $('#proceedToPayButton').removeAttr('disabled');
    // console.log('enabled')
  }
  else if($('.bundles__content__paymentoptions__price__addbtn__add--added').length == 0){
    $('#proceedToPayButton').attr('disabled', true);
    // console.log('disabled')
  }
})
$(document).ready(function(){
  plan_id = "{{ implode(',',array_keys($campaign->plan_id)) }}"
  plan_id = plan_id.split(',');
  console.log(plan_id)
  plan_id.forEach(function(value,index,array){
    $('#hidden_checkbox_'+value).parent().find('button').click();
    console.log('hidden_checkbox_'+value)
  });
})
function showApplyCouponPopup() {
  $("#discountcoupon").modal('show');
}
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection