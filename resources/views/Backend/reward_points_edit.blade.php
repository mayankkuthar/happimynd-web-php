<x-backend-layout>
  <x-slot name="title">
    {{ $data->exists ? 'Edit Reward' : 'Add Reward' }}
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>{{ $data->exists ? 'Edit Reward' : 'Add Reward' }}</h3>
          </div>
        </div>
            
          <div class="clearfix"></div>
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
                
                @if(Session::has("error"))
                <div class="alert alert-danger">{{Session::get("error")}}</div>
                @endif
                @if(Session::has("success"))
                <div class="alert alert-success">{{Session::get("success")}}</div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger">
                      @foreach ($errors->all() as $error)
                      {{$error}}
                      @endforeach
                </div>
                @endif
                      
            </div>
          </div>
          <form method="POST" id="validate-form" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                    @php($isApp = !$data->exists || $data->awarded_by === \App\Models\RewardPointInstance::BY_APP)

                    <div class="form-group">
                      <label>Reward text (shown to users)</label>
                      <input type="text" name="action_performed" maxlength="255" required value="{{ old('action_performed', $data->action_performed) }}" class="form-control" placeholder="e.g. Complete a HappiPAUSE session">
                    </div>

                    <div class="form-group">
                      <label>Service</label>
                      <input type="text" name="service" maxlength="64" list="reward-services" value="{{ old('service', $data->service) }}" class="form-control" placeholder="Pick or type a service">
                      <datalist id="reward-services">
                        @foreach(\App\Models\RewardPointInstance::SERVICES as $service)
                          <option value="{{ $service }}">
                        @endforeach
                      </datalist>
                    </div>

                    <div class="form-group">
                      <label>Key</label>
                      @if($data->exists)
                        <input type="text" value="{{ $data->key }}" class="form-control" disabled>
                        <small class="text-muted">Fixed: the {{ $isApp ? 'app' : 'backend code' }} finds this reward by its key.</small>
                      @else
                        <input type="text" name="key" maxlength="64" required value="{{ old('key') }}" class="form-control" placeholder="e.g. happipause_session">
                        <small class="text-muted">Lowercase letters, numbers and _ only; cannot be changed later. The app, website or service sends this key to <code>POST /api/v1/earn-reward-points</code> when the user does the action.</small>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Points</label>
                      <input type="number" name="points_to_be_given" min="0" max="1000" required value="{{ old('points_to_be_given', $data->points_to_be_given) }}" class="form-control">
                      <small class="text-muted">0 = no points are given.</small>
                    </div>

                    <div class="form-group">
                      <label>How often can a user earn it?</label>
                      <select name="frequency" class="form-control">
                        @foreach(\App\Models\RewardPointInstance::FREQUENCIES as $value => $label)
                          @if(!$isApp || $value !== 'every_time')
                            <option value="{{ $value }}" @if(old('frequency', $data->frequency) === $value) selected @endif>{{ $label }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @if(old('is_active', $data->is_active)) checked @endif> Switched on
                      </label>
                    </div>

                    <br>

                    <button type="submit" id="submit" class="btn btn-primary">{{ $data->exists ? 'Update' : 'Add reward' }}</button>
                    <a href="{{ route('admin.getRewardPointsInstanceList') }}" class="btn btn-default">Cancel</a>
              </div>



            </div>
          </div>
        </form>
        </div>
      </div>
  </x-slot>
  <x-slot name="js">
  </x-slot>
</x-backend-layout>




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js" integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/additional-methods.min.js" integrity="sha256-vb+6VObiUIaoRuSusdLRWtXs/ewuz62LgVXg2f1ZXGo=" crossorigin="anonymous"></script>




