<x-backend-layout>
  <x-slot name="title">
    All prompts
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2>Prompts</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-sm-12">
                  <button class="btn btn-primary btn-round" onclick="location.href='{{ route('admin.prompt.add') }}'">Add</button>
                  <div class="card-box table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dataTable no-footer dtr-inline" style="width: 100%;" role="grid" aria-describedby="datatable-buttons_info">
                      <thead>
                        <tr role="row">
                          <th class="" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;" aria-label="">id</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;">Description</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 60px;">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($prompts as $prompt)
                        <tr role="row">
                            <td>{{ $prompt->id }}</td>
                            <td class="">{{ $prompt->description }}</td>
                            <td>
                              <a href="{{ route('admin.prompt.edit', ['id' => $prompt->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                              <a href="#" class="btn btn-danger btn-xs" onclick="deletePrompt('{{ route('admin.prompt.delete', ['id' => $prompt->id]) }}')"><i class="fa fa-trash-o"></i> Delete </a>
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
    </x-slot>
    <x-slot name="js">
      <script>
        function deletePrompt(url){
          var check = confirm('Confirm to delete');
          if(check){
            location.href=url;
          }
        }
      </script>
    </x-slot>
  </x-backend-layout>
