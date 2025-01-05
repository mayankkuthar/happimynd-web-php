<x-backend-layout>
  <x-slot name="title">
    Edit a Question
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
              <h2>Edit a Question</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <form action="{{ route('admin.chat-bot.questions.update', $question) }}" method="post">
                @csrf
                @method('PUT')

                {{-- Categories --}}
                <div class="form-group">
                  <select class="form-control w-auto pr-5" id="chat-bot-category-id" name="chat_bot_category_id" required>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" {{ ( $category->id == old('chat_bot_category_id', $question->chat_bot_category_id)) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                  </select>

                    @error('chat_bot_category_id')
                      <div class="text-danger py-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Question --}}
                <div class="form-group">
                  <label class="control-label" for="question">Question</label>
                  <textarea class="form-control" id="question" name="question" required>{{ old('question', $question->question) }}</textarea>

                  @error('question')
                    <div class="text-danger py-1">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Options --}}
                <div id="options" class="form-group">
                  @if (old('options', $question->options))
                    @for ( $i = 0; $i < count(old('options', $question->options)); $i++)
                    <div class="form-group row">
                      <input type="hidden" class="col-12" name="options[{{ $i }}][id]" value="{{ ($question->options[$i]->id) ? $question->options[$i]->id : 0 }}">

                      <div class="col-md-6 col-sm-12 form-group">
                        <label class="control-label">Option</label>
                        <input type="text" name="options[{{ $i }}][option]" class="form-control" value="{{ old('options.'.$i.'.option', $question->options[$i]->option) }}"  required>
                      </div>

                      <div class="col-md-6 col-sm-12 form-group">
                        <label class="control-label">Score</label>
                        <input type="text" name="options[{{ $i }}][score]" class="form-control" value="{{ old('options.'.$i.'.score', $question->options[$i]->score) }}"  required>
                      </div>
                    </div>
                    @endfor
                  @else
                    <div class="form-group row">
                      <input type="hidden" class="col-12" name="options[0][id]" value="0">

                      <div class="col-md-6 col-sm-12 form-group">
                        <label class="control-label">Option</label>
                        <input type="text" name="options[0][option]" class="form-control" value="{{ old('options.0.option') }}" required>
                      </div>

                      <div class="col-md-6 col-sm-12 form-group">
                        <label class="control-label">Score</label>
                        <input type="text" name="options[0][score]" class="form-control" value="{{ old('options.0.score') }}" required>
                      </div>
                    </div>
                  @endif
                </div>

                {{-- Actions --}}
                <div class="form-group m-0">
                  <button type="button" id="add-option" class="btn btn-primary" data-index="{{ ( old('options', $question->options)) ? count(old('options', $question->options)) - 1 : 0 }}">Add option</button>
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
      var addOption = $('#add-option');
      var index = addOption.data('index');

      addOption.on('click', function () {
        index++;

        $('#options').append(`
          <div class="form-group row">
            <input type="hidden" class="col-12" name="options[${index}][id]" value="0">

            <div class="col-md-6 col-sm-12 form-group">
              <label class="control-label">Option</label>
              <input type="text" name="options[${index}][option]" class="form-control" value="{{ old('options.${index}.option') }}" required>
            </div>

            <div class="col-md-6 col-sm-12 form-group">
              <label class="control-label">Score</label>
              <input type="text" name="options[${index}][score]" class="form-control" value="{{ old('options.${index}.score') }}" required>
            </div>
          </div>
        `);
      });
    </script>
  </x-slot>
</x-backend-layout>
