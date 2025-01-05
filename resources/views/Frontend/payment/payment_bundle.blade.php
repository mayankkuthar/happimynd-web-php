@extends('layouts.app')

@section('title', 'Happimynd | Payment Bundles')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.loginsuccesspopup')
  @include('Frontend.includes.popups.commingsoon')
  @include('Frontend.includes.popups.discount_coupon')
  <div class="bundles">
  <form action="{{ route('payment.orderBundle') }}" method="get">
    <input type="hidden" id="psychologist_plan_id" name = "psychologist_plan_id">
    <input type="hidden" id="psychologist_id" name="psychologist_id">
    <input type="hidden" id="psychologist_session" name="psychologist_session">
    @csrf
    <div class="container">
      <div class="bundles__content">
        @foreach($packages as $package)

        <?php  
              $name = $package->name;
              if($name == 'HappiLIFE Summary Reading'){
                  $name = 'HappiLEARN';
              } 
              if($name == 'HappiLIFE Screening'){
                  $name = 'HappiLIFE Awareness Tool';
              } 
            ?>  

        @if(strtolower($name) != strtolower('HappiAPP') && strtolower($name) != strtolower('HappiBUDDY+ HappiAPP'))
          <div class="bundles__content__paymentoptions">
            <div class="row">
              <div class="col-lg-6">
                <div class="bundles__content__paymentoptions__text">
                  <h1><span class="bundles__content__paymentoptions__text__name">{{ $name }}</span>
                  @if($package->bundle)
                    <span class="bundles__content__paymentoptions__text__offer">Bundle Deal</span>
                  @elseif($package->plan[0]->offer_max_discount != 0)

                    <span class="bundles__content__paymentoptions__text__offer">@if($package->name == 'HappiTALK') Upto @endif{{ $package->plan[count($package->plan)-1]->offer_max_discount}}% Off</span>
                  @endif
                  </h1>
                  <p>{{ $package->description }}</p>
                </div>
              </div>
              {{-- @if(count($package->plan)>1) {{ dd($package) }} @endif --}}
              <div class="col-lg-6">
                <div class="bundles__content__paymentoptions__price">
                  @foreach($package->plan as $plan)
                  <div class="row align-items-end">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                      <div class="bundles__content__paymentoptions__price__text">
                        <h2>
                          @if($plan->isHappiTalkPlan())
                            <span id="psychologist_price_default_text" style="color: #3C92C6;text-decoration: none;"> Starting from<b> Rs.{{ $plan->getPerSessionSellingPrice() }} </b></span>
                            <span id="psychologist_price_custom_text" style="line-height: 138.5%;letter-spacing: -0.02em;color: #3C92C6;text-decoration: none;"></span>
                          @else
                            @if($plan->hasOffer())<span>Rs.{{ $plan->getCostPrice() }}</span>@endif
                            @if($plan->hasOffer())
                              @if($plan->getSellingPrice() == 0)
                                <b>Free</b>
                              @else
                              <b>Rs.{{ $plan->getSellingPrice() }}</b>
                              @endif
                            @else
                              <b>Rs.{{ $plan->getCostprice() }}</b>
                            @endif
                          @endif
                        </h2>
                        <span>


                          @if($package->name == 'HappiTALK' && $plan->duration->frequency != '1')
                            <div id="psychologist_session_text">per {{$plan->duration->printType() }}</div>
                          @else

                          <?php 

                            if($package->name == 'HappiLIFE Screening'){
                              $duration_name = 'Single Report Fees';
                            }
                            else if($package->name == 'HappiLIFE Summary Reading'){ //learn
                              $duration_name = 'Annual Subscription';
                            }
                            else if($package->name == 'HappiGUIDE'){
                              $duration_name = 'One Session Fees';
                            }
                            else if($package->name == 'HappiBUDDY'){
                              $duration_name = 'Annual Subscription';
                            }
                            else if($package->name == 'HappiSELF'){
                              $duration_name = 'Annual Subscription';
                            }
                            else if($package->name == 'HappiSELF + HappiGUIDE'){
                              $duration_name = 'One Session Fees';
                            }
                            else if($package->name == 'HappiLEARN + HappiBUDDY'){
                              $duration_name = 'Annual Subscription';
                            }
                            else if($package->name == 'HappiBUDDY + HappiSELF'){
                              $duration_name = 'Annual Subscription';
                            }
                            else if($package->name == 'HappiLEARN + HappiBUDDY + HappiSELF'){
                              $duration_name = 'Annual Subscription';
                            } 
                            else{
                              $duration_name = $plan->duration->name;
                            }

                          ?>

                            {{ ' '.$duration_name }}
                          @endif
                        </span>
                        {{-- Show and hide this h4 tag when coupon applied or not --}}
                        @if($plan->isHappiTalkPlan())
                          <h4 class="coupon-applied" id="coupon-applied-psychologist"></h4>
                        @else
                          <h4 class="coupon-applied" id="coupon-applied{{$plan->id}}"></h4>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="bundles__content__paymentoptions__price__addbtn">
                      <input type="hidden" name="coupon_code"  id="couponid" value="">
                      <input type="hidden" name="user_id" value="{{$user_id}}">
                      <input type="checkbox" name="plan[]" value="{{$plan->id}}" id="hidden_checkbox_{{$plan->id}}" hidden/>
                      @if($package->name=='HappiTALK')
                        @if($plan->isActive())
                          <button class="bundles__content__paymentoptions__price__addbtn__add @if($package->name=="HappiTALK"){{  "tobe_disabled" }} @endif" type="button" id="addbtn_{{$loop->iteration}}"  data-package-name = "{{ $package->name }}" data-amount="{{ $plan->getSellingPrice() }}" data-checkbox_id="hidden_checkbox_{{$plan->id}}"  data-plan_id="">Book</button>
                        @elseif($plan->offer)
                        <button class="bundles__content__paymentoptions__price__addbtn__add disabled" type="button" id="addbtn_{{$loop->iteration}}"  data-package-name = "{{ $package->name }}" disabled >Book</button>
                        @endif
                      @else
                        @if(!$plan->isActive())
                          <button class="bundles__content__paymentoptions__price__addbtn__add disabled" type="button" id="addbtn_{{$loop->iteration}}"  disabled>Add</button>
                        @else
                          @if(!auth()->user()->organizationHasPlan($plan))
                          <button
                            class="bundles__content__paymentoptions__price__addbtn__add"
                            type="button"
                            id="addbtn_{{$loop->iteration}}"
                            data-amount="{{ $plan->getSellingPrice() }}"
                            data-discount_amount = "{{ $plan->getSellingPrice() }}"
                            data-package_id="{{ $package->id }}"
                            data-checkbox_id="hidden_checkbox_{{$plan->id}}"
                            data-plan_id="{{$plan->id}}"
                            data-package-name = "{{ $package->name }}"
                            data-button_text = "Add"
                            @if($package->name == 'HappiGUIDE') disabled @endif
                          >Add</button>
                          @else
                           <button class="bundles__content__paymentoptions__price__addbtn__add disabled" type="button" id="addbtn_{{$loop->iteration}}"  disabled>Added</button>
                          @endif
                        @endif
                      @endif
                        </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        @endif
        @endforeach
      </div>
    </div>
    <div class="bundles__proceedpay">
      <div class="container">
        <div class="row bundles__proceedpay__content-new">
          <div class="col-lg-6 col-md-6 col-sm-12">
            {{-- Show and hide this below div when coupon applied or not --}}
            <div>
              <div class="coupon-applied-text" style="display:none">
              <p id="coupontext" style="display:inherit"></p>
                <span style="cursor: pointer;" onclick="removeCoupon()">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </p>
            </div>
              <h4 class="coupon-applied" id="coupon-applied-here"  data-total-discount="0">></h4>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 mt-md-0 mt-2">
            <div class="d-flex align-items-center justify-content-start justify-content-md-end">
              <div>
                <h1>Total amount: <span id="total_amount" data-total="0" data-actual-amount="0">0</span></h1>
                {{-- Apply coupon btn --}}
                <button type="button" class="apply-coupon" id="apply-coupon"  onclick="showApplyCouponPopup()" >Apply Coupon</button>
              </div>
              <button type="submit" id="proceedToPayButton" disabled=true onclick="$('#proceedToPayButton').html('<div class=\'btn__loader1\'></div>');clearCart()">Proceed</button>
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
  let coupon_id = '';
  let plans = new Map();
  let discount_price = 0;
  let discount_price_total = 0;
  let discount_value = {};
  let coupon_applied = false;
  let plan_applied = {};
  let plan_applied_id = {};
  var actual_amount = 0;
  function plan(plan_id, amount, discount_price) {
    this.plan_id = plan_id
    this.amount = amount
    this.discount_price = discount_price
  }
  $('#apply-coupon').hide();
$(".bundles__content__paymentoptions__price__addbtn__add").click(function(){
  var psychologistRedirectSkipFlag = "{{ (!auth('user')->user()->isOrganizationUser() && !auth('user')->user()->organizationHasHappiTalkPlan()) }}"
  if($(this).data("packageName") && $(this).data("packageName") == "HappiTALK" && psychologistRedirectSkipFlag && !$(this).hasClass("bundles__content__paymentoptions__price__addbtn__add--added")){
    @if(auth('user')->user()->psychologistAppointment!=null)
      sessionStorage.setItem('show_happitalk_instruction', true)
      location.href="{{ route('user.dashboard') }}";
    @else
      storePlans()
      location.href="{{ route('user.psychologist') }}";
    @endif
    return;
  }else{
    if($(this).hasClass("bundles__content__paymentoptions__price__addbtn__add--added")){  // if packages remove
      var hideCoupon = false;
      removeCoupon();
      $(this).removeClass("bundles__content__paymentoptions__price__addbtn__add--added");
      // $(".bundles__content__paymentoptions__price__addbtn__add").not(".disabled").removeAttr('disabled');
      if($(this).data("packageName") == "HappiTALK"){
        $(this).text("Book");
      }else{
        $(this).text($(this).data('button_text'));
      }
      //var amount_exists = parseFloat($("#total_amount").data('total'));
      var amount_this = parseFloat($(this).data('amount'));
      var actual_amount = parseFloat($("#total_amount").data('actual-amount'))
      var amount_exists = parseFloat($("#total_amount").data('total'));
      var total = 0;
      var package_name = $(this).data('packageName');
      if(coupon_applied){
        if(package_name == "HappiTALK"){
          var psychologist_plan_id = $(this).data('plan_id');
          if( psychologist_plan_id in plan_applied[package_name]){
            console.log(amount_exists,'pol');
            console.log(plan_applied[package_name][psychologist_plan_id].discounted_price,'po')
            total = parseFloat(amount_exists  - plan_applied[package_name][psychologist_plan_id].discounted_price).toFixed(2);
            if($(this).data("packageName") == "HappiTALK"){
              $('#coupon-applied-psychologist').text('')
            }else{
              $('#coupon-applied'+$(this).data('plan_id')).text('')
            }
            var discount_amount = parseFloat(plan_applied[package_name][psychologist_plan_id].price - plan_applied[package_name][psychologist_plan_id].discounted_price).toFixed(2);
            discount_price = +parseFloat(discount_price) - +parseFloat(discount_amount);
            $("#coupon-applied-here").text(`You have saved additional ₹${discount_price.toFixed(2)}`);
            $("#coupon-applied-here").data('total-discount', discount_price.toFixed(2));
          }
        }else if(package_name in plan_applied){
          console.log(amount_exists,'pol');
          console.log(plan_applied[package_name].discounted_price,'po')
          total = parseFloat(amount_exists  - plan_applied[package_name].discounted_price).toFixed(2);
          if($(this).data("packageName") == "HappiTALK"){
            $('#coupon-applied-psychologist').text('')
          }else{
            $('#coupon-applied'+$(this).data('plan_id')).text('')
          }
          var discount_amount = parseFloat(plan_applied[package_name].price - plan_applied[package_name].discounted_price).toFixed(2);
          discount_price = +parseFloat(discount_price) - +parseFloat(discount_amount);
          $("#coupon-applied-here").text(`You have saved additional ₹${discount_price.toFixed(2)}`);
          $("#coupon-applied-here").data('total-discount', discount_price.toFixed(2));
        }else{
          total = parseFloat(amount_exists  - amount_this).toFixed(2);
        }
      }else{
        total = parseFloat(amount_exists  - amount_this).toFixed(2);
      }
      console.log(actual_amount);
      console.log(amount_this);
      actual_amount = parseInt(actual_amount)  - parseInt(amount_this)
      $("#total_amount").html(total);
      $('#total_amount').data('total', total);
      $("#total_amount").data('actual-amount', actual_amount);
      console.log(actual_amount)
      // Uncheck the checkbox
      console.log($(this).data('checkbox_id'))
      $("#"+$(this).data('checkbox_id')).prop('checked', false);
      console.log('pa '+$(this).data('plan_id'))
      if(plans.has($(this).data('plan_id'))){
        plans.delete($(this).data('plan_id'))
      }
       $('.bundles__content__paymentoptions__price__addbtn__add--added').each(function(indx, val) {
        if($(val).data('organization-plan') == 1) {
          hideCoupon = true;
        }
      })
      if($('.bundles__content__paymentoptions__price__addbtn__add--added').length == 0){
        hideCoupon = true;
        // console.log('disabled')
      }
      if(hideCoupon){
        $('#apply-coupon').hide();
      }else{
        $('#apply-coupon').show();
      }
      if($(this).data('organization-plan') != 1){
        unsetPsychologist();
      }
    }else{ // if package added
      var hideCoupon = false;
      $(this).addClass("bundles__content__paymentoptions__price__addbtn__add--added");
      // $(".bundles__content__paymentoptions__price__addbtn__add").not(this).attr('disabled', true)
      $(this).text("Added");
      var actual_amount = parseFloat($("#total_amount").data('actual-amount'))
      var  amount_this = parseFloat($(this).data('amount'));
      var amount_exists = parseFloat($("#total_amount").data('total'));
      var total = 0;
      if(coupon_applied){
        var package_name = $(this).data('packageName');
        console.log(amount_exists)

        if(package_name in plan_applied){
          total = parseFloat(amount_exists  + plan_applied[package_name].discounted_price).toFixed(2);
          var discount_amount = parseFloat(plan_applied[package_name].price - plan_applied[package_name].discounted_price).toFixed(2);
          if(discount_amount > 0.00){
            $('#coupon-applied'+$(this).data('plan_id')).text(`Coupon discount: ₹${discount_amount}`);
          }
          discount_price = +parseFloat(discount_price) +parseFloat(discount_amount);
          console.log(discount_amount)
          console.log(discount_price)
          $("#coupon-applied-here").text(`You have saved additional ₹${discount_price.toFixed(2)}`);
          $("#coupon-applied-here").data('total-discount', discount_price.toFixed(2));
        }else{
          total = parseFloat(amount_exists  + amount_this).toFixed(2);
        }
      }else{
        console.log(amount_exists)
        console.log(amount_this)
        total = parseFloat(amount_exists  + amount_this).toFixed(2);
      }
      console.log($(this));
      //var total = parseFloat(amount_exists+amount_this).toFixed(2);
      actual_amount =  parseInt(actual_amount) + parseInt(amount_this)
      console.log(amount_this)
      console.log("pop")
      console.log(actual_amount)
      console.log('totl.='+total)
      $("#total_amount").html(total);
      $('#total_amount').data('total', total);
      $("#total_amount").data('actual-amount', actual_amount);
      // check the checkbox
      $("#"+$(this).data('checkbox_id')).prop('checked', true);
      if(!plans.has($(this).data('plan_id'))){
        plans.set($(this).data('plan_id'), new plan($(this).data('plan_id'), $(this).data('amount'), $(this).data('discount_amount')))
      }
    }
    if($('.bundles__content__paymentoptions__price__addbtn__add--added').length>0){
      $('#proceedToPayButton').removeAttr('disabled');
      // console.log('enabled')
    }
    else if($('.bundles__content__paymentoptions__price__addbtn__add--added').length == 0){
        $('#proceedToPayButton').attr('disabled', true);
        hideCoupon = true;
        // console.log('disabled')
      }

      $('.bundles__content__paymentoptions__price__addbtn__add--added').each(function(indx, val) {
        if($(val).data('organization-plan') == 1) {
          hideCoupon = true;
        }
      })
      if(hideCoupon){
        removeCoupon();
        $('#apply-coupon').hide();
      }else{
        $('#apply-coupon').show();
      }
  }
})
if(sessionStorage.getItem('add-plan')){
    $('#addbtn_1').trigger('click');
    sessionStorage.removeItem('add-plan');
  }
function showApplyCouponPopup() {
  $("#discountcoupon").modal('show');
  $('#coupon-error-msg').text('');
  $("#coupon_submit").text('Apply');
}
$(".discountcoupon__popup__content__form").submit(function(e){
  e.preventDefault();
  e.stopPropagation();
  var button_text = $('#coupon_submit').text();
  if(button_text == 'Ok'){
    $("#discountcoupon").modal('hide');
    return;
  }
  var code = $('#coupon-code').val();
  console.log(code);
  var plan_id = [];
  var price = [];

  if(code){
    var data = {
      'plan_id':[...plans.keys()],
      'code': code,
    }

    console.log(plans)
    console.log(plans)
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.getElementsByName('csrf-token')[0].content
            }
        });
    $.ajax({
      url: '{{route("user.verify-coupon")}}',
      method: 'POST',
      data: data,
      success: function(result) {
        if(!result.error){
          var discount_percent = result.discount;
          coupon_id = code.trim();
          total_amount = 0;
          var total_discount = 0;
          var actual_amount = 0;
          console.log(result);
          plan_applied = {}
          plan_applied_ids = result.msg.coupon_plan_ids;
          result.msg['coupon_plan'].forEach((val)=>{
            if(val['package_name'] == 'HappiTALK'){
              if(plan_applied[val['package_name']]){
                plan_applied[val['package_name']][val['plan_id']] = {
                  'price': +parseFloat(val['price']).toFixed(2),
                  'discounted_price':+parseFloat(val['discounted_price']).toFixed(2)
                }
              }else{
                plan_applied[val['package_name']] = {}
                plan_applied[val['package_name']][val['plan_id']] = {
                  'price': +parseFloat(val['price']).toFixed(2),
                  'discounted_price':+parseFloat(val['discounted_price']).toFixed(2)
                }
              }
            }else{
              plan_applied[val['package_name']] = {
              'price': +parseFloat(val['price']).toFixed(2),
              'discounted_price':+parseFloat(val['discounted_price']).toFixed(2)
              }
            }
          })
          console.log("popi")
          console.log(plan_applied_ids)
          console.log(plan_applied)
          plans = new Map();
          $(".coupon-applied").text('')
          result.msg.plans.forEach((val, key)=>{
            plans.set('coupon', code)
            console.log('val = ')
            console.log(val);
            var discount = +parseFloat(val['price'] - val['discounted_price']).toFixed(2);
            total_discount = +parseFloat(total_discount) +parseFloat(discount);
            total_amount = (+parseFloat(total_amount) + +(parseFloat(val['discounted_price']))).toFixed(2);
            console.log('total discount='+ total_discount);
            if(val['discount_applied']){
              plans.set(val['plan_id'], new plan(val['plan_id'], val['price'], val['discounted_price']))
              $('#coupon-applied'+val['plan_id']).text(`Coupon discount: ₹${discount}`);
              if(val['is_psychologist']){
                $('#coupon-applied-psychologist').text(`Coupon discount: ₹${discount}`);
                $('button[data-package-name="HappiTALK"]').text('Added');
              }
            }
            $('button[data-plan_id="'+val['plan_id']+'"]').data('discount_amount', val['discounted_price'])
          })
          $("#total_amount").html(total_amount);
          $("#total_amount").data('total', total_amount);
          coupon_applied = true;
          console.log(total_discount)
          discount_price = total_discount;
          $('#coupontext').text('Coupon Applied');
          $('input[name="coupon_code"]').val(coupon_id);
          $('.coupon-applied-text').css("display","inline");
          $("#coupon-applied-here").text(`You have saved additional ₹${total_discount.toFixed(2)}`);
          $("#coupon-applied-here").data('total-discount', total_discount.toFixed(2));
          $("#discountcoupon").modal('hide');
          console.log(result);
        }else{
          $('#coupon-error-msg').html(result.msg)
          $('#coupon_submit').text('Ok');
        }
        console.log(result);
      },
      error: function(err){
        console.log(err);
      }
    });
  }
})
function removeCoupon(){
  $('input[name="coupon_code"]').val('')
  var p = $("#total_amount").data('actual-amount');
  $("#total_amount").html(p);
  $("#total_amount").data('total', p);
  $('#coupontext').text('');
  $('.coupon-applied-text').css("display","none");
  $(".coupon-applied").text('');
  $('button[data-discount_amount]').each((key,val)=>{
    $(val).data('discount_amount',$(val).data('amount'));
  })
  plans.delete('code');
  plans.forEach((val, key) => {
    val.discount_price = val.amount
  })
  coupon_applied = false;
  discount_price = 0;
  discount_value ={};
  plan_applied = {}
}

function storePlans() {
  localStorage.setItem('previous_payment_page', location.href)
  localStorage.setItem('cart', JSON.stringify([...plans.entries()]))
}

function fetchPlans() {
  var cart = JSON.parse(localStorage.getItem('cart'))
  if(cart) {
    cart.forEach((item, index) => {
      if(item[0] != 'coupon'){
        $('button[data-plan_id="'+item[1].plan_id+'"]').trigger('click')
      }

    })
  }
  setPsychologist();

}

function setPsychologist() {
  var psychologist = JSON.parse(localStorage.getItem('psychologist'));
  if(psychologist) {
    psychologist = psychologist[0][1]
    $('#psychologist_plan_id').val(psychologist.psychologist_plan_id)
    $('#psychologist_session').val(psychologist.psychologist_sessions)
    $('#psychologist_id').val(psychologist.psychologist_id)
    $('#psychologist_price_default_text').hide()
    $('#psychologist_price_custom_text').html('<b>Rs. '+psychologist.psychologist_price+'</b>')
    $('#psychologist_session_text').html(psychologist.psychologist_sessions+' sessions')
    plans.set(psychologist.psychologist_plan_id, new plan(psychologist.psychologist_plan_id, psychologist.psychologist_price, psychologist.psychologist_price))
    $('button[data-package-name="HappiTALK"]').addClass("bundles__content__paymentoptions__price__addbtn__add--added");
    $('button[data-package-name="HappiTALK"]').text('Added');
    $('button[data-package-name="HappiTALK"]').data('amount',psychologist.psychologist_price);
    $('button[data-package-name="HappiTALK"]').data('plan_id',psychologist.psychologist_plan_id);
    var amount_exists = parseFloat($("#total_amount").data('total'));
      var amount_this = parseFloat(psychologist.psychologist_price);
      var total = parseFloat(amount_exists + amount_this).toFixed(2);
      $("#total_amount").html(total);
      $('#total_amount').data('total', total);
      $('#total_amount').data('actual-amount', total);
      $('#proceedToPayButton').removeAttr('disabled')
      showApplyCoupon();
  }
  localStorage.removeItem('psychologist');
}

function unsetPsychologist() {
  $('#psychologist_price_custom_text').hide();
  $('#psychologist_session_text').html('per session')
  $('#psychologist_price_default_text').show();
  var plan_id = $('#psychologist_plan_id').val()
  $('#psychologist_plan_id').val('')
  $('#psychologist_session').val('')
  $('#psychologist_id').val('')
  plans.delete(plan_id)
  $('button[data-package-name="HappiTALK"]').removeClass("bundles__content__paymentoptions__price__addbtn__add--added");
  $('button[data-package-name="HappiTALK"]').data('amount','0');
  $('button[data-package-name="HappiTALK"]').data('plan_id','0');
}
function showApplyCoupon() {
  $('#apply-coupon').show();
}
function hideApplyCoupon() {
  $('#apply-coupon').hide();
}
function clearCart() {
  localStorage.removeItem('psychologist');
  localStorage.removeItem('cart');
}

$(document).ready(function(){
  fetchPlans();
})
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection