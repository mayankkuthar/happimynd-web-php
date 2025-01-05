<x-backend-layout>
  <x-slot name="title">
    Recommendation Categories
  </x-slot>

  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">

            <div class="x_title">
              <h2>Recommendation Categories</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <div class="row">
                <div class="col-sm-12">
                  <button class="btn btn-primary btn-round" onclick="location.href='{{ route('admin.chat-bot.recommendation-categories.create') }}'">Add</button>

                  <div class="card-box table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dataTable no-footer dtr-inline" style="width: 100%;" role="grid" aria-describedby="datatable-buttons_info">
                      <thead>
                        <tr role="row">
                          <th class="" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;" aria-label="">ID</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 110px;">Name</th>
                          <th class="sorting" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 60px;">Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($recommendationCategories as $recommendationCategory)
                        <tr role="row">
                            <td>{{ $recommendationCategory->id }}</td>
                            <td class="">{{ $recommendationCategory->name }}</td>
                            <td>
                              <a href="{{ route('admin.chat-bot.recommendation-categories.edit', [$recommendationCategory]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                              <a href="#" class="btn btn-danger btn-xs" onclick="deleteRecommendationCategory('{{ route('admin.chat-bot.recommendation-categories.destroy', [$recommendationCategory]) }}')"><i class="fa fa-trash-o"></i> Delete </a>
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
        function deleteRecommendationCategory(url){
          var check = confirm('Confirm to delete');
          if(check){
            location.href=url;
          }
        }
      </script>
    </x-slot>
  </x-backend-layout>
