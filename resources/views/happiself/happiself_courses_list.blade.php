<x-backend-layout>
  <x-slot name="title">
    Happiself Course List
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
                  <p class="text-muted font-13 m-b-30">
                    Happiself Course List
                  </p>

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

                  <!-- <a href="{{url('admin/export-self-data')}}" class="btn btn-primary">Export HappiSELF Summary</a> -->

                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th>
                          <th style="width: 160px;">Language</th>
                          <th style="width: 160px;">Course Name</th>
                          <th style="width: 160px;">Course ID</th>
                          <th style="text-align: center;">Action</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php  
                          $i=0;
                        ?>
                        @foreach($course_list as $row)
                        <tr>
                          <td>{{ ++$i }}</td>
                          <td>{{$row->language}}</td>
                          <td>{{$row->course_name}}</td>
                          <td>{{$row->id}}</td>
                          <td style="text-align: center;    width: 50%;">
                            <a href="{{url('admin/edit-happiself-course').'/'.$row->id}}" class="btn btn-primary">Edit</a>
                            <a href="{{url('admin/add-sub-course').'/'.$row->id}}" class="btn btn-primary">Add Sub Course</a>
                            <a href="{{url('admin/view-sub-course').'/'.$row->id}}" class="btn btn-primary">View Sub Course</a>
                            <a href="{{url('admin/delete-course').'/'.$row->id}}" class="btn btn-primary">Delete</a>

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
  </x-slot>


</x-backend-layout>
