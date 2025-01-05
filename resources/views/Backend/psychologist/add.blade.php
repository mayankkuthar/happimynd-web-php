<x-backend-layout>
  <x-slot name="title">
    All Psychologist
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Add Psychologist</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
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
                <br>
                <form class="form-horizontal form-label-left" id="add-psychologist-form" enctype="multipart/form-data" method="POST" action="{{ route('admin.psychologist.add.post') }}">
                  @csrf
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">First Name</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="first name" name="first_name" required>
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Last Name</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="last name" name="last_name" required >
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Username<span> (Username should be Unique)</span></label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Username" name="username" required >
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Email</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="email" class="form-control" placeholder="Email" name="email" required >
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Password</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Password" name="password" required >
                    </div>
                  </div>


                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Price per session(if session book through Orgnisation) (1-5000)</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Price Per Session" name="price_per_session" required >
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Given Percentage (1-100%)</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Given Percentage" name="commission_percentage" required >
                    </div>
                  </div>
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">TDS Percentage (1-100%)</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Given Percentage" name="tds_percentage" required >
                    </div>
                  </div>



                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Summary <span class="required">*</span>
                    </label>
                    <div class="col-md-9 col-sm-9 ">
                      <textarea class="form-control" rows="3" placeholder="Summary" name="summary" required></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Expert Level</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="form-control" name="expert_level_id" required>
                        <option></option>
                        @foreach($expertLevels as $expertLevel)
                          <option value="{{ $expertLevel->id }}">{{ $expertLevel->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Select Specialization</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="select2_multiple form-control" multiple="multiple" tabindex="-1" name="specialization_id[]" required>
                        <option></option>
                        @foreach($specializations as $specialization)
                          <option value="{{ $specialization->id }}">{{ $specialization->name }}</option>
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
                          <option value="{{ $city->id }}">{{ $city->name }}</option>
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
                          <option value="{{ $language->id }}">{{ $language->name }}
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Psychologist Picture</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="file" name="picture" accept="image/*" required>
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
