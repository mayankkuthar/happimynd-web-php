<x-backend-layout>
  <x-slot name="title">
    HappiLearn Content List
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
                    HappiLearn Content List
                  </p>
                  <div class="card-box table-responsive custom-change-table">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.NO</th>
                          <th>Type</th>
                          <th>Status</th>
                          <th>Title</th>
                          <th>Keywords</th>
                          <th>Credit</th>

                          <th>Action</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $i=0;
                        ?>
                        @foreach($content as $row)
                        <tr>
                          <td>{{ ++$i }}</td>
                          <td>{{$row->type}}</td>
                          <td>{{$row->status}}</td>
                          <td>{{$row->title}}</td>
                          <td>{{$row->keywords}}</td>
                          <td>{{$row->credit}}</td>

                          <td>
                            <a href="{{url('admin/delete-happilearn-content').'/'.$row->id}}" class="btn btn-primary">Delete</a>
                          </td>



                        </tr>
                        @endforeach
                      </tbody>
                    </table>

                    {{$content->links()}}
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </x-slot>


</x-backend-layout>
