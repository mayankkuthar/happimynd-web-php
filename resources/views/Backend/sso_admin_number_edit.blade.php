<x-backend-layout>
  <x-slot name="title">
    {{ $number->exists ? 'Edit App Admin Number' : 'Add App Admin Number' }}
  </x-slot>
  <x-slot name="css">
    <style>
      .sa-row { display: flex; flex-wrap: wrap; gap: 16px; }
      .sa-row > .form-group { flex: 1 1 220px; }
      .sa-muted { color: var(--hm-muted, #6b6b80); font-size: 12px; }
    </style>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>{{ $number->exists ? 'Edit App Admin Number' : 'Add App Admin Number' }}</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              @if($errors->any())
                <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
              @endif

              <form method="POST" data-busy-text="Saving…"
                    action="{{ $number->exists ? route('admin.ssoAdminNumbers.update', $number->id) : route('admin.ssoAdminNumbers.store') }}">
                @csrf

                <div class="sa-row">
                  <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" maxlength="100" required class="form-control"
                           value="{{ old('name', $number->name) }}" placeholder="e.g. Mayank Kuthar">
                  </div>
                  <div class="form-group">
                    <label for="mobile">Mobile number <span class="required">*</span></label>
                    <input type="text" id="mobile" name="mobile" maxlength="16" required class="form-control" inputmode="numeric"
                           value="{{ old('mobile', $number->mobile) }}" placeholder="10 digits, e.g. 9318458907">
                    <div class="sa-muted">The number they log in to HappiMynd with.</div>
                  </div>
                </div>

                <div class="sa-row">
                  <div class="form-group">
                    <label for="quizzard_role">Quizzard access</label>
                    <select id="quizzard_role" name="quizzard_role" class="form-control">
                      @foreach(\App\Models\SsoAdminNumber::QUIZZARD_ROLES as $value => $label)
                        <option value="{{ $value }}" @if(old('quizzard_role', $number->quizzard_role) === $value) selected @endif>{{ $label }}</option>
                      @endforeach
                    </select>
                    <div class="sa-muted">Super Admin manages everything; Admin manages one organisation.</div>
                  </div>
                  <div class="form-group" id="org-group">
                    <label for="quizzard_org_id">Quizzard organisation (for Admin)</label>
                    @if(count($orgs))
                      <select id="quizzard_org_id" name="quizzard_org_id" class="form-control">
                        <option value="">Choose…</option>
                        @foreach($orgs as $id => $orgName)
                          <option value="{{ $id }}" @if((string) old('quizzard_org_id', $number->quizzard_org_id) === (string) $id) selected @endif>{{ $orgName }}</option>
                        @endforeach
                      </select>
                    @else
                      <input type="text" id="quizzard_org_id" name="quizzard_org_id" maxlength="255" class="form-control"
                             value="{{ old('quizzard_org_id', $number->quizzard_org_id) }}" placeholder="Quizzard organisation id">
                    @endif
                  </div>
                </div>

                <div class="form-group">
                  <label style="font-weight: normal;">
                    <input type="hidden" name="hub_admin" value="0">
                    <input type="checkbox" name="hub_admin" value="1" @if(old('hub_admin', $number->hub_admin)) checked @endif>
                    <b>Games Hub (HappiPAUSE) admin dashboard</b>
                  </label>
                </div>

                <div class="form-group">
                  <label style="font-weight: normal;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @if(old('is_active', $number->is_active)) checked @endif>
                    <b>On</b> <span class="sa-muted">(switch off to take the rights away without deleting the entry)</span>
                  </label>
                </div>

                <a href="{{ route('admin.ssoAdminNumbers') }}" class="btn btn-default">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
  <x-slot name="js">
    <script>
      (function () {
        var role = document.getElementById('quizzard_role');
        var org = document.getElementById('org-group');
        function sync() { org.style.display = role.value === 'admin' ? '' : 'none'; }
        role.addEventListener('change', sync);
        sync();
      })();
    </script>
  </x-slot>
</x-backend-layout>
