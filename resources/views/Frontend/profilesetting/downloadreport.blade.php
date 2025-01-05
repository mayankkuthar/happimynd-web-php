@extends('layouts.app')

@section('title', 'Happimynd | Download Summary')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.dashboard.header')
  @include('Frontend.includes.popups.verification_popups')
  <div class="container">
    <div class="downloadreport">
      <div class="d-flex align-items-start align-items-md-center  justify-content-between flex-column-reverse flex-md-row" style="padding-bottom: 64px">
        <h1>Download HappiLIFE Awareness Tool Summary</h1>
        @if(!auth()->user()->hasSummaryReadingPlan())
          <div class="avail_free_summary">
            @if(auth()->user()->organizationHasSummaryReadingPlan())
            <a href="{{ route('user.subscribedServices') }}" class="downloadreport__content__buttons-view">Avail for free summary reading </a>
            @else
            <a href="{{ route('user.payment.buyBundle') }}" class="downloadreport__content__buttons-download">Buy summary reading</a>
            @endif
          </div>
        @endif
      </div>
      <div class="downloadreport__headings__list">
        <div class="row align-items-md-center">
          <div class="col-lg-2 col-md-2 col-sm-7 col-6">
            <div class="downloadreport__headings">
              <h2>DATE</h2>
            </div>
          </div>
          <div class="col-lg-10 col-md-10 col-sm-5 col-6">
            <div class="row align-items-center downloadreport__content__column">
              <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="downloadreport__headings">
                  <h2>STATUS</h2>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="downloadreport__headings">
                  <h2>EXPIRY</h2>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12">

              </div>
            </div>
          </div>
        </div>
      </div>
      @foreach($user->assessment as $assessment)
        @if(true)
          <div class="downloadreport__content__list downloadreport__content__list1">
            <div class="row align-items-md-center">
              <div class="col-lg-2 col-md-2 col-sm-7 col-6">
                <div class="downloadreport__content__date">
                  <!-- <h3>26 June, 2020 </h3> -->
                  <h3>{{ $assessment->started_at }}</h3>
                </div>
              </div>
              <div class="col-lg-10 col-md-10 col-sm-5 col-6">
                <div class="row align-items-center downloadreport__content__column">
                  <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="downloadreport__content__status">
                      @if($assessment->report)
                        <h3>Downloadable</h3>
                      @elseif(auth('user')->user()->bundleStatus()->where('plan_id',1)->get()->count() == 0)
                        <h3>Pay to Access summary</h3>
                      @else
                      @php $processing=true; @endphp
                        <h3>Processing <img src="{{ asset('assets/Frontend/images/spinner.svg') }}" width="30px" height="30px" /></h3>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="downloadreport__content__expiry">
                     @if($assessment->report)
                        {{ $assessment->expiryDays() }}
                      @else
                        <h3>-</h3>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="downloadreport__content__buttons">
                      <a
                       @if($assessment->report != null && $user->showReport())
                        href="{{ route('calculateAssessmentScore') }}?assessment_id={{$assessment->id}}"
                        @else
                        href=""
                        @endif
                      >
                        <button
                        @if(is_null($assessment->report)  || $user->showReport() == false)
                          disabled
                        @endif
                          class="downloadreport__content__buttons-view"
                          type="button"
                        >View
                        </button>
                      </a>
                      <button
                        class="downloadreport__content__buttons-download"
                        type="button"
                        @if(is_null($assessment->report) || $user->showReport() == false)
                          @if(auth('user')->user()->bundleStatus()->where('plan_id',1)->get()->count() == 0)
                            onclick="sessionStorage.setItem('add-plan', 1);window.location='{{ route('user.payment.buyBundle') }}'"
                            @php
                              $buttonText = "Proceed";
                            @endphp
                          @else
                            disabled
                          @endif
                        @else
                          id="report_download_{{$loop->iteration}}"
                          data-appointment_status = @if($assessment->approve && $assessment->approve->slot!="") "1" @else "0" @endif
                          data-assessment_id="{{$assessment->id}}"
                          onclick="window.open('{{ $assessment->report }}','_blank');"
                        @endif
                      >{{ $buttonText ?? "Download" }}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
      @endforeach
    </div>
  </div>
</div>
@section('js')
<script type="text/javascript">
@if(isset($processing) && $processing)
  setTimeout(function(){
    location.reload();
  },15000)
  @endif
</script>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection
@endsection
