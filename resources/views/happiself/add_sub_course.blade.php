<x-backend-layout>
  <x-slot name="title">
    Add HappiSelf Sub Course
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Add HappiSelf Sub Course</h3>
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
                 
                  <div class="form-group">
                       <label for="reg_price" style="font-size: 17px;">Course Name:</label>
                       <input type="text" class="form-control" name="course_name" value="{{$couse_detail->course_name}}" disabled required/>
                  </div>

                  <div class="form-group">
                       <label for="reg_price" style="font-size: 17px;">Sub Course Name:</label>
                       <input type="text" class="form-control" name="sub_course_name" id="name" value="" required/>
                  </div>


                  <div class="form-group">
                       <label for="reg_price" style="font-size: 17px;">After:</label>
                       <?php 
                          $length_of_sub_courses = count($sub_course_list_of_course);
                       ?>
                       <select name="count_for_sequence" class="form-control" @if($length_of_sub_courses == 0) disabled @endif>
                        @foreach($sub_course_list_of_course as $row)
                         <option value="{{$row->count_for_sequence}}">{{$row->sub_course_name}}</option>
                        @endforeach
                       </select>
                  </div>



                <div class="form-group">
                  <button type="submit" class="btn btn-primary terms__update__btn">Add</button>
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
<script>
  var msg = '{{Session::get('alert')}}';
  var exist = '{{Session::has('alert')}}';
  if(exist){
    alert(msg);
  }
</script>