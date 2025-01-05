<x-backend-layout>
  <x-slot name="title">
    Notification Messages
  </x-slot>
  <x-slot name="css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
  </x-slot>
  <x-slot name="js">
      <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>


  </x-slot>



<x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="x_panel">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">

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
                      


                  <p class="text-muted font-13 m-b-30">
                    Notification Messages
                  </p>


                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th style="width: 10px;">S.NO</th>
                          <th style="width: 100px;">Type</th>
                          <th style="width: 100px;">English</th>
                          <th style="width: 100px;">Hindi</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>

                        @foreach($message_list as  $row)
                        <tr>
                          <td>{{++ $i}}</td>
                          <td>{{$row->type}}</td>
                          <td>{{$row->english}}  <i language='english' id='{{$row->id}}' message='{{$row->english}}' class="fa fa-solid fa-edit edit-message"></i> </td>
                          <td>{{$row->hindi}}  <i language='hindi' id='{{$row->id}}' message='{{$row->hindi}}' class="fa fa-solid fa-edit edit-message"></i></td>
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
  </x-slot>


</x-backend-layout>




<div class="container">
  <h2>Modal Example</h2>
  <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      
      <form method="post" action="{{route('admin.updateNotificationMessage')}}" id="message-form">
      {{csrf_field()}}

      <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" >
            <h4 class="modal-title" style="margin-left: 170px;">Message</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="id" name="id" class="form-control">
            <input type="hidden" id="language" name="language" class="form-control">

            <!-- <input type="text" id="message" name="message" class="form-control"> -->
            <textarea id="message" rows="4" name="message" class="form-control"></textarea>
          </div>
          <input type="submit" name="submit" class="btn">
        </form>

    </div>
  </div>
</div>




<script type="text/javascript">
  $(document).ready(function(){
    // $('.edit-message').on('click' , function(){
    $(document).on('click' , '.edit-message' , function(){
      
      var language = $(this).attr('language');
      $('#language').val(language);

      var id = $(this).attr('id');
      $('#id').val(id);

      var message = $(this).attr('message');
      $('#message').val(message);

      $('#myModal').modal('show');
      $("#myModal").unbind("click");

    })

    $('.close').on('click' , function(){
      $('#myModal').modal('hide');
    })

  })
</script>




<script type="text/javascript">
  $(document).ready(function(){
    setTimeout(function(){
      $('.alert-success').hide();
    },4000)
  })
</script>





