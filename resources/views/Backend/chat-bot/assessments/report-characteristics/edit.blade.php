<x-backend-layout>
  <x-slot name="title">
    Edit a Report Characteristic
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
              <h2>Edit a Report Characteristic</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.report-characteristics.update', $reportCharacteristic) }}" method="post">
                @csrf
                @method('PUT')

                {{-- Categories --}}
                <div class="form-group">
                  <select class="form-control w-auto pr-5" id="chat-bot-category-id" name="chat_bot_category_id" required>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" {{ ( $category->id == old('chat_bot_category_id', $reportCharacteristic->chat_bot_category_id)) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                  </select>

                    @error('chat_bot_category_id')
                      <div class="text-danger py-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Scores --}}
                <div id="scores" class="form-group">
                  <div class="mt-3 pt-3">
                    <div class="form-group row">
                      <div class="col-md-6 col-sm-12 form-group">
                        <label class="control-label">Minimum</label>
                        <input type="number" name="minimum" class="form-control" value="{{ old('minimum', $reportCharacteristic->minimum) }}" required>
                      </div>

                      <div class="col-md-6 col-sm-12 form-group">
                        <label class="control-label">Maximum</label>
                        <input type="number" name="maximum" class="form-control" value="{{ old('maximum', $reportCharacteristic->maximum) }}" required>
                      </div>

                      <div class="col-sm-12 form-group">
                        <label class="control-label">Interpretation</label>
                        <textarea type="text" name="interpretation" class="form-control" required>{{ old('interpretation', $reportCharacteristic->interpretation) }}</textarea>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Actions --}}
                <div class="form-group m-0">
                  <button type="submit" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
