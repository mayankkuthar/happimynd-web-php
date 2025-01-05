
<style type="text/css">
  #message-error{
    color: red;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    Push Notification
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Push Notification</h3>
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
          <form method="POST" id="validate-form" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                  <label>Message</label>
                  <textarea class="form-control" name="message"></textarea>

                  <br> 

                  <label>Notification Send To</label>
                  <select class="form-control" name="type" value=''>
                    <option value="all">All</option>
                    <option value="d2c">Organiztion Users</option>
                    <option value="normal">Normal Users</option>
                  </select>

                  <br>

                  <label>Users Language</label>
                  <select class="form-control" name="user_language" value=''>
                    <option value="all">All</option>
                    @foreach($user_language as $row)
                    <option value="{{$row->name}}">{{$row->name}}</option>
                    @endforeach
                  </select>

                  <br>

                  <label>Date & Time</label>
                  <input type="datetime-local" class="form-control" name="date_time">

                  <br>
                  <br>

                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
              </div>




              <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th> 
                          <th>Message</th>
                          <th>Send To</th> 

                          <th>Date & Time</th> 

                          <th style="text-align: center;">Action</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($schedule_notification_list as $row)
                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>{{ $row->message}}</td> 

                          @if($row->user_type === 'd2c')
                          <td>Organization</td> 
                          @elseif($row->user_type === 'normal')
                          <td>Normal</td> 
                          @else
                          <td>All User</td> 
                          @endif




                          <td>{{ $row->scheduled_date_time}}</td> 

                          <td style="text-align:center;">
                            <a href="{{url('admin/delete-scheduled-notification').'/'.$row->id}}" class="btn btn-primary">Delete</a> 
                          </td> 

                          
                        </tr>
                        @endforeach

                      </tbody>
                    </table>
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




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js" integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/additional-methods.min.js" integrity="sha256-vb+6VObiUIaoRuSusdLRWtXs/ewuz62LgVXg2f1ZXGo=" crossorigin="anonymous"></script>

<script type="text/javascript">

  $(document).ready(function(){
    
    $("form[id = 'validate-form']").validate({
      rules:{
        message: {
            required:true,
        },
      },
      messages:{
        message:{
          required: 'Please enter message.',
        }, 
      },

      submitHandler: function(form) { 
          $("#submit").attr("disabled", true);
          form.submit();
          
      }
    })
  })
  
</script>



<script>
  var msg = '{{Session::get('alert')}}';
  var exist = '{{Session::has('alert')}}';
  if(exist){
    alert(msg);
  }
</script>



<script>
  var today = new Date().toISOString().slice(0, 16);
  document.getElementsByName("date_time")[0].min = today;
</script>




