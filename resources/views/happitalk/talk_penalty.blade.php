
<style type="text/css">
  #message-error{
    color: red;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    Penalty Clause
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Penalty Clause</h3>
          </div>
        </div>
            
          <div class="clearfix"></div>
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
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
            </div>
          </div>
          <form method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">

                <div class="col-md-6">
                  <label>For B2B for 1 Credit</label>
                  <!-- <input type="text" class="form-control" value="{{$penalty_details->for_b2b_user ?? ''}}" name="for_b2b_user"> -->
                  <select class="form-control" name="for_b2b_user_for_one_credit" value="{{$penalty_details->for_b2b_user_for_one_credit ?? ''}}" >
                    <option value="1" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '1' ? 'selected' : '' }} >Before 1 Hour</option>
                    <option value="2" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '2' ? 'selected' : '' }} >Before 2 Hour</option>
                    <option value="3" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '3' ? 'selected' : '' }} >Before 3 Hour</option>
                    <option value="4" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '4' ? 'selected' : '' }} >Before 4 Hour</option>
                    <option value="5" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '5' ? 'selected' : '' }} >Before 5 Hour</option>
                    <option value="6" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '6' ? 'selected' : '' }} >Before 6 Hour</option>
                    <option value="7" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '7' ? 'selected' : '' }} >Before 7 Hour</option>
                    <option value="8" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '8' ? 'selected' : '' }} >Before 8 Hour</option>
                    <option value="9" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '9' ? 'selected' : '' }} >Before 9 Hour</option>
                    <option value="10" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '10' ? 'selected' : '' }} >Before 10 Hour</option>
                    <option value="11" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '11' ? 'selected' : '' }} >Before 11 Hour</option>
                    <option value="12" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '12' ? 'selected' : '' }} >Before 12 Hour</option>
                    <option value="13" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '13' ? 'selected' : '' }} >Before 13 Hour</option>
                    <option value="14" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '14' ? 'selected' : '' }} >Before 14 Hour</option>
                    <option value="15" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '15' ? 'selected' : '' }} >Before 15 Hour</option>
                    <option value="16" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '16' ? 'selected' : '' }} >Before 16 Hour</option>
                    <option value="17" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '17' ? 'selected' : '' }} >Before 17 Hour</option>
                    <option value="18" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '18' ? 'selected' : '' }} >Before 18 Hour</option>
                    <option value="19" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '19' ? 'selected' : '' }} >Before 19 Hour</option>
                    <option value="20" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '20' ? 'selected' : '' }} >Before 20 Hour</option>
                    <option value="21" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '21' ? 'selected' : '' }} >Before 21 Hour</option>
                    <option value="22" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '22' ? 'selected' : '' }} >Before 22 Hour</option>
                    <option value="23" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '23' ? 'selected' : '' }} >Before 23 Hour</option>
                    <option value="24" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_one_credit == '24' ? 'selected' : '' }} >Before 24 Hour</option> 
                  </select>
                  <br>
                </div>

                <div class="col-md-6">
                  <label>For B2B for 0.5 Credit</label>
                  <!-- <input type="text" class="form-control" value="{{$penalty_details->for_b2b_user ?? ''}}" name="for_b2b_user"> -->
                  <select class="form-control" name="for_b2b_user_for_half_credit" value="{{$penalty_details->for_b2b_user_for_half_credit ?? ''}}" >
                    <option value="1" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '1' ? 'selected' : '' }} >Before 1 Hour</option>
                    <option value="2" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '2' ? 'selected' : '' }} >Before 2 Hour</option>
                    <option value="3" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '3' ? 'selected' : '' }} >Before 3 Hour</option>
                    <option value="4" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '4' ? 'selected' : '' }} >Before 4 Hour</option>
                    <option value="5" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '5' ? 'selected' : '' }} >Before 5 Hour</option>
                    <option value="6" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '6' ? 'selected' : '' }} >Before 6 Hour</option>
                    <option value="7" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '7' ? 'selected' : '' }} >Before 7 Hour</option>
                    <option value="8" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '8' ? 'selected' : '' }} >Before 8 Hour</option>
                    <option value="9" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '9' ? 'selected' : '' }} >Before 9 Hour</option>
                    <option value="10" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '10' ? 'selected' : '' }} >Before 10 Hour</option>
                    <option value="11" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '11' ? 'selected' : '' }} >Before 11 Hour</option>
                    <option value="12" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '12' ? 'selected' : '' }} >Before 12 Hour</option>
                    <option value="13" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '13' ? 'selected' : '' }} >Before 13 Hour</option>
                    <option value="14" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '14' ? 'selected' : '' }} >Before 14 Hour</option>
                    <option value="15" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '15' ? 'selected' : '' }} >Before 15 Hour</option>
                    <option value="16" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '16' ? 'selected' : '' }} >Before 16 Hour</option>
                    <option value="17" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '17' ? 'selected' : '' }} >Before 17 Hour</option>
                    <option value="18" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '18' ? 'selected' : '' }} >Before 18 Hour</option>
                    <option value="19" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '19' ? 'selected' : '' }} >Before 19 Hour</option>
                    <option value="20" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '20' ? 'selected' : '' }} >Before 20 Hour</option>
                    <option value="21" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '21' ? 'selected' : '' }} >Before 21 Hour</option>
                    <option value="22" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '22' ? 'selected' : '' }} >Before 22 Hour</option>
                    <option value="23" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '23' ? 'selected' : '' }} >Before 23 Hour</option>
                    <option value="24" {{isset($penalty_details) &&  $penalty_details->for_b2b_user_for_half_credit == '24' ? 'selected' : '' }} >Before 24 Hour</option> 
                  </select>
                  <br>
                </div>


                <div class="col-md-6">
                  <label>For B2C 1 Credit</label>
                  <!-- <input type="text" class="form-control" value="{{$penalty_details->for_b2c_user ?? ''}}" name="for_b2c_user"> -->
                  <select class="form-control" name="for_b2c_user_for_one_credit" value="{{$penalty_details->for_b2c_user_for_one_credit ?? ''}}" >
                    <option value="1" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '1' ? 'selected' : '' }} >Before 1 Hour</option>
                    <option value="2" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '2' ? 'selected' : '' }} >Before 2 Hour</option>
                    <option value="3" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '3' ? 'selected' : '' }} >Before 3 Hour</option>
                    <option value="4" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '4' ? 'selected' : '' }} >Before 4 Hour</option>
                    <option value="5" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '5' ? 'selected' : '' }} >Before 5 Hour</option>
                    <option value="6" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '6' ? 'selected' : '' }} >Before 6 Hour</option>
                    <option value="7" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '7' ? 'selected' : '' }} >Before 7 Hour</option>
                    <option value="8" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '8' ? 'selected' : '' }} >Before 8 Hour</option>
                    <option value="9" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '9' ? 'selected' : '' }} >Before 9 Hour</option>
                    <option value="10" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '10' ? 'selected' : '' }} >Before 10 Hour</option>
                    <option value="11" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '11' ? 'selected' : '' }} >Before 11 Hour</option>
                    <option value="12" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '12' ? 'selected' : '' }} >Before 12 Hour</option> 
                    <option value="13" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '13' ? 'selected' : '' }} >Before 13 Hour</option>
                    <option value="14" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '14' ? 'selected' : '' }} >Before 14 Hour</option>
                    <option value="15" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '15' ? 'selected' : '' }} >Before 15 Hour</option>
                    <option value="16" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '16' ? 'selected' : '' }} >Before 16 Hour</option>
                    <option value="17" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '17' ? 'selected' : '' }} >Before 17 Hour</option>
                    <option value="18" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '18' ? 'selected' : '' }} >Before 18 Hour</option>
                    <option value="19" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '19' ? 'selected' : '' }} >Before 19 Hour</option>
                    <option value="20" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '20' ? 'selected' : '' }} >Before 20 Hour</option>
                    <option value="21" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '21' ? 'selected' : '' }} >Before 21 Hour</option>
                    <option value="22" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '22' ? 'selected' : '' }} >Before 22 Hour</option>
                    <option value="23" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '23' ? 'selected' : '' }} >Before 23 Hour</option>
                    <option value="24" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_one_credit == '24' ? 'selected' : '' }} >Before 24 Hour</option> 
                  </select>
                  <br>
                </div>

                <div class="col-md-6">
                  <label>For B2C 0.5 Credit</label>
                  <!-- <input type="text" class="form-control" value="{{$penalty_details->for_b2c_user ?? ''}}" name="for_b2c_user"> -->
                  <select class="form-control" name="for_b2c_user_for_half_credit" value="{{$penalty_details->for_b2c_user_for_half_credit ?? ''}}" >
                    <option value="1" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '1' ? 'selected' : '' }} >Before 1 Hour</option>
                    <option value="2" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '2' ? 'selected' : '' }} >Before 2 Hour</option>
                    <option value="3" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '3' ? 'selected' : '' }} >Before 3 Hour</option>
                    <option value="4" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '4' ? 'selected' : '' }} >Before 4 Hour</option>
                    <option value="5" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '5' ? 'selected' : '' }} >Before 5 Hour</option>
                    <option value="6" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '6' ? 'selected' : '' }} >Before 6 Hour</option>
                    <option value="7" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '7' ? 'selected' : '' }} >Before 7 Hour</option>
                    <option value="8" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '8' ? 'selected' : '' }} >Before 8 Hour</option>
                    <option value="9" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '9' ? 'selected' : '' }} >Before 9 Hour</option>
                    <option value="10" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '10' ? 'selected' : '' }} >Before 10 Hour</option>
                    <option value="11" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '11' ? 'selected' : '' }} >Before 11 Hour</option>
                    <option value="12" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '12' ? 'selected' : '' }} >Before 12 Hour</option>
                    <option value="12" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '12' ? 'selected' : '' }} >Before 12 Hour</option> 
                    <option value="13" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '13' ? 'selected' : '' }} >Before 13 Hour</option>
                    <option value="14" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '14' ? 'selected' : '' }} >Before 14 Hour</option>
                    <option value="15" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '15' ? 'selected' : '' }} >Before 15 Hour</option>
                    <option value="16" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '16' ? 'selected' : '' }} >Before 16 Hour</option>
                    <option value="17" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '17' ? 'selected' : '' }} >Before 17 Hour</option>
                    <option value="18" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '18' ? 'selected' : '' }} >Before 18 Hour</option>
                    <option value="19" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '19' ? 'selected' : '' }} >Before 19 Hour</option>
                    <option value="20" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '20' ? 'selected' : '' }} >Before 20 Hour</option>
                    <option value="21" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '21' ? 'selected' : '' }} >Before 21 Hour</option>
                    <option value="22" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '22' ? 'selected' : '' }} >Before 22 Hour</option>
                    <option value="23" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '23' ? 'selected' : '' }} >Before 23 Hour</option>
                    <option value="24" {{isset($penalty_details) &&  $penalty_details->for_b2c_user_for_half_credit == '24' ? 'selected' : '' }} >Before 24 Hour</option> 
                  </select>
                  <br>
                </div>

                  <div style="text-align: center;">
                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                  </div>

              </div>



            </div>
          </div>
        </form>
        </div>
      </div>
  </x-slot>
  <x-slot name="js">
  </x-slot>
</x-backend-layout>





