<x-backend-layout>
  <x-slot name="title">
    Edit a Category
  </x-slot>

  <x-slot name="content">
    <!-- Page content -->
    <div class="right_col" role="main">
      <div class="page-title">
        <div class="title_left">
          <h3>Edit a Category</h3>
        </div>
      </div>

      <div class="clearfix"></div>

      <div class="row">
        <div class="col-sm-12">
          <div class="flash-message">
            @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
          </div>

          <div class="x_panel">
            <div class="x_title">
              <h2>Edit a Category</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.categories.update', $category) }}" method="post">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="form-group row">
                  <label class="control-label col-md-3 col-sm-3">Name <span class="text-danger">*</span></label>
                  <div class="col-md-9 col-sm-9">
                    <input class="form-control" name="name" value="{{ old('name', $category->name) }}" required>

                    @error('name')
                      <div class="text-danger py-1">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                {{-- Calculation step macro --}}
                <div class="form-group row">
                  <label class="control-label col-md-3 col-sm-3">Calculation Step <span class="text-danger">*</span></label>
                  <div class="col-md-9 col-sm-9">
                    <input class="form-control" name="calculation_step_macro" value="{{ old('calculation_step_macro', $category->calculation_step_macro) }}" required>

                    @error('calculation_step_macro')
                      <div class="text-danger py-1">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="form-group row mt-3 pt-3 border-top">
                  <div class="col-md-9 offset-md-3">
                    <button type='submit' class="btn btn-primary">Update</button>
                    <button type='reset' class="btn btn-success">Reset</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
