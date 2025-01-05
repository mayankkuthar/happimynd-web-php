<x-backend-layout>
  <x-slot name="title">
    Import Questions
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
              <h2>Import Questions</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.questions.import') }}" method="post" enctype="multipart/form-data">
                @csrf

                {{-- Categories --}}
                <div class="form-group">
                  <label class="control-label" for="questions">Category <span class="text-danger">*</span></label>
                  <select class="form-control w-auto pr-5" id="chat-bot-category-id" name="chat_bot_category_id" required>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" {{ ( $category->id == old('chat_bot_category_id')) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                  </select>

                    @error('chat_bot_category_id')
                      <div class="text-danger py-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                  <label class="control-label" for="questions">File <span class="text-danger">*</span></label>
                  <input type="file" name="questions" id="questions" class="form-control" value="{{ old('questions') }}">
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
