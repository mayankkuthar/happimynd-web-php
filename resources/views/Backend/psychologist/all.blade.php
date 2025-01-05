<x-backend-layout>
  <x-slot name="title">
    All Psychologist
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2>Psychologists</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-sm-12">
                  <button class="btn btn-primary btn-round" onclick="location.href='{{ route('admin.psychologist.add.get') }}'">Add</button>
                  <button type="button" class="btn btn-primary disabled" data-toggle="modal" data-target="#add-dates-modal" id="add-slots-button" disabled>
                    Add Slots
                  </button>
                  <div class="card-box table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dataTable no-footer dtr-inline" style="width: 100%;" role="grid" aria-describedby="datatable-buttons_info">
                      <thead>
                        <tr role="row">
                          <th class="" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;" aria-label=""># <input type="checkbox" id="check-all"> </th>
                          <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;" aria-label="Name: activate to sort column ascending">Name</th>
                          <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 177px;" aria-label="Position: activate to sort column ascending">Specialization</th>
                          <th class="sorting_desc" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 81px;" aria-label="Office: activate to sort column ascending" aria-sort="descending">Expert Level</th>
                          <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 35px;" aria-label="Age: activate to sort column ascending">Summary</th>
                          <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 75px;" aria-label="Start date: activate to sort column ascending">Languages</th>
                          <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 60px;" aria-label="Salary: activate to sort column ascending">City</th>
                          <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 60px;" aria-label="Salary: activate to sort column ascending">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($psychologists as $psychologist)
                        <tr role="row">
                          <td> <label> <input type="checkbox" class="psychologist_checkbox" id="checkbox-{{$psychologist->id}}" value="{{ $psychologist->id }}"> {{ $psychologist->id }} <img src="{{ $psychologist->s3ImageUrl }}" class="img-thumbnail" alt="Avatar" style="height: 50%;
                            width: 50%;"></label></td>
                            <td class="">{{ $psychologist->full_name }}</td>
                            <td class="">{{ $psychologist->specialization->pluck('name') ?? '-'}}</td>
                            <td>
                              {{ $psychologist->expertLevel->name }}<br><br>
                              @foreach($psychologist->getPsychologistPlans() as $plan)
                              {{ $plan->duration->name ." for ". $plan->selling_price }} <br>
                              @endforeach
                            </td>
                            <td>{{ $psychologist->summary }}</td>
                            <td>{{ $psychologist->language->pluck('name') }}</td>
                            <td>{{ $psychologist->city->name }}</td>
                            <td>
                              <a href="{{ route('admin.psychologist.edit.get', ['id' => $psychologist->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                              <a href="#" class="btn btn-danger btn-xs" onclick="deletePsychologist('{{ route('admin.psychologist.delete.get', ['id' => $psychologist->id]) }}')"><i class="fa fa-trash-o"></i> Delete </a>
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
      <!-- Button trigger modal -->
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
                    <h5>Select days</h5>
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
                      <label class="form-label" for="datefilter-1">Select Date Range</label>
                      <input type="text" class="form-control" name="section[1][date]" value="" id="datefilter-1"/>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="time">Time</label>
                      <input type="text" class="form-control" name="section[1][time]" value="" id="time"/>
                    </div>
                    <div class="form-group">
                      <div class="row">
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
                   

                    <div id="section-1">
                      <h5 class="text-center">Section 2</h5>
                      <h6>Select Days</h6>
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
                        <label class="form-label" for="datefilter-2">Select Date Range</label>
                        <input type="text" class="form-control" name="section[2][date]" value="" id="datefilter-2"/>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="time">Time</label>
                        <input type="text" class="form-control" name="section[2][time]" value="" id="time"/>
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

          function deletePsychologist(url){
            var check = confirm('Confirm to delete');
            if(check){
              location.href=url;
            }
          }
          $('#slots-form').submit(function(e){
            e.preventDefault();
            psychologists = $('.psychologist_checkbox:checked').map(function (indx,element) {
              return $(element).val()
            })
            psychologists = psychologists.get().join(',')
            console.log($(e.target).serialize())
            $.ajax({
              url: "{{ route('admin.psychologist.addDates.get') }}?psychologist_ids="+psychologists,
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
                  $('#add-dates-modal-close').trigger('click');
                }
              }
            })
            console.log($(e.target).serialize());
          });

          $('.psychologist_checkbox').on('change', function(){
            if($('.psychologist_checkbox:checked').length>0){
              $('#add-slots-button').removeClass('disabled');
              $('#add-slots-button').prop('disabled', false);
            }
            else{
              $('#add-slots-button').addClass('disabled');
              $('#add-slots-button').prop('disabled', true);
            }
          })

          $('#check-all').on('change', function(){
            if($('#check-all').is(":checked")){
              $('.psychologist_checkbox').prop('checked', true);
            }
            else if(!$('#check-all').is(":checked")){
              $('.psychologist_checkbox').prop('checked',false);
            }
            $('.psychologist_checkbox').trigger('change');
          })
        </script>
      </x-slot>
    </x-backend-layout>