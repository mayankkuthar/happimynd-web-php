<x-backend-layout>
  <x-slot name="title">
    Categories
  </x-slot>

  <x-slot name="content">
    <!-- Page content -->
    <div class="right_col" role="main">
      <div class="page-title">
        <div class="title_left">
          <h3>Categories</h3>
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
              <h2>Create a Category</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.categories.store') }}" method="post">
                @csrf

                {{-- Name --}}
                <div class="form-group">
                  <label class="control-label"> Name <span class="text-danger">*</span></label>
                  <input class="form-control" name="name" value="{{ old('name') }}" required>

                  @error('name')
                    <div class="text-danger py-1">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Calculation step macro --}}
                <div class="form-group">
                  <label class="control-label"> Calculation Step <span class="text-danger">*</span></label>
                  <input class="form-control" name="calculation_step_macro" value="{{ old('calculation_step_macro', 'ADDALLSCORE') }}" required>

                  @error('calculation_step_macro')
                    <div class="text-danger py-1">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group row">
                  <div class="col-sm-12 col-md-auto">
                    <label class="control-label">Score Calculation Operator</label>
                    <select name="operator" class="form-control">
                      <option>Operator</option>
                      <option value="multiplication">*</option>
                      <option value="division">/</option>
                      <option value="addition">+</option>
                      <option value="subtraction">-</option>
                      <option value="modulus">%</option>
                    </select>
                  </div>

                  <div class="col-sm-12 col-md-auto">
                    <label class="control-label">Score Calculation Amount</label>
                    <input type="number" class="form-control" placeholder="Amount">
                  </div>
                </div>

                <div class="form-group mt-3 pt-3 border-top">
                  <button type='submit' class="btn btn-primary">Save</button>
                  <button type='reset' class="btn btn-success">Reset</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Categories</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <div class="card-box table-responsive">
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Category Name</th>
                      <th>Calculation Step</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach($categories as $category)
                    <tr>
                      <th>{{ $category->id }}</th>
                      <td>{{ $category->name }}</td>
                      <td>{{ $category->calculation_step_macro }}</td>
                      <td>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.chat-bot.categories.edit', $category) }}">
                          <i class="fa fa-pencil"></i>
                        </a>

                        <form class="d-inline" action="{{ route('admin.chat-bot.categories.destroy', $category) }}" method="post" onsubmit="return confirm('Are you sure?');">
                          @csrf
                          @method('DELETE')

                          <button type="submit" class="btn btn-xs btn-danger">
                            <i class="fa fa-trash"></i>
                          </button>
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
  </x-slot>
</x-backend-layout>
