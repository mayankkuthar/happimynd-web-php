<x-backend-layout>
  <x-slot name="title">
    Add Recommendations
  </x-slot>

  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div>
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Add Recommendations</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                @include('Backend.includes.flash_message')
                <br>

                <form class="form-horizontal form-label-left" id="add-recommendations-form" method="POST" action="{{ route('admin.chat-bot.recommendations.store') }}">
                  @csrf
                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">User Profile <span class="required text-danger">*</span></label>
                    <div class="col-md-9 col-sm-9">
                      <select id="user_profile_id" name="user_profile_id" required>
                        @foreach($user_profiles as $user_profile)
                          <option value="{{ $user_profile->id }}" {{ (old('user_profile_id') == $user_profile->id ? 'selected' : '') }}>{{ $user_profile->name }}</option>
                        @endforeach
                      </select>

                      @error('user_profile_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Category <span class="required text-danger">*</span></label>
                    <div class="col-md-9 col-sm-9">
                      <select id="recommendation_category_id" name="recommendation_category_id" required>
                        @foreach($recommendationCategories as $recommendationCategory)
                          <option value="{{ $recommendationCategory->id }}" {{ (old('recommendation_category_id') == $recommendationCategory->id ? 'selected' : '') }}>{{ $recommendationCategory->name }}</option>
                        @endforeach
                      </select>

                      @error('recommendation_category_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Recommendations <span class="required text-danger">*</span></label>
                    <div class="col-md-9 col-sm-9">
                      <div class="row">
                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Title:</label>
                          <input type="text" name="title_1" class="form-control" value="{{ old('title_1', 'Title 1') }}" required>
                          @error('title_1')
                            <div class="text-danger mt-2">Title is required.</div>
                          @enderror
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">URL:</label>
                          <input type="text" name="url_1" class="form-control" value="{{ old('url_1', 'https://happimynd.com') }}" required>
                          @error('url_1')
                            <div class="text-danger mt-2">URL is required.</div>
                          @enderror
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Title:</label>
                          <input type="text" name="title_2" class="form-control" value="{{ old('title_2', 'Title 2') }}" required>
                          @error('title_2')
                            <div class="text-danger mt-2">Title is required.</div>
                          @enderror
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">URL:</label>
                          <input type="text" name="url_2" class="form-control" value="{{ old('url_2', 'https://happimynd.com') }}" required>
                          @error('url_2')
                            <div class="text-danger mt-2">URL is required.</div>
                          @enderror
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">Title:</label>
                          <input type="text" name="title_3" class="form-control" value="{{ old('title_3', 'Title 3') }}" required>
                          @error('title_3')
                            <div class="text-danger mt-2">Title is required.</div>
                          @enderror
                        </div>

                        <div class="col-md-6 col-sm-12 form-group">
                          <label class="control-label">URL:</label>
                          <input type="text" name="url_3" class="form-control" value="{{ old('url_3', 'https://happimynd.com') }}" required>
                          @error('url_3')
                            <div class="text-danger mt-2">URL is required.</div>
                          @enderror
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="ln_solid"></div>

                  <div class="form-group">
                    <div class="col-md-9 col-sm-9 offset-md-3">
                      <button type="reset" class="btn btn-primary">Reset</button>
                      <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
