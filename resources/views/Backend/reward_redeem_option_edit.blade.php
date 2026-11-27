<x-backend-layout>
  <x-slot name="title">
    {{ $option->exists ? 'Edit Redeem Option' : 'Add Redeem Option' }}
  </x-slot>
  <x-slot name="css">
    <style>
      .rr-preview { width: 96px; height: 96px; object-fit: cover; border-radius: 10px; border: 1px solid #e6e4ef; }
      .rr-row { display: flex; flex-wrap: wrap; gap: 16px; }
      .rr-row > .form-group { flex: 1 1 200px; }
    </style>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>{{ $option->exists ? 'Edit Redeem Option' : 'Add Redeem Option' }}</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              @if(Session::has('error'))<div class="alert alert-danger">{{ Session::get('error') }}</div>@endif
              @if($errors->any())
                <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
              @endif

              <form method="POST" enctype="multipart/form-data" data-busy-text="Saving…"
                    action="{{ $option->exists ? route('admin.rewardRedeemOptions.update', $option->id) : route('admin.rewardRedeemOptions.store') }}">
                @csrf

                <div class="form-group">
                  <label for="title">Title (shown in the app) <span class="required">*</span></label>
                  <input type="text" id="title" name="title" maxlength="255" required class="form-control"
                         value="{{ old('title', $option->title) }}" placeholder="e.g. Rs 100 off your next HappiTALK session">
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea id="description" name="description" rows="3" maxlength="2000" class="form-control"
                            placeholder="A short line on what the user gets">{{ old('description', $option->description) }}</textarea>
                </div>

                <div class="rr-row">
                  <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" class="form-control">
                      @foreach(\App\Models\RewardRedeemOption::CATEGORIES as $value => $label)
                        <option value="{{ $value }}" @if(old('category', $option->category) === $value) selected @endif>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="points_required">Points needed <span class="required">*</span></label>
                    <input type="number" id="points_required" name="points_required" min="1" max="1000000" required class="form-control"
                           value="{{ old('points_required', $option->points_required) }}">
                  </div>
                  <div class="form-group">
                    <label for="reward_value">Reward value</label>
                    <input type="text" id="reward_value" name="reward_value" maxlength="255" class="form-control"
                           value="{{ old('reward_value', $option->reward_value) }}" placeholder="e.g. Rs 100 off, 1 free session">
                  </div>
                </div>

                <div class="rr-row">
                  <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" min="0" class="form-control" value="{{ old('stock', $option->stock) }}">
                    <small class="text-muted">Leave empty for unlimited.</small>
                  </div>
                  <div class="form-group">
                    <label for="max_per_user">Limit per user</label>
                    <input type="number" id="max_per_user" name="max_per_user" min="1" class="form-control" value="{{ old('max_per_user', $option->max_per_user) }}">
                    <small class="text-muted">Leave empty for no limit.</small>
                  </div>
                  <div class="form-group">
                    <label for="sort_order">Display order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', $option->sort_order ?? 0) }}">
                    <small class="text-muted">Lower numbers show first.</small>
                  </div>
                </div>

                <div class="rr-row">
                  <div class="form-group">
                    <label for="valid_from">Valid from</label>
                    <input type="date" id="valid_from" name="valid_from" class="form-control"
                           value="{{ old('valid_from', optional($option->valid_from)->format('Y-m-d')) }}">
                  </div>
                  <div class="form-group">
                    <label for="valid_until">Valid until</label>
                    <input type="date" id="valid_until" name="valid_until" class="form-control"
                           value="{{ old('valid_until', optional($option->valid_until)->format('Y-m-d')) }}">
                    <small class="text-muted">Leave both empty to show it any time.</small>
                  </div>
                </div>

                <div class="form-group">
                  <label for="image">Image</label>
                  @if($option->image)
                    <div style="margin-bottom: 8px;">
                      <img src="{{ $option->image }}" alt="" class="rr-preview">
                      <label style="font-weight: normal; margin-left: 10px;">
                        <input type="checkbox" name="remove_image" value="1"> Remove image
                      </label>
                    </div>
                  @endif
                  <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp" class="form-control">
                  <small class="text-muted">PNG, JPG or WebP, up to 2 MB. A square image works best.</small>
                </div>

                <div class="form-group">
                  <label for="terms">Terms &amp; conditions</label>
                  <textarea id="terms" name="terms" rows="3" maxlength="5000" class="form-control">{{ old('terms', $option->terms) }}</textarea>
                </div>

                <div class="checkbox">
                  <label>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @if(old('is_active', $option->is_active)) checked @endif> Switched on (shown in the app)
                  </label>
                </div>

                <br>
                <button type="submit" class="btn btn-primary">{{ $option->exists ? 'Update' : 'Add option' }}</button>
                <a href="{{ route('admin.rewardRedeemOptions') }}" class="btn btn-default">Cancel</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
