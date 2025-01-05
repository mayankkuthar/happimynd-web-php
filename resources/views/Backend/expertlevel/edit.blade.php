<x-backend-layout>
  <x-slot name="title">
    All Psychologist
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Edit Psychologist Plans </h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="flash-message">
                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                  @if(Session::has( $msg))
                  <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
                  @endif
                  @endforeach
                </div>
                <form id="expert-level-form">
                  <input type="hidden" name="expertLevelId" value="{{ $expertLevel->id }}">
                  <div class="field item form-group @error('name') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">Expert Level Name<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6">
                      <input class="form-control" name="name" placeholder="Clinical Psychologist" required="required" value="{{ old('name') ?? $expertLevel->name }}" />
                    </div>
                    @error('expert_level')
                    <div class="alert" id="expert_level-error"> {{ $message }}</div>
                    @enderror
                  </div>

                  <!-- show prices of offer -->
                  @foreach($durations as $duration)
                  <hr>
                  <input type="hidden" name="plans[{{ $duration->id }}][duration_type_id]" value="{{ $duration->id }}">
                  <div class="field item form-group @error('duration-{{ $duration->id }}-cost-price') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Cost<span class="required"></span></label>
                    <div class="col-md-6 col-sm-6">
                      <input class="form-control" name="plans[{{ $duration->id }}][cost-price]" type="text" placeholder="enter amount" value="{{ old('plans['.$duration->id.'][cost-price]') ?? $expertLevel->plan()->where('duration_type_id', $duration->id)->with('offer')->first()->price ?? ''}}"/>
                    </div>
                    @error('duration-{{ $duration->id }}')
                    <div class="alert" id="duration-{{ $duration->id }}-cost-price-error"> {{ $message }}</div>
                    @enderror
                  </div>
                  <div class="field item form-group @error('duration-{{ $duration->id }}-cost-price-discount') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Offer discount(%)<span class="required"></span></label>
                    <div class="col-md-6 col-sm-6">
                      <input class="form-control" name="plans[{{ $duration->id }}][cost-price-discount]" type="text" placeholder="enter amount" value="{{ old("plans[$duration->id][cost-price-discount]") ?? $expertLevel->plan()->where('duration_type_id', $duration->id)->with('offer')->first()->offer->discount ?? ''}}"/>
                    </div>
                    @error('duration-{{ $duration->id }}')
                    <div class="alert" id="duration-{{ $duration->id }}-cost-price-discount-error"> {{ $message }}</div>
                    @enderror
                  </div>

                  <!-- show original prices -->
                  <div class="field item form-group @error('duration-{{ $duration->id }}-selling-price') bad @enderror">
                    <label class="col-form-label col-md-3 col-sm-3  label-align">{{ $duration->name }} Selling Price<span class="required"></span></label>
                    <div class="col-md-6 col-sm-6">
                      <input class="form-control" name="plans[{{ $duration->id }}][selling-price]" type="text" placeholder="enter amount" value="{{ old("plans[$duration->id][selling-price]")?? $expertLevel->plan()->where('duration_type_id', $duration->id)->with('offer')->first()->offer->price ?? $expertLevel->plan()->where('duration_type_id', $duration->id)->with('offer')->first()->offer->price ?? ''}}"/>
                    </div>
                    @error('duration-{{ $duration->id }}')
                    <div class="alert" id="duration-{{ $duration->id }}-selling-price-error"> {{ $message }}</div>
                    @enderror
                  </div>
                  @endforeach

                  <div class="ln_solid"></div>
                  <div class="form-group row">
                    <div class="col-md-9 col-sm-9  offset-md-3">
                      <button type="submit" class="btn btn-success">Update</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- page content -->
  </x-slot>

  <x-slot name="js">
    <script type="text/javascript">
      $('#expert-level-form').on('submit', function(e){
        e.preventDefault();
        var form = e.target;
        var formData = $(form).serialize();
        $.ajax({
          method: "POST",
          data: formData,
          url: "{{ route('admin.updateExpertLevel.post') }}",
          dataType: "JSON",
          success: function(data) {
            console.log(data);
          },
          error: function(data) {
            console.log(data);
          }
        })
        console.log();
      })
    </script>
  </x-slot>
</x-backend-layout>
