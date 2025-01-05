<x-backend-layout>
  <x-slot name="title">
    All Recommendations
  </x-slot>

  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">

            <div class="x_title">
              <h2>Recommendations</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <div class="row">
                <div class="col-sm-12">
                  <button class="btn btn-primary btn-round" onclick="location.href='{{ route('admin.chat-bot.recommendations.create') }}'">Add</button>

                  <div class="card-box table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dataTable no-footer dtr-inline" style="width: 100%;" role="grid" aria-describedby="datatable-buttons_info">
                      <thead>
                        <tr role="row">
                          <th class="sorting">Id</th>
                          <th class="sorting">Recommendations</th>
                          <th class="sorting">Category</th>
                          <th class="sorting">User Profile</th>
                          <th class="sorting">Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($recommendations as $recommendation)
                          <tr role="row">
                            <td>{{ $recommendation->id }}</td>

                            <td>
                              <ol class="m-0">
                                <li><a href="{{ $recommendation->url_1 }}">{{ $recommendation->title_1 }}</a></li>
                                <li><a href="{{ $recommendation->url_2 }}">{{ $recommendation->title_2 }}</a></li>
                                <li><a href="{{ $recommendation->url_3 }}">{{ $recommendation->title_3 }}</a></li>
                              </ol>
                            </td>

                            <td>{{ $recommendation->recommendationCategory->name }}</td>
                            <td>{{ $recommendation->userProfile->name }}</td>

                            <td>
                              <a href="{{ route('admin.chat-bot.recommendations.edit', ['recommendation' => $recommendation]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                              <form action="{{ route('admin.chat-bot.recommendations.destroy', ['recommendation' => $recommendation]) }}" method="POST" onsubmit="return confirm('Confirm to delete');">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</button>
                              </form>
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
      function deleteRecommendation(url) {
        var check = confirm('Confirm to delete');

        if (check){
          location.href = url;
        }
      }
    </script>
  </x-slot>
</x-backend-layout>
