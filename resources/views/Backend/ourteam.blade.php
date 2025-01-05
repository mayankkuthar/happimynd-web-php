@php  
use Illuminate\Support\Str;
@endphp
<x-backend-layout>
  <x-slot name="title">
    OurTeam
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="p-5">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @elseif((session('error')))
        <div class="alert alert-danger alert-dismissible fade show terms__addtitle" role="alert">
          {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
      </div>
          <form action="{{ route('admin.staticData.OurTeamFormSave') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$member->id ??''}}" >
            <div class="form-group">
                 <label for="name">Name:</label>
                 <input type="text" class="form-control" name="name" id="name" value="{{$member->name ??''}}" required/>
            </div>
            <div class="form-group">
                 <label for="designation">Designation</label>
                 <input type="text" class="form-control" name="designation" id="designation"  value="{{$member->designation ??''}}" required >
            </div>
            <div class="form-group">
                 <label for="description">Description</label>
                 <textarea rows="8" cols="50" class="form-control" name="description" id="description">{{$member->description ??''}}</textarea>
            </div>
            <div class="form-group">
                 <label for="category">Category:</label>
                 <select name="category" id="category"  value="{{$member->category ??''}}" required>
                   <option value="" disabled selected>Category</option>
                   <option value="Founders" >Founders</option>
                   <option value="Experts">Experts</option>
                   <option value="Psychologists">Psychologists</option>
                 </select>
                 <div id="discount_vali" style="color:red;"></div>
            </div>

            <div class="form-group">
                 <label for="Image">Image</label>
                 <input type="file" class="form-control" name="image" id="image"/>
                 <div id="discount_vali" style="color:red;"></div>
            </div>

            <div class="form-group">
                 <label for="linkedin">LinkedIn</label>
                 <input type="text" class="form-control" name="linkedin"  value="{{$member->linkedin ??''}}" id="linkedin" >
                 <div id="inoffer_vali" style="color:red;"></div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary terms__update__btn">Save</button>
            </div>
          </form>
          <div class="note">
            <p style="font-size:150%;color:red;">
                Note:Please drag to reorder and change the preference and it will be saved automatically.
            </p>
          </div>
      <div class="new-section">
        @isset($founders)
          <div class="col">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                      Founders
                  </p>
                  <div class="x_content">
                  <table id="datatable_founder" class="table table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>Preference</th>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($founders as $founder)
                      <tr class>
                        <td class='priority_founder'>{{ $loop->iteration }}</td>
                        <td class="id_founder">{{ $founder->id }}</td>
                        <td>{{ $founder->name }}</td>
                        <td>{{ $founder->designation }}</td>
                        <td>{!!Str::words($founder->description,500)!!}</td>
                        <td>
                            <a href="{{ route('admin.staticData.ourteamFormEdit',['id'=>$founder->id]) }}"><i class="fa fa-edit"></i> </span></a>
                            <a href="{{ route('admin.staticData.ourteamFormDelete',['id'=>$founder->id]) }}"><i class="fa fa-trash-o"></i></a>
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
        @endisset
      </div>

      <div class="new-section">
        @isset($experts)
          <div class="col">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                      Experts
                  </p>
                  <div class="x_content">
                  <table id="datatable_expert" class="table table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>Preference</th>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($experts as $expert)
                      <tr class>
                        <td class='priority_expert'>{{ $loop->iteration }}</td>
                        <td class="id_expert">{{ $expert->id }}</td>
                        <td>{{ $expert->name }}</td>
                        <td>{{ $expert->designation }}</td>
                        <td>{!!Str::words($expert->description,500)!!}</td>
                        <td>
                            <a href="{{ route('admin.staticData.ourteamFormEdit',['id'=>$expert->id]) }}"><i class="fa fa-edit"></i> </span></a>
                            <a href="{{ route('admin.staticData.ourteamFormDelete',['id'=>$expert->id]) }}"><i class="fa fa-trash-o"></i></a>
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
        @endisset
      </div>

      <div class="new-section">
        @isset($psychologists)
          <div class="col">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                      Psychologists
                  </p>
                  <div class="x_content">
                  <table id="datatable_psychologist" class="table table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>Preference</th>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($psychologists as $psychologist)
                      <tr class>
                        <td class='priority_psychologist'>{{ $loop->iteration }}</td>
                        <td class="id_psychologist">{{ $psychologist->id }}</td>
                        <td>{{ $psychologist->name }}</td>
                        <td>{{ $psychologist->designation }}</td>
                        <td>{!!Str::words($psychologist->description,500)!!}</td>
                        <td>
                            <a href="{{ route('admin.staticData.ourteamFormEdit',['id'=>$psychologist->id]) }}"><i class="fa fa-edit"></i> </span></a>
                            <a href="{{ route('admin.staticData.ourteamFormDelete',['id'=>$psychologist->id]) }}"><i class="fa fa-trash-o"></i></a>
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
        @endisset
      </div>

    </div>
    <x-slot name="js">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
      <script type="text/javascript">
         function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }
        initializeCKEditor("description")
        
        $(document).ready(function() {
          var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width())
            });
            return $helper;
          };

          $("#datatable_expert tbody").sortable({
              helper: fixHelperModified,
            stop: function(event,ui) {renumber_table('#datatable_expert','expert')}
          }).disableSelection();

          $("#datatable_founder tbody").sortable({
              helper: fixHelperModified,
            stop: function(event,ui) {renumber_table('#datatable_founder','founder')}
          }).disableSelection();

          $("#datatable_psychologist tbody").sortable({
              helper: fixHelperModified,
            stop: function(event,ui) {renumber_table('#datatable_psychologist','psychologist')}
          }).disableSelection();

        });

        function renumber_table(tableID,category) {
          $(tableID + " tr").each(function() {
            count = $(this).parent().children().index($(this)) + 1;
            $(this).find('.priority_'+category).html(count);
          })
        updatePriority(category);
        }
        function updatePriority(category){
          var rows = document.getElementById("datatable_"+category).rows;
          var data={
          };
          var y;
          for(i = 1; i <rows.length;i++)
          {
            y = rows[i].cells;
            data[y[1].innerHTML]=y[0].innerHTML;
          }
          var jsondata=JSON.stringify(data);
          $.ajax({
            type: "post",
            url:'{{url("/")}}/admin/priority-update',
            data:{jsondata,data},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(result) {
                if(result.message.notify.type=="error"){
                  location.reload();
                }
              }
          })
        }
        </script>

    </x-slot>
  </x-slot>
</x-backend-layout>
<style type="text/css">
.ui-sortable tr {
	cursor:pointer;
}
.ui-sortable tr:hover {
	background:rgba(244,251,17,0.45);
}
</style>
