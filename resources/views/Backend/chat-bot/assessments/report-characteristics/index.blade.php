<x-backend-layout>
  <x-slot name="title">
    Report Characteristics
  </x-slot>

  <x-slot name="content">
    <!-- Page content -->
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-sm-12">
          <div class="flash-message">
            @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="m-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>

          <div class="x_panel">
            <div class="x_title">
              <h2>Report Characteristics</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <div class="card-box table-responsive">
                <div class="mb-3">
                  <a class="btn btn-primary" href="{{ route('admin.chat-bot.report-characteristics.create') }}">Add</a>
                </div>
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Category</th>
                      <th>Minimum</th>
                      <th>Maximum</th>
                      <th>Interpretation</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach($categories as $category)
                      @foreach ($category->reportCharacteristics as $reportCharacteristic)
                        <tr>
                          <th>{{ $reportCharacteristic->id }}</th>
                          <td>{{ $category->name }}</td>
                          <td>{{ $reportCharacteristic->minimum }}</td>
                          <td>{{ $reportCharacteristic->maximum }}</td>
                          <td>{{ $reportCharacteristic->interpretation }}</td>
                          <td>
                            <a class="btn btn-xs btn-info" href="{{ route('admin.chat-bot.report-characteristics.edit', $reportCharacteristic) }}">
                              <i class="fa fa-pencil"></i>
                            </a>

                            <form class="d-inline" action="{{ route('admin.chat-bot.report-characteristics.destroy', $reportCharacteristic) }}" method="post" onsubmit="return confirm('Are you sure?');">
                              @csrf
                              @method('DELETE')

                              <button type="submit" class="btn btn-xs btn-danger">
                                <i class="fa fa-trash"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
