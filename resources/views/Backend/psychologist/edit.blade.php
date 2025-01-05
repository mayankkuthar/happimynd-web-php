<x-backend-layout>
  <x-slot name="title">
    Edit Psychologist
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Edit Psychologist</h2>
                <div class="clearfix"></div>
              </div>


                 @if(Session::has("error"))
                    <div class="alert alert-danger">{{Session::get("error")}}</div>
                    @endif
                    @if(Session::has("Success"))
                    <div class="alert alert-success">{{Session::get("Success")}}</div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                          @foreach ($errors->all() as $error)
                          {{$error}}
                          @endforeach
                    </div>
                  @endif
                      

              <div class="x_content">
                <br>

                <form class="form-horizontal form-label-left" id="add-psychologist-form" enctype="multipart/form-data" method="POST" action="{{ route('admin.psychologist.edit.post') }}">
                  @csrf
                  <input type="hidden" value="{{ $psychologist->id }}" name="id">
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">First Name</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="first name" name="first_name" required value="{{ $psychologist->first_name }}">
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Last Name</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="last name" name="last_name" required value="{{ $psychologist->last_name }}">
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Username</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Username" name="username" required value="{{ $psychologist->username }}">
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Email</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Email" name="email" required value="{{ $psychologist->email }}">
                    </div>
                  </div>





                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Price per session(if session book through Orgnisation) (1-5000)</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Price Per Session" name="price_per_session" value="{{ $psychologist->price_per_session }}" required >
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Given Percentage (1-100%)</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Given Percentage" name="commission_percentage" value="{{ $psychologist->commission_percentage }}" required >
                    </div>
                  </div>


                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">TDS Percentage (1-100%)</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Given Percentage" name="tds_percentage" value="{{ $psychologist->tds_percentage }}" required >
                    </div>
                  </div>





                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Summary <span class="required">*</span>
                    </label>
                    <div class="col-md-9 col-sm-9 ">
                      <textarea class="form-control" rows="3" placeholder="Summary" name="summary" required>{{ $psychologist->summary }}</textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Expert Level</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="form-control" name="expert_level_id" required>
                        <option></option>
                        @foreach($expertLevels as $expertLevel)
                        <option value="{{ $expertLevel->id }}" @if($expertLevel->id == $psychologist->expert_level_id) selected @endif>{{ $expertLevel->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Select Specialization</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="select2_multiple form-control"  multiple="multiple" tabindex="-1" name="specialization_id[]" required>
                        <option></option>
                        @foreach($specializations as $specialization)
                        <option value="{{ $specialization->id }}" @if(in_array($specialization->id, $psychologist->specialization->pluck('id')->toArray())) selected @endif>{{ $specialization->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Select City</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="select2_single form-control" name="city_id" required>
                        <option></option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}" @if($psychologist->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Select Languages</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="select2_multiple form-control" multiple="multiple" name="language_id[]" required>
                        <option></option>
                        @foreach($languages as $language)
                        <option value="{{ $language->id }}"@if(in_array($language->id, $psychologist->language->pluck('id')->toArray())) selected @endif>{{ $language->name }}
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="control-label col-md-3 col-sm-3 ">Psychologist Picture</label>
                      <div class="col-md-9 col-sm-9 ">
                        <input type="file" name="picture" accept="image/*">
                        <img src="{{ $psychologist->s3ImageUrl }}">
                      </div>
                    </div>

                  <input type="hidden" name="expertLevelId" value="{{ $psychologist->expertLevel->id }}">
                  <h3>Pricing</h3>

                  <!-- show prices of offer -->
                  @foreach($durations as $duration)
                  <hr>
                  <input type="hidden" name="plans[{{ $duration->id }}][duration_type_id]" value="{{ $duration->id }}">
                  <div class="field item form-group @error('duration-{{ $duration->id }}-cost-price') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Cost<span class="required"></span></label>
                    <div class="col-md-6 col-sm-6">
                      <input
                        class="form-control"
                        name="plans[{{ $duration->id }}][cost-price]"
                        type="text"
                        placeholder="enter amount"
                        value="{{ $customPlans[$duration->id]->psychologistCustomPrice->cost_price ?? $psychologistPlans[$duration->id]->price ?? ''}}"
                      />
                    </div>
                    @error('duration-{{ $duration->id }}')
                    <div class="alert" id="duration-{{ $duration->id }}-cost-price-error"> {{ $message }}</div>
                    @enderror
                  </div>
                  <div class="field item form-group @error('duration-{{ $duration->id }}-cost-price-discount') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Offer discount(%)<span class="required"></span></label>
                    <div class="col-md-6 col-sm-6">
                      <input
                        class="form-control"
                        name="plans[{{ $duration->id }}][cost-price-discount]"
                        type="text"
                        placeholder="enter amount"
                        value="{{ $customPlans[$duration->id]->psychologistCustomPrice->discount ?? $psychologistPlans[$duration->id]->offer->discount ?? ''}}"
                        />
                    </div>
                    @error('duration-{{ $duration->id }}')
                    <div class="alert" id="duration-{{ $duration->id }}-cost-price-discount-error"> {{ $message }}</div>
                    @enderror
                  </div>

                  <!-- show original prices -->
                  <div class="field item form-group @error('duration-{{ $duration->id }}-selling-price') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Selling Price<span class="required"></span></label>
                    <div class="col-md-6 col-sm-6">
                      <input
                        class="form-control"
                        name="plans[{{ $duration->id }}][selling-price]"
                        type="text"
                        placeholder="enter amount"
                        value="{{ $customPlans[$duration->id]->psychologistCustomPrice->selling_price ?? $psychologistPlans[$duration->id]->selling_price ?? ''}}"/>
                    </div>
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Custom Price <input type="checkbox" name="plans[{{ $duration->id }}][custom-price]" class="flat" @isset($customPlans[$duration->id]) checked @endif ></label>
                    @error('duration-{{ $duration->id }}')
                    <div class="alert" id="duration-{{ $duration->id }}-selling-price-error"> {{ $message }}</div>
                    @enderror
                  </div>
                  @endforeach
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

            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Slots <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-dates-modal" id="add-slots-button">
                    Modify Slots
                  </button></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
                        <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap no-footer">
                          <div class="row">
                            <div class="col-sm-12">
                              <table id="datatable" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;" role="grid" aria-describedby="datatable_info">
                                <thead>
                                  <tr role="row">
                                    <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" style="width: 164px;" aria-sort="ascending">
                                      Date
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 256px;">
                                      Slots
                                    </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($slots as $date => $slot)
                                  <tr role="row" class="odd">
                                    <td class="sorting_1">{{ $date }}({{ \Carbon\Carbon::parse($date)->format('l') }})</td>
                                    <td class="sorting_1">{{ implode(' || ', array_column($slot, 'time')) }}</td>
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
            </div>

            <!-- Modal -->
            <div class="modal fade" id="add-dates-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Session Slots</h5>
              <button type="button" class="close" id="add-dates-modal-close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="container-fluid">
                <form id="slots-form">
                  <div id="section-1">
                    <h5 class="text-center">Section 1</h5>
                    <h5>Select days*</h5>
                    <div class="form-group">
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="MON-1" value="MON" data-week-no=1>
                        <label class="form-check-label" for="MON-1" >
                          MON
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="TUE-1" value="TUE" data-week-no=2>
                        <label class="form-check-label" for="TUE-1" >
                          TUE
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="WED-1" value="WED" data-week-no=3>
                        <label class="form-check-label" for="WED-1" >
                          WED
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="THU-1" value="THU" data-week-no=4>
                        <label class="form-check-label" for="THU-1" >
                          THU
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="FRI-1" value="FRI" data-week-no=5>
                        <label class="form-check-label" for="FRI-1" >
                          FRI
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="SAT-1" value="SAT" data-week-no=6>
                        <label class="form-check-label" for="SAT-1" >
                          SAT
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-1-days-checkbox form-check-input" type="checkbox"name="section[1][days][]" id="SUN-1" value="SUN" data-week-no=0>
                        <label class="form-check-label" for="SUN-1" >
                          SUN
                        </label>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="datefilter-1">Select Date Range*</label>
                      <input type="text" class="form-control" name="section[1][date]" value="" id="datefilter-1" autocomplete="off" required/>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="time">Time*</label>
                      <input type="text" class="form-control" name="section[1][time]" value="" id="time" autocomplete="off" required/>
                    </div>
                    <div class="form-group">
                      <div class="row mt-3">
                        <div class="col-md-6">
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-1" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-1" value="12:00 AM - 1:00 AM"> 12:00 AM - 1:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-2" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-2" value="12:30 AM - 1:30 AM"> 12:30 AM - 1:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-3" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-3" value="1:00 AM - 2:00 AM"> 1:00 AM - 2:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-4" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-4" value="1:30 AM - 2:30 AM"> 1:30 AM - 2:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-5" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-5" value="2:00 AM - 3:00 AM"> 2:00 AM - 3:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-6" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-6" value="2:30 AM - 3:30 AM"> 2:30 AM - 3:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-7" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-7" value="3:00 AM - 4:00 AM"> 3:00 AM - 4:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-8" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-8" value="3:30 AM - 4:30 AM"> 3:30 AM - 4:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-9" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-9" value="4:00 AM - 5:00 AM"> 4:00 AM - 5:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-10" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-10" value="4:30 AM - 5:30 AM"> 4:30 AM - 5:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-11" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-11" value="5:00 AM - 6:00 AM"> 5:00 AM - 6:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-12" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-12" value="5:30 AM - 6:30 AM"> 5:30 AM - 6:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-13" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-13" value="6:00 AM - 7:00 AM"> 6:00 AM - 7:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-14" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-14" value="6:30 AM - 7:30 AM"> 6:30 AM - 7:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-15" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-15" value="7:00 AM - 8:00 AM"> 7:00 AM - 8:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-16" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-16" value="7:30 AM - 8:30 AM"> 7:30 AM - 8:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-17" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-17" value="8:00 AM - 9:00 AM"> 8:00 AM - 9:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-18" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-18" value="8:30 AM - 9:30 AM"> 8:30 AM - 9:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-19" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-19" value="9:00 AM - 10:00 AM"> 9:00 AM - 10:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-20" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-20" value="9:30 AM - 10:30 AM"> 9:30 AM - 10:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-21" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-21" value="10:00 AM - 11:00 AM"> 10:00 AM - 11:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-22" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-22" value="10:30 AM - 11:30 AM"> 10:30 AM - 11:30 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-23" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-23" value="11:00 AM - 12:00 PM"> 11:00 AM - 12:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-24" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-24" value="11:30 AM - 12:30 PM"> 11:30 AM - 12:30 PM
                            </label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-25" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-25" value="12:00 PM - 1:00 PM"> 12:00 PM - 1:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-26" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-26" value="12:30 PM - 1:30 PM"> 12:30 PM - 1:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-27" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-27" value="1:00 PM - 2:00 PM"> 1:00 PM - 2:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-28" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-28" value="1:30 PM - 2:30 PM"> 1:30 PM - 2:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-29" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-29" value="2:00 PM - 3:00 PM"> 2:00 PM - 3:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-30" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-30" value="2:30 PM - 3:30 PM"> 2:30 PM - 3:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-31" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-31" value="3:00 PM - 4:00 PM"> 3:00 PM - 4:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-32" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-32" value="3:30 PM - 4:30 PM"> 3:30 PM - 4:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-33" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-33" value="4:00 PM - 5:00 PM"> 4:00 PM - 5:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-34" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-34" value="4:30 PM - 5:30 PM"> 4:30 PM - 5:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-35" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-35" value="5:00 PM - 6:00 PM"> 5:00 PM - 6:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-36" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-36" value="5:30 PM - 6:30 PM"> 5:30 PM - 6:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-37" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-37" value="6:00 PM - 7:00 PM"> 6:00 PM - 7:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-38" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-38" value="6:30 PM - 7:30 PM"> 6:30 PM - 7:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-39" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-39" value="7:00 PM - 8:00 PM"> 7:00 PM - 8:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-40" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-40" value="7:30 PM - 8:30 PM"> 7:30 PM - 8:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-41" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-41" value="8:00 PM - 9:00 PM"> 8:00 PM - 9:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-42" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-42" value="8:30 PM - 9:30 PM"> 8:30 PM - 9:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-43" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-43" value="9:00 PM - 10:00 PM"> 9:00 PM - 10:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-44" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-44" value="9:30 PM - 10:30 PM"> 9:30 PM - 10:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-45" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-45" value="10:00 PM - 11:00 PM"> 10:00 PM - 11:00 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-46" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-46" value="10:30 PM - 11:30 PM"> 10:30 PM - 11:30 PM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-47" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-47" value="11:00 PM - 12:00 AM"> 11:00 PM - 12:00 AM
                            </label>
                          </div>
                          <div class="form-check plan-checkbox">
                            <label class="form-check-label custom-input-field" for="section-1-slot-48" >
                              <input class ="checkbox-input" type="checkbox"name="section[1][slots][]" id="section-1-slot-48" value="11:30 PM - 12:30 AM"> 11:30 PM - 12:30 AM
                            </label>
                          </div>

                        </div>
                      </div>
                    </div>
                     
                  </div>

                  <div id="section-1">
                    <h5 class="text-center">Section 2</h5>
                    <h6>Select Days*</h6>
                    <div class="form-group">
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="MON-2" value="MON" data-week-no=1>
                        <label class="form-check-label" for="MON-2" >
                          MON
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="TUE-2" value="TUE" data-week-no=2>
                        <label class="form-check-label" for="TUE-2" >
                          TUE
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="WED-2" value="WED" data-week-no=3>
                        <label class="form-check-label" for="WED-2" >
                          WED
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="THU-2" value="THU" data-week-no=4>
                        <label class="form-check-label" for="THU-2" >
                          THU
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="FRI-2" value="FRI" data-week-no=5>
                        <label class="form-check-label" for="FRI-2" >
                          FRI
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="SAT-2" value="SAT" data-week-no=6>
                        <label class="form-check-label" for="SAT-2" >
                          SAT
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class ="section-2-days-checkbox form-check-input" type="checkbox"name="section[2][days][]" id="SUN-2" value="SUN" data-week-no=0>
                        <label class="form-check-label" for="SUN-2" >
                          SUN
                        </label>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="datefilter-2">Select Date Range*</label>
                      <input type="text" class="form-control" name="section[2][date]" value="" id="datefilter-2" autocomplete="off"/>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="time">Time*</label>
                      <input type="text" class="form-control" name="section[2][time]" value="" id="time" autocomplete="off"/>
                    </div>
                    <div class="form-group">
                      <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-1" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-1" value="12:00 AM - 1:00 AM"> 12:00 AM - 1:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-2" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-2" value="12:30 AM - 1:30 AM"> 12:30 AM - 1:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-3" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-3" value="1:00 AM - 2:00 AM"> 1:00 AM - 2:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-4" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-4" value="1:30 AM - 2:30 AM"> 1:30 AM - 2:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-5" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-5" value="2:00 AM - 3:00 AM"> 2:00 AM - 3:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-6" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-6" value="2:30 AM - 3:30 AM"> 2:30 AM - 3:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-7" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-7" value="3:00 AM - 4:00 AM"> 3:00 AM - 4:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-8" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-8" value="3:30 AM - 4:30 AM"> 3:30 AM - 4:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-9" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-9" value="4:00 AM - 5:00 AM"> 4:00 AM - 5:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-10" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-10" value="4:30 AM - 5:30 AM"> 4:30 AM - 5:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-11" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-11" value="5:00 AM - 6:00 AM"> 5:00 AM - 6:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-12" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-12" value="5:30 AM - 6:30 AM"> 5:30 AM - 6:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-13" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-13" value="6:00 AM - 7:00 AM"> 6:00 AM - 7:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-14" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-14" value="6:30 AM - 7:30 AM"> 6:30 AM - 7:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-15" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-15" value="7:00 AM - 8:00 AM"> 7:00 AM - 8:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-16" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-16" value="7:30 AM - 8:30 AM"> 7:30 AM - 8:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-17" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-17" value="8:00 AM - 9:00 AM"> 8:00 AM - 9:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-18" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-18" value="8:30 AM - 9:30 AM"> 8:30 AM - 9:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-19" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-19" value="9:00 AM - 10:00 AM"> 9:00 AM - 10:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-20" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-20" value="9:30 AM - 10:30 AM"> 9:30 AM - 10:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-21" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-21" value="10:00 AM - 11:00 AM"> 10:00 AM - 11:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-22" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-22" value="10:30 AM - 11:30 AM"> 10:30 AM - 11:30 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-23" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-23" value="11:00 AM - 12:00 PM"> 11:00 AM - 12:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-24" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-24" value="11:30 AM - 12:30 PM"> 11:30 AM - 12:30 PM
                              </label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-25" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-25" value="12:00 PM - 1:00 PM"> 12:00 PM - 1:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-26" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-26" value="12:30 PM - 1:30 PM"> 12:30 PM - 1:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-27" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-27" value="1:00 PM - 2:00 PM"> 1:00 PM - 2:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-28" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-28" value="1:30 PM - 2:30 PM"> 1:30 PM - 2:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-29" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-29" value="2:00 PM - 3:00 PM"> 2:00 PM - 3:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-30" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-30" value="2:30 PM - 3:30 PM"> 2:30 PM - 3:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-31" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-31" value="3:00 PM - 4:00 PM"> 3:00 PM - 4:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-32" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-32" value="3:30 PM - 4:30 PM"> 3:30 PM - 4:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-33" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-33" value="4:00 PM - 5:00 PM"> 4:00 PM - 5:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-34" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-34" value="4:30 PM - 5:30 PM"> 4:30 PM - 5:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-35" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-35" value="5:00 PM - 6:00 PM"> 5:00 PM - 6:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-36" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-36" value="5:30 PM - 6:30 PM"> 5:30 PM - 6:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-37" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-37" value="6:00 PM - 7:00 PM"> 6:00 PM - 7:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-38" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-38" value="6:30 PM - 7:30 PM"> 6:30 PM - 7:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-39" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-39" value="7:00 PM - 8:00 PM"> 7:00 PM - 8:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-40" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-40" value="7:30 PM - 8:30 PM"> 7:30 PM - 8:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-41" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-41" value="8:00 PM - 9:00 PM"> 8:00 PM - 9:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-42" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-42" value="8:30 PM - 9:30 PM"> 8:30 PM - 9:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-43" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-43" value="9:00 PM - 10:00 PM"> 9:00 PM - 10:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-44" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-44" value="9:30 PM - 10:30 PM"> 9:30 PM - 10:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-45" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-45" value="10:00 PM - 11:00 PM"> 10:00 PM - 11:00 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-46" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-46" value="10:30 PM - 11:30 PM"> 10:30 PM - 11:30 PM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-47" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-47" value="11:00 PM - 12:00 AM"> 11:00 PM - 12:00 AM
                              </label>
                            </div>
                            <div class="form-check plan-checkbox">
                              <label class="form-check-label custom-input-field" for="section-2-slot-48" >
                                <input class ="checkbox-input" type="checkbox"name="section[2][slots][]" id="section-2-slot-48" value="11:30 PM - 12:30 AM"> 11:30 PM - 12:30 AM
                              </label>
                            </div>
                          </div>
                      </div>
                    </div>
                     
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-slots-button">
                      Save <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinner" style="display: none"></span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
          </x-slot>
          <x-slot name="js">

            <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
      <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
      <script>
        var section_1_days = [];
        var section_2_days = [];
        datePicker('1', section_1_days);
        datePicker('2', section_2_days);
        $('.section-1-days-checkbox').on('change', function(){
          section_1_days = [];

          $('.section-1-days-checkbox:checked').each(function(indx, element){
            console.log(element);
            if(!section_1_days.includes($(element).data('week-no')))
            section_1_days.push($(element).data('week-no'));
          })
          datePicker('1', section_1_days);
        })

        $('.section-2-days-checkbox').on('change', function(){
          section_2_days = [];
          $('.section-2-days-checkbox:checked').each(function(indx, element){
            console.log(element);
            if(!section_2_days.includes($(element).data('week-no')))
            section_2_days.push($(element).data('week-no'));
          })
          datePicker('2', section_2_days);
        })

        function datePicker(id, weeksToEnable) {

          $('#datefilter-'+id).daterangepicker({
            autoUpdateInput: false,
            locale: {
              cancelLabel: 'Clear'
            },
            "opens": "center",
            "drops": "auto",
            isInvalidDate: function(date){
              return !weeksToEnable.includes(date.weekday());
            }
          });

          $('#datefilter-'+id).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
          });

          $('#datefilter-'+id).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
          });

        }
              $('#slots-form').submit(function(e){
                e.preventDefault();
                psychologists = $('.psychologist_checkbox:checked').map(function (indx,element) {
                  return $(element).val()
                })
                psychologists = psychologists.get().join(',')
                console.log($(e.target).serialize())
                $.ajax({
                  url: "{{ route('admin.psychologist.addDates.get') }}?psychologist_ids="+{{ $psychologist->id }},
                  data: $(e.target).serialize(),
                  method: "GET",
                  beforeSend:function(){
                    $('#spinner').show();
                  },
                  complete: function(){
                    $('#spinner').hide();
                  },
                  success: function(response){
                    if(response.error == false){
                      // location.reload()
                    }
                  }
                })
                console.log($(e.target).serialize());
              });
            </script>
          </x-slot>
        </x-backend-layout>
