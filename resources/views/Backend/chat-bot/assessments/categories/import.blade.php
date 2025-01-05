<x-backend-layout>
  <x-slot name="title">
    Import Categories
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
              <h2>Import Categories</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.categories.import') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                  <label class="control-label" for="categories">File <span class="text-danger">*</span></label>
                  <input type="file" name="categories" id="categories" class="form-control" value="{{ old('categories') }}">
                </div>

                <div class="form-group m-0">
                  <button type="submit" class="btn btn-primary">Import</button>
                  <button type="reset" class="btn btn-danger">Reset</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
