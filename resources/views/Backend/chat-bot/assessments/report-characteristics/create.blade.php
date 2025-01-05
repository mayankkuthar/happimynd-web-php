<x-backend-layout>
  <x-slot name="title">
    Add a Report Characteristic
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
              <h2>Add a Report Characteristic</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.report-characteristics.store') }}" method="post">
                @csrf

                {{-- Categories --}}
                <div class="form-group">
                  <select class="form-control w-auto pr-5" id="chat-bot-category-id" name="chat_bot_category_id" required>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" {{ ( $category->id == old('chat_bot_category_id')) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                  </select>

                    @error('chat_bot_category_id')
                      <div class="text-danger py-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Scores --}}
                <div id="scores" class="form-group">
                  @if (old('scores'))
                    @for ( $i = 0; $i < count(old('scores')); $i++)
                    <div class="mt-3 pt-3">
                      <div class="form-group row">
                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Minimum</label>
                          <input type="number" name="scores[{{ $i }}][minimum]" class="form-control" value="{{ old('scores.'.$i.'.minimum') }}" required>
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Maximum</label>
                          <input type="number" name="scores[{{ $i }}][maximum]" class="form-control" value="{{ old('scores.'.$i.'.maximum') }}" required>
                        </div>

                        <div class="col-sm-12 form-group">
                          <label class="control-label">Interpretation</label>
                          <textarea type="text" name="scores[{{ $i }}][interpretation]" class="form-control" required>{{ old('scores.'.$i.'.interpretation') }}</textarea>
                        </div>
                      </div>
                    </div>
                    @endfor
                  @else
                    <div class="mt-3 pt-3">
                      <div class="form-group row">
                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Minimum</label>
                          <input type="number" name="scores[0][minimum]" class="form-control" value="{{ old('scores.0.minimum') }}" required>
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Maximum</label>
                          <input type="number" name="scores[0][maximum]" class="form-control" value="{{ old('scores.0.maximum') }}" required>
                        </div>

                        <div class="col-sm-12 form-group">
                          <label class="control-label">Interpretation</label>
                          <textarea type="text" name="scores[0][interpretation]" class="form-control" required>{{ old('scores.0.interpretation') }}</textarea>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>

                {{-- Actions --}}
                <div class="form-group m-0">
                  <button type="button" id="add-score" class="btn btn-primary" data-index="{{ ( old('scores')) ? count(old('scores')) - 1 : 0 }}">Add score</button>
                  <button type="submit" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>

  <x-slot name="js">
    <script type="text/javascript">
      var addScore = $('#add-score');
      var index = addScore.data('index');

      addScore.on('click', function () {
        index++;

        $('#scores').append(`
          <div class="border-top mt-3 pt-3">
            <div class="form-group row">
              <div class="col-md-6 col-sm-12 form-group">
                <label class="control-label">Minimum</label>
                <input type="number" name="scores[${index}][minimum]" class="form-control" value="{{ old('scores.${index}.minimum') }}" required>
              </div>

              <div class="col-md-6 col-sm-12 form-group">
                <label class="control-label">Maximum</label>
                <input type="number" name="scores[${index}][maximum]" class="form-control" value="{{ old('scores.${index}.maximum') }}" required>
              </div>

              <div class="col-sm-12 form-group">
                <label class="control-label">Interpretation</label>
                <textarea type="text" name="scores[${index}][interpretation]" class="form-control" required>{{ old('scores.${index}.interpretation') }}</textarea>
              </div>
            </div>
          </div>
        `);
      });
    </script>
  </x-slot>
</x-backend-layout>
