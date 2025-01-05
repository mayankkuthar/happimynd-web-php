@extends('layouts.app')

@section('title', 'Happimynd | Payment Bundles')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.header')
  @include('Frontend.includes.popups.loginsuccesspopup')
  @include('Frontend.includes.popups.commingsoon')
  <div class="sychologist__page">
      <div class="container">
        <div class="sychologist">
          <div class="sychologist__search">
            <div>
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17.5 17.5L13.875 13.875" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <input type="text" placeholder="Search by Name, City, Specialization or Language" id="search-text"/>
            <div class="sychologist__search__btn">
              <button type="button" onclick="search()">Search</button>
            </div>
          </div>
          <div class="sychologist__dropdowns">
            <div class="sychologist__dropdowns__content">
              <div class="dropdown">
                <button class="dropdown-toggle sychologist__dropdowns__select sychologist__dropdowns__select--expert" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Expert Category:<span>All</span>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCategory('All')">All</a>
                  @foreach($expertLevels as $expertLevel)
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCategory('{{ $expertLevel->name }}')">{{ ucwords($expertLevel->name) }}</a>
                  @endforeach
                  {{-- <a class="dropdown-item" href="javascript:void(0);" onclick="selectCategory('Premium Experts Psychologist')">Premium Experts Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCategory('Clinical Psychologist (RCI Registered)')">Clinical Psychologist (RCI Registered)</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCategory('Clinical Psychologist')">Clinical Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCategory('Counselor Psychologist')">Counselor Psychologist</a> --}}
                </div>
              </div>
            </div>
            <div class="sychologist__dropdowns__content">
              <div class="dropdown">
                <button class="dropdown-toggle sychologist__dropdowns__select sychologist__dropdowns__select--specialization" role="button" id="dropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Specialization:<span>All</span>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectSpecialization('All')">All</a>
                  @foreach($specializations as $specialization)
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectSpecialization('{{ $specialization->name }}')">{{ ucwords($specialization->name) }}</a>
                  @endforeach
                  {{-- <a class="dropdown-item" href="javascript:void(0);" onclick="selectSpecialization('Premium Experts Psychologist')">Premium Experts Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectSpecialization('Clinical Psychologist (RCI Registered)')">Clinical Psychologist (RCI Registered)</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectSpecialization('Clinical Psychologist')">Clinical Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectSpecialization('Counselor Psychologist')">Counselor Psychologist</a> --}}
                </div>
              </div>
            </div>
            <div class="sychologist__dropdowns__content">
              <div class="dropdown">
                <button class="dropdown-toggle sychologist__dropdowns__select sychologist__dropdowns__select--language" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Language:<span>All</span>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectLanguage('All')">All</a>
                  @foreach($languages as $language)
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectLanguage('{{ $language->name }}')">{{ ucwords($language->name) }}</a>
                  @endforeach
                  {{-- <a class="dropdown-item" href="javascript:void(0);" onclick="selectLanguage('Premium Experts Psychologist')">Premium Experts Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectLanguage('Clinical Psychologist (RCI Registered)')">Clinical Psychologist (RCI Registered)</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectLanguage('Clinical Psychologist')">Clinical Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectLanguage('Counselor Psychologist')">Counselor Psychologist</a> --}}
                </div>
              </div>
            </div>
            <div class="sychologist__dropdowns__content">
              <div class="dropdown">
                <button class="dropdown-toggle sychologist__dropdowns__select sychologist__dropdowns__select--city" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  City:<span>All</span>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCity('All')">All</a>
                  @foreach($cities as $city)
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCity('{{ $city->name }}')">{{ ucwords($city->name) }}</a>
                  @endforeach
                  {{-- <a class="dropdown-item" href="javascript:void(0);" onclick="selectCity('Premium Experts Psychologist')">Premium Experts Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCity('Clinical Psychologist (RCI Registered)')">Clinical Psychologist (RCI Registered)</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCity('Clinical Psychologist')">Clinical Psychologist</a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="selectCity('Counselor Psychologist')">Counselor Psychologist</a> --}}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if(sizeof($psychologists)>0)
        <form action="{{ route('payment.bookPsychologist') }}" method="get" id="form">

          @if(auth('user')->user() != null)
          <input type="hidden" name="user_id" value="{{auth('user')->user()->id}}">
          @else
          <input type="hidden" name="user_id" value="0">
          @endif

          <input type="hidden" id="couponid" name="coupon_id" value="">
        <div id="psychologists_section">
        @foreach($psychologists as $psychologist)
        <div class="bundles__content bundles__content__psychologist">
          <div class="bundles__content__psychologistoptions">
            <div class="row">
              <div class="col-lg-6">
                <div class="bundles__content__psychologist__content">
                  <div class="bundles__content__psychologist__img">
                    <img src="{{ $psychologist->s3ImageUrl }}" alt="{{ $psychologist->full_name }}">
                  </div>
                  <div>
                    <div class="bundles__content__psychologist__text">
                      <h1>{{ $psychologist->full_name }}</h1>
                      <p>{{ $psychologist->expertLevel->name }}</p>
                      <span class="bundles__content__psychologist__text__features">Specialization: <span >{{ ucwords($psychologist->printSpecializations()) }}</span></span>
                      <span class="bundles__content__psychologist__text__features">Language: <span >{{ $psychologist->printLanguages() }}</span></span>
                    </div>
                    <div class="bundles__content__psychologistoptions__timedesc bundles__content__psychologistoptions__timedesc--lg6" id="bundles__content__psychologistoptions__timedesc--lg6{{ $loop->iteration }}">
                      <div>
                       @if(isset($psychologist->slot1))
                        <p>
                          <span class="bundles__content__psychologistoptions__time">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M6 11C8.76142 11 11 8.76142 11 6C11 3.23858 8.76142 1 6 1C3.23858 1 1 3.23858 1 6C1 8.76142 3.23858 11 6 11Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M6 3V6L8 7" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                              {{ $psychologist->slot1['time'] ?? "" }}
                            </span>
                          </span>
                          <span>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M8 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M4 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M1.5 5H10.5" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                              {{ $psychologist->slot1['days'] ?? "" }}
                            </span>
                          </span>
                        </p>
                        @if(isset($psychologist->slot2))
                        <p>
                          <span class="bundles__content__psychologistoptions__time">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M6 11C8.76142 11 11 8.76142 11 6C11 3.23858 8.76142 1 6 1C3.23858 1 1 3.23858 1 6C1 8.76142 3.23858 11 6 11Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M6 3V6L8 7" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                              {{ $psychologist->slot2['time'] ?? "" }}
                            </span>
                          </span>
                          <span>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M8 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M4 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M1.5 5H10.5" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                              {{ $psychologist->slot2['days'] ?? "" }}
                            </span>
                          </span>
                        </p>
                        @endif
                        @endif
                        <p class="bundles__content__psychologistoptions__desc">
                          <span class="d-flex align-items-start">
                            <span>
                              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 1H3C2.73478 1 2.48043 1.10536 2.29289 1.29289C2.10536 1.48043 2 1.73478 2 2V10C2 10.2652 2.10536 10.5196 2.29289 10.7071C2.48043 10.8946 2.73478 11 3 11H9C9.26522 11 9.51957 10.8946 9.70711 10.7071C9.89464 10.5196 10 10.2652 10 10V4L7 1Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 1V4H10" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 6.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 8.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 4.5H4.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            </span>
                            <span class="psychologist_summary">
                              {{ $psychologist->summary }}
                            </span>
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="bundles__content__paymentoptions__price bundles__content__psychologist__book" id = "bundles__content__psychologist__book{{ $loop->iteration }}">
                  <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                      <div class="bundles__content__paymentoptions__price__text">
                        <h2>
                          Starting from
                          <b>Rs.{{ $psychologist->getMinimumSessionPrice() }}</b>
                        </h2>
                        <span>Per Session</span>
                        {{-- Show and hide this h4 tag when coupon applied or not --}}
                        {{-- <h4 class="coupon-applied">Coupon discount: ₹99</h4> --}}
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="bundles__content__paymentoptions__price__addbtn">
                        <button class="bundles__content__paymentoptions__price__addbtn__add--added bundles__content__psychologist__book__btn" id="pricing_{{ $loop->iteration }}" data-id={{ $loop->iteration }} type="button">Book</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="bundles__content__paymentoptions__price bundles__content__psychologist__pricing" id="bundles__content__psychologist__pricing{{ $loop->iteration }}" >
                  @foreach($psychologist->getPsychologistPlans() as $plan)
                  <div class="row bundles__content__psychologist__pricing__paddingtop">
                    <div class="col-lg-9 col-md-8 col-sm-8">
                      <div class="bundles__content__paymentoptions__price__text">
                        <h2>
                          <span>Rs. {{ $plan->getCostPrice() }}</span>
                          <b>Rs. {{ $plan->getPerSessionSellingPrice() }}</b>
                          &nbsp; per session
                        </h2>
                        <span>{{ $plan->getSessionSellingPrice() }} for {{ $plan->printDuration() }}</span>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-4">
                      <div class="bundles__content__paymentoptions__price__addbtn">
                        <input type="checkbox" name="plan_id" value={{$plan->id}} id="plan_hidden_checkbox_{{$plan->id}}" data-psychologist-id="{{ $psychologist->id }}" hidden/>
                        <input type="checkbox" name="psychologist_id" value={{$psychologist->id}} id="psychologist_hidden_checkbox_{{$plan->id}}" data-psychologist-id="{{ $psychologist->id }}" hidden/>
                        <button
                          class="bundles__content__paymentoptions__price__addbtn__add"
                          id="{{$psychologist->id}}addbtn_{{$loop->iteration}}"
                          data-amount="{{ $plan->getSessionSellingPrice() }}"
                          data-psychologist-id="{{ $psychologist->id }}"
                          data-plan_id = "{{$plan->id}}"
                          data-plan_checkbox_id="plan_hidden_checkbox_{{$plan->id}}"
                          data-psychologist_checkbox_id="psychologist_hidden_checkbox_{{$plan->id}}"
                          data-psychologist-session="{{ $plan->getSessionDuration() }}"
                          type="button"
                        >Add</button>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              <div class="bundles__content__psychologistoptions__timedesc bundles__content__psychologistoptions__timedesc--lg12" id="bundles__content__psychologistoptions__timedesc--lg12{{ $loop->iteration }}">
                <div>
                  <p>
                    <span class="bundles__content__psychologistoptions__time">
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 11C8.76142 11 11 8.76142 11 6C11 3.23858 8.76142 1 6 1C3.23858 1 1 3.23858 1 6C1 8.76142 3.23858 11 6 11Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 3V6L8 7" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>
                        {{ $psychologist->slot1['time'] ?? "" }}
                      </span>
                    </span>
                    <span>
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.5 5H10.5" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>
                        {{ $psychologist->slot1['days'] ?? "" }}
                      </span>
                    </span>
                  </p>
                  <p class="bundles__content__psychologistoptions__desc">
                    <span class="d-flex align-items-start">
                      <span>
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M7 1H3C2.73478 1 2.48043 1.10536 2.29289 1.29289C2.10536 1.48043 2 1.73478 2 2V10C2 10.2652 2.10536 10.5196 2.29289 10.7071C2.48043 10.8946 2.73478 11 3 11H9C9.26522 11 9.51957 10.8946 9.70711 10.7071C9.89464 10.5196 10 10.2652 10 10V4L7 1Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M7 1V4H10" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M8 6.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M8 8.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M5 4.5H4.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </span>
                      <span class="psychologist_summary">
                        {{ $psychologist->summary }}
                      </span>
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      </form>
      <div class="no_results_found" style="display:none;">
        <div class="no_results_found__img">
          <img src="{{ url('/assets/Frontend/') }}/images/no_result.svg" alt="noresult">
        </div>
        <h1>We couldn’t find any matches</h1>
        <p>Please try another keyword!</p>
       </div>
      @else
       <div class="no_results_found">
        <div class="no_results_found__img">
          <img src="{{ url('/assets/Frontend/') }}/images/no_result.svg" alt="noresult">
        </div>
        <h1>We couldn’t find any matches</h1>
        <p>Please try another keyword!</p>
       </div>
      @endif

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
                </div>
                <h4 class="coupon-applied"></h4>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 mt-md-0 mt-2">
              <div class="d-flex align-items-center justify-content-start justify-content-md-end">
                <div>
                  <h1>Total amount: <span id="total_amount" data-total="0">0</span></h1>
                  {{-- Apply coupon btn --}}
                  {{-- <button type="button" class="apply-coupon" onclick="showApplyCouponPopup()" >Apply Coupon</button> --}}
                </div>
                <button type="submit" id="proceedToPayButton" disabled=true onclick="$('#proceedToPayButton').html('<div class=\'btn__loader1\'></div>');">Proceed</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @include('Frontend.includes.popups.discount_coupon')
  </div>
  @endsection
  @section('js')
  <script>
    var psychologist = new Map();
    function psychologistDetail(psychologist_id, psychologist_sessions, psychologist_price, plan_id) {
      this.psychologist_id = psychologist_id
      this.psychologist_sessions = psychologist_sessions
      this.psychologist_price = psychologist_price
      this.psychologist_plan_id = plan_id
    }
    $('#proceedToPayButton').click(function(){
      // var url = "{{ route('payment.bookPsychologist') }}";
      // $('#form').submit();
      storeSelectedPsychologist();
      if(localStorage.getItem('previous_payment_page'))
        location.href = localStorage.getItem('previous_payment_page');
      else{
        location.href = "{{ route('user.subscribedServices') }}";
      }
    })


    let selectflag = false;
    let selectEle;
    let plan_id = "";
    function bookinghandler(){
      if(selectflag){
        $(".bundles__content__paymentoptions__price__addbtn__add").not('#'+selectEle).attr('disabled', true);
        $('#'+selectEle).addClass("bundles__content__paymentoptions__price__addbtn__add--added");
        $('#'+selectEle).text("Added");
      }

      $(".bundles__content__paymentoptions__price__addbtn__add").click(function(){
      if($(this).hasClass("bundles__content__paymentoptions__price__addbtn__add--added")){  // if packages remove
        $(this).removeClass("bundles__content__paymentoptions__price__addbtn__add--added");
        $(this).text("Add");
        if(psychologist.has($(this).data('psychologist-id'))){
          psychologist.delete($(this).data('psychologist-id'));
        }
        plan_id = '';
        $(".bundles__content__paymentoptions__price__addbtn__add").not(".disabled").removeAttr('disabled');
        var amount_exists = $("#total_amount").data('total');
        var amount_this = $(this).data('amount');
        if(coupon_applied){
          $("#total_amount").html(0);
          $('#total_amount').data('total', 0);
          $('#coupontext').text('Coupon Applied');
          $('.coupon-applied-text').css("display","none");
          $(".coupon-applied").text(``);
        }else{
          $("#total_amount").html(amount_exists - amount_this);
          $('#total_amount').data('total', amount_exists - amount_this);
        }

        // Uncheck the checkbox
        $("#"+$(this).data('plan_checkbox_id')).prop('checked', false);
        $("#"+$(this).data('psychologist_checkbox_id')).prop('checked', false);
        selectflag = false;
        selectEle = null;
      }else{ // if package added
        $(this).addClass("bundles__content__paymentoptions__price__addbtn__add--added");
        $(this).text("Added");
        psychologist.set($(this).data('psychologist-id'), new psychologistDetail($(this).data('psychologist-id'), $(this).data('psychologist-session'),  $(this).data('amount'), $(this).data('plan_id')))
        $(".bundles__content__paymentoptions__price__addbtn__add").not(this).attr('disabled', true)
        var amount_exists = parseFloat($("#total_amount").data('total'));
        var amount_this = parseFloat($(this).data('amount'));
        $("#total_amount").html(amount_exists+amount_this);
        $('#total_amount').data('total', amount_exists + amount_this);

        // check the checkbox
        $("#"+$(this).data('plan_checkbox_id')).prop('checked', true);
        $("#"+$(this).data('psychologist_checkbox_id')).prop('checked', true);
        selectflag = true;
        selectEle = $(this).attr('id');
        plan_id = $(this).data('plan_id');
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
    }
    bookinghandler();
  </script>
  <script>
    let page = 1;
    let loading = false;
    let allPsychologist = false;
    let coupon_applied = false;
    let total_amount = $("#total_amount").data('total');
    let coupon_id = '';
    $("#search-text").keyup(function(e){
      if(e.keyCode == '13'){
        search();
      }
    });
    $(function () {
        // Pagination
        var paginationToggle = false;
        $(window).bind('scroll', chk_scroll);

        function chk_scroll() {
          if (Math.abs($(document).height() - (Math.round($(window).scrollTop())+$(window).height())) <= 10) {
            if(loading) {
              return;
            }
            loadMorePsychologist();
          }
        }
    })
    let item = {!! json_encode($testData) !!}
    //console.log(item)
    item=item.length
    function getParameterByName(name, url = window.location.href) {
      name = name.replace(/[\[\]]/g, '\\$&');
      var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
      if (!results) return "";
      if (!results[2]) return '';
      return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
    function renderPsychoHtml(psychologist){
      var html="";
      html+=`
      <div class="bundles__content__psychologistoptions">
      <div class="row">
      <div class="col-lg-6">
                <div class="bundles__content__psychologist__content">
                  <div class="bundles__content__psychologist__img">
                    <img src="${psychologist.profile_picture_url}" alt="${psychologist.full_name }">
                  </div>
                  <div>
                    <div class="bundles__content__psychologist__text">
                      <h1>${ psychologist.full_name }</h1>
                      <p>${ psychologist.expert_level.name }</p>
                      <span class="bundles__content__psychologist__text__features">Specialization: <span >${ psychologist.specialization }</span></span>
                      <span class="bundles__content__psychologist__text__features">Language: <span >${ psychologist.languages }</span></span>
                    </div>
                    <div class="bundles__content__psychologistoptions__timedesc bundles__content__psychologistoptions__timedesc--lg6" id="bundles__content__psychologistoptions__timedesc--lg6${item}">
                      <div>
                        <p>
                          <span class="bundles__content__psychologistoptions__time">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M6 11C8.76142 11 11 8.76142 11 6C11 3.23858 8.76142 1 6 1C3.23858 1 1 3.23858 1 6C1 8.76142 3.23858 11 6 11Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M6 3V6L8 7" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                              ${ (psychologist.slot1 && psychologist.slot1['time'] )?  psychologist.slot1['time'] :"" }
                            </span>
                          </span>
                          <span>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M8 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M4 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M1.5 5H10.5" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                              ${ (psychologist.slot1 && psychologist.slot1['days']) ? psychologist.slot1['days'] :"" }
                            </span>
                          </span>
                        </p>`
                        if(psychologist.slot2){
                          html +=`<p>
                    <span class="bundles__content__psychologistoptions__time">
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 11C8.76142 11 11 8.76142 11 6C11 3.23858 8.76142 1 6 1C3.23858 1 1 3.23858 1 6C1 8.76142 3.23858 11 6 11Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 3V6L8 7" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>
                        ${(psychologist.slot2 && psychologist.slot2['time'] )? psychologist.slot2['time']:"" }
                      </span>
                    </span>
                    <span>
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.5 5H10.5" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>
                        ${ (psychologist.slot2 && psychologist.slot2['days']) ?  psychologist.slot2['days']:"" }
                      </span>
                    </span>
                  </p>`
                  }
                    html +=`<p class="bundles__content__psychologistoptions__desc">
                          <span class="d-flex align-items-start">
                            <span>
                              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 1H3C2.73478 1 2.48043 1.10536 2.29289 1.29289C2.10536 1.48043 2 1.73478 2 2V10C2 10.2652 2.10536 10.5196 2.29289 10.7071C2.48043 10.8946 2.73478 11 3 11H9C9.26522 11 9.51957 10.8946 9.70711 10.7071C9.89464 10.5196 10 10.2652 10 10V4L7 1Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 1V4H10" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 6.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 8.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 4.5H4.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            </span>
                            <span class="psychologist_summary" >
                              ${ psychologist.summary }
                            </span>
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="bundles__content__paymentoptions__price bundles__content__psychologist__book" id = "bundles__content__psychologist__book${item}">
                  <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                      <div class="bundles__content__paymentoptions__price__text">
                        <h2>
                          Starting from
                          <b>Rs.${ psychologist.minimum_session_price }</b>
                        </h2>
                        <span>Per Session</span>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="bundles__content__paymentoptions__price__addbtn">
                        <button class="bundles__content__paymentoptions__price__addbtn__add--added bundles__content__psychologist__book__btn" id="pricing_${item}" data-id=${item} type="button">Book</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="bundles__content__paymentoptions__price bundles__content__psychologist__pricing" id="bundles__content__psychologist__pricing${item}" >`
                for( var key in psychologist.plans){
                  html+=`<div class="row bundles__content__psychologist__pricing__paddingtop">
                    <div class="col-lg-9 col-md-8 col-sm-8">
                      <div class="bundles__content__paymentoptions__price__text">
                        <h2>
                          <span>Rs. ${ psychologist.plans[key].cost_price }</span>
                          <b>Rs. ${psychologist.plans[key].session_selling_price }</b>
                          &nbsp; per session
                        </h2>
                        <span>${psychologist.plans[key].session_selling_price } for ${ psychologist.plans[key].print_duration }</span>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-4">
                      <div class="bundles__content__paymentoptions__price__addbtn">
                        <input type="checkbox" name="plan_id" value=${psychologist.plans[key].id} id="plan_hidden_checkbox_${psychologist.plans[key].id}" data-psychologist-id="${ psychologist.id }" hidden/>
                        <input type="checkbox" name="psychologist_id" value=${psychologist.id} id="psychologist_hidden_checkbox_${psychologist.plans[key].id}" data-psychologist-id="${ psychologist.id }" hidden/>
                        <button class="bundles__content__paymentoptions__price__addbtn__add" id="${psychologist.id}addbtn_${key-1}" data-amount="${ psychologist.plans[key].session_selling_price }"  data-plan_id ="${psychologist.plans[key].id}" data-plan_checkbox_id="plan_hidden_checkbox_${psychologist.plans[key].id}" data-psychologist_checkbox_id="psychologist_hidden_checkbox_${psychologist.plans[key].id}" data-psychologist-id="${ psychologist.id }" data-psychologist-session="${psychologist.plans[key].print_duration}" type="button">Add</button>
                      </div>
                    </div>
                  </div>`
                }

              html+=`</div>
              </div>
              <div class="bundles__content__psychologistoptions__timedesc bundles__content__psychologistoptions__timedesc--lg12" id="bundles__content__psychologistoptions__timedesc--lg12${item}">
                <div>
                  <p>
                    <span class="bundles__content__psychologistoptions__time">
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 11C8.76142 11 11 8.76142 11 6C11 3.23858 8.76142 1 6 1C3.23858 1 1 3.23858 1 6C1 8.76142 3.23858 11 6 11Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 3V6L8 7" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>
                        ${(psychologist.slot1 && psychologist.slot1['time'] )? psychologist.slot1['time']:"" }
                      </span>
                    </span>
                    <span>
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 1V3" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.5 5H10.5" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>
                        ${ (psychologist.slot1 && psychologist.slot1['days']) ?  psychologist.slot1['days']:"" }
                      </span>
                    </span>
                  </p>
                  <p class="bundles__content__psychologistoptions__desc">
                    <span class="d-flex align-items-start">
                      <span>
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M7 1H3C2.73478 1 2.48043 1.10536 2.29289 1.29289C2.10536 1.48043 2 1.73478 2 2V10C2 10.2652 2.10536 10.5196 2.29289 10.7071C2.48043 10.8946 2.73478 11 3 11H9C9.26522 11 9.51957 10.8946 9.70711 10.7071C9.89464 10.5196 10 10.2652 10 10V4L7 1Z" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M7 1V4H10" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M8 6.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M8 8.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M5 4.5H4.5H4" stroke="#6D7488" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </span>
                      <span class="psychologist_summary">
                        ${psychologist.summary }
                      </span>
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          `
      return html
    }
    function generatePsychologishtml(psychologists){
      psychologists.forEach(function (psychologist) {
        item+=1;
        var articleEl = $(`<div class="bundles__content bundles__content__psychologist"></div>`);
        articleEl.html(renderPsychoHtml(psychologist));
        $("#psychologists_section").append(articleEl);
      });
      $(".bundles__content__psychologist__book__btn").on('click', function() {
      var element = $(this);
      id = $(this).data('id');
      // $("#pricing_"+id).hide();
      $("#bundles__content__psychologist__book"+id).hide();
      $("#bundles__content__psychologist__pricing"+id).show();
      $("#bundles__content__psychologistoptions__timedesc--lg12"+id).hide();
      $("#bundles__content__psychologistoptions__timedesc--lg6"+id).show();
     });
     $(".bundles__content__paymentoptions__price__addbtn__add").unbind();
     bookinghandler();
     if(psychologists.length == 0){
      allPsychologist = true;
     }
     if(item == 0){
       $(".no_results_found").css('display', 'block');
     }
    }
    function loadMorePsychologist(){
      var data={};
      var query = window.location.search;
      const params = new URLSearchParams(query);
      for(var k of params.entries()){
        data[k[0]] = k[1]
      }
      data['_token'] = $('meta[name="csrf-token"]').attr('content')
      data['page_number'] = page
      console.log(data);
      if(!allPsychologist){
        $(".no_results_found").css('display', 'none');
        $.ajax({
          type: "GET",
          url: "{{ route("getPsychologists") }}",
          data: data,
          success: function(data)
          {
            loading = false
            data=JSON.parse(data);
            //console.log(data);
            generatePsychologishtml(data.psychologists)
            page++;
            }
        });
        loading = true
      }
    }
    function search() {
      var shouldSearch = false;
      var search_query = $('#search-text').val().trim();
      if(search_query){
        var expert_category = $('.sychologist__dropdowns__select--expert.selected').text().trim()
        var specialization = $('.sychologist__dropdowns__select--specialization.selected').text().trim()
        var language = $('.sychologist__dropdowns__select--language.selected').text().trim()
        var city = $('.sychologist__dropdowns__select--city.selected').text().trim()
        search_link = "{{ route('user.psychologist') }}?";
        var url = window.location.pathname;
        var query = window.location.search;
        const params = new URLSearchParams(query)
        params.set('search', search_query);
        location.href=`${url}?${params.toString()}`;
      }
    }
    $(document).ready(function(){
      is_filtered_page = true;
      var search_query = getParameterByName('search').trim();
      var expert_category = getParameterByName('expert_category').trim();
      var specialization = getParameterByName('specialization').trim();
      var language = getParameterByName('language').trim();
      var city = getParameterByName('city').trim();
      $('#search-text').val(search_query);
      selectCategory(expert_category);
      selectSpecialization(specialization)
      selectCity(city)
      selectLanguage(language)
    })

    $(".bundles__content__psychologist__book__btn").on('click', function() {
      var element = $(this);
      id = $(this).data('id');
      // $("#pricing_"+id).hide();
      $("#bundles__content__psychologist__book"+id).hide();
      $("#bundles__content__psychologist__pricing"+id).show();
      $("#bundles__content__psychologistoptions__timedesc--lg12"+id).hide();
      $("#bundles__content__psychologistoptions__timedesc--lg6"+id).show();
    });
    function changeQueryParams(f,query,val){
      const params = new URLSearchParams(query)
      const params_temp= new URLSearchParams()
      params_temp.set('expert_category','')
      params_temp.set('specialization','')
      params_temp.set('language', '')
      params_temp.set('city', '')
      if(params.has('expert_category')){
        params_temp.set('expert_category', params.get('expert_category'))
      }
      if(params.has('specialization')){
        params_temp.set('specialization', params.get('specialization'))
      }
      if(params.has('language')){
        params_temp.set('language', params.get('language'))
      }
      if(params.has('city')){
        params_temp.set('city', params.get('city'))
      }
      switch(f){
        case 1:
          params_temp.set('expert_category', val);
          break;
        case 2:
          params_temp.set('specialization', val);
          break;
        case 3:
          params_temp.set('language', val);
          break;
        case 4:
          params_temp.set('city', val);
          break;
      }
      //var params1=params_temp
      const params_temp1= new URLSearchParams()
      if( $('#search-text').val()){
        params_temp1.set('search' , $('#search-text').val())
      }
      for(var k of params_temp.entries()){
        if(k[1]){
          params_temp1.set(k[0], k[1])
        }
      }
      return params_temp1

    }
    function selectCategory(val) {
      if(val == '')
        return;
      var url = window.location.pathname;
      var query = window.location.search;
      const params = new URLSearchParams(query)
      allPsychologist = false;
      if(val == "All"){
        $(".sychologist__dropdowns__select--expert").html('Expert Category:<span>All</span>');
        $(".sychologist__dropdowns__select--expert").removeClass('selected');
        if(params.has('expert_category')){
          params.delete('expert_category');
          window.history.replaceState(null, null, `${url}${params.toString()?'?'+params.toString():''}`);
          $("#psychologists_section").html('');
          page = 0;
          item = 0;
          loadMorePsychologist();
        }
        return;
      }
      var params_temp = changeQueryParams(1,query,val)
      window.history.replaceState(null, null, `${url}?${params_temp.toString()}`);
      $(".sychologist__dropdowns__select--expert").html(val);
      $(".sychologist__dropdowns__select--expert").addClass('selected');
      $("#psychologists_section").html('');
      page = 0;
      item = 0;
      loadMorePsychologist();
    }

    function selectSpecialization(val) {
      if(val == '')
      return;
      var url = window.location.pathname;
      var query = window.location.search;
      const params = new URLSearchParams(query)
      allPsychologist = false;
      if(val == "All"){
        $(".sychologist__dropdowns__select--specialization").html('Specialization:<span>All</span>');
        $(".sychologist__dropdowns__select--specialization").removeClass('selected');
        if(params.has('specialization')){
          params.delete('specialization');
          window.history.replaceState(null, null, `${url}${params.toString()?'?'+params.toString():''}`);
          $("#psychologists_section").html('');
          page = 0;
          item = 0;
          loadMorePsychologist();
        }
        return;
      }
      var params_temp = changeQueryParams(2,query,val)
      window.history.replaceState(null, null, `${url}?${params_temp.toString()}`);
      $(".sychologist__dropdowns__select--specialization").html(val);
      $(".sychologist__dropdowns__select--specialization").addClass('selected');
      $("#psychologists_section").html('');
      page = 0;
      item = 0;
      loadMorePsychologist();
    }


    function selectLanguage(val) {
      if(val == '')
        return;
      var url = window.location.pathname;
      var query = window.location.search;
      const params = new URLSearchParams(query)
      allPsychologist = false;
      if(val == "All"){
        $(".sychologist__dropdowns__select--language").html('Language:<span>All</span>');
        $(".sychologist__dropdowns__select--language").removeClass('selected');
        if(params.has('language')){
          params.delete('language');
          window.history.replaceState(null, null, `${url}${params.toString()?'?'+params.toString():''}`);
          $("#psychologists_section").html('');
          page = 0;
          item = 0;
          loadMorePsychologist();
        }
        return;
      }
      var params_temp = changeQueryParams(3,query,val)
      window.history.replaceState(null, null, `${url}?${params_temp.toString()}`);
      $(".sychologist__dropdowns__select--language").html(val);
      $(".sychologist__dropdowns__select--language").addClass('selected');
      $("#psychologists_section").html('');
      page = 0;
      item = 0;
      loadMorePsychologist();
    }

    function selectCity(val) {
      if(val == '')
      return;
      var url = window.location.pathname;
      var query = window.location.search;
      const params = new URLSearchParams(query)
      allPsychologist = false;
      if(val == "All"){
        $(".sychologist__dropdowns__select--city").html('City:<span>All</span>');
        $(".sychologist__dropdowns__select--city").removeClass('selected');
        if(params.has('city')){
          params.delete('city');
          window.history.replaceState(null, null, `${url}${params.toString()?'?'+params.toString():''}`);
          $("#psychologists_section").html('');
          page = 0;
          item = 0;
          loadMorePsychologist();
        }
        return;
      }
      var params_temp = changeQueryParams(4,query,val)
      window.history.replaceState(null, null, `${url}?${params_temp.toString()}`);
      $(".sychologist__dropdowns__select--city").html(val);
      $(".sychologist__dropdowns__select--city").addClass('selected');
      $("#psychologists_section").html('');
      page = 0;
      item = 0;
      loadMorePsychologist();
    }
    function showApplyCouponPopup() {
      $("#discountcoupon").modal('show');
    }
  $(".discountcoupon__popup__content__form").submit(function(e){
  e.preventDefault();
  e.stopPropagation();
  console.log("pop");
  var code = $('#coupon-code').val();
  console.log(code);
  code = code.trim();
  if(code){
    var data = {
      'plan_id':[plan_id],
      'code': code,
    }
    console.log(data)
    $.ajax({
      url: '{{route("user.verify-coupon")}}',
      method: 'get',
      data: data,
      success: function(result) {
        if(!result.error){
          var discount_percent = result.discount_percent;
          coupon_id = result.coupon_id;
          total_amount = $("#total_amount").data('total');
          var discount = parseFloat(((total_amount)*(discount_percent)/100).toFixed(2));
          var amount = total_amount - discount;
          $("#total_amount").html(amount);
          $("#total_amount").data('total', amount);
          coupon_applied = true;
          $('#coupontext').text('Coupon Applied');
          $('#couponid').val(coupon_id);
          $('.coupon-applied-text').css("display","inline");
          $(".coupon-applied").text(`You have saved additional ₹${discount}`);
          $("#discountcoupon").modal('toggle');
          console.log(result);
        }else{
          console.log(result);
          $("#couponerror").html(result.msg);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  }
})
function removeCoupon(){
  $("#total_amount").html(total_amount);
  $("#total_amount").data('total', total_amount);
  $('#coupontext').text('');
  $('.coupon-applied-text').css("display","none");
  $(".coupon-applied").text('');
}

function storeSelectedPsychologist(){
  localStorage.setItem('psychologist', JSON.stringify([...psychologist.entries()]));
}
  </script>
  <script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
  @endsection