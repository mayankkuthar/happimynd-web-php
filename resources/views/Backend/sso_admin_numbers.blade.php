<x-backend-layout>
  <x-slot name="title">
    App Admin Numbers
  </x-slot>
  <x-slot name="css">
    <style>
      .sa-muted { color: var(--hm-muted, #6b6b80); font-size: 12px; }
      .sa-actions { display: flex; gap: 6px; align-items: center; flex-wrap: nowrap; }
      .sa-actions form { display: inline; margin: 0; }
      .sa-actions .btn { padding: 3px 9px; font-size: 12px; line-height: 1.5; margin: 0; white-space: nowrap; }
      .sa-pill { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 11px; font-weight: 600; white-space: nowrap; border: 1px solid transparent; }
      .sa-pill.on { background: #e7f6ee; color: #1f7a4d; border-color: #bfe6cf; }
      .sa-pill.off { background: #f1f0f6; color: #6b6b80; border-color: #e6e4ef; }
      .sa-pill.role { background: #efeafd; color: #5b3fb8; border-color: #ddd3fa; }
      table.sa-table td { vertical-align: middle !important; }
    </style>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>App Admin Numbers <small>admin access in Quizzard and the Games Hub</small></h2>
              <a href="{{ route('admin.ssoAdminNumbers.create') }}" class="btn btn-primary btn-sm pull-right">+ Add number</a>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              @if(Session::has('error'))<div class="alert alert-danger">{{ Session::get('error') }}</div>@endif
              @if(Session::has('success'))<div class="alert alert-success">{{ Session::get('success') }}</div>@endif

              <p class="sa-muted">
                Quizzard and the Games Hub (HappiPAUSE) have no passwords: everyone signs in with their HappiMynd mobile OTP.
                A number on this list gets the admin rights shown when its owner signs in. Rights are checked on every sign-in,
                so switching a number off or removing it takes them away (on the Games Hub dashboard, straight away).
              </p>

              <div class="table-responsive">
                <table class="table table-striped table-bordered sa-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Quizzard</th>
                      <th>Games Hub</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($numbers as $n)
                      <tr>
                        <td><b>{{ $n->name }}</b></td>
                        <td>{{ $n->mobile }}</td>
                        <td>
                          @if($n->quizzard_role === 'none')
                            <span class="sa-muted">No access</span>
                          @else
                            <span class="sa-pill role">{{ \App\Models\SsoAdminNumber::QUIZZARD_ROLES[$n->quizzard_role] ?? $n->quizzard_role }}</span>
                            @if($n->quizzard_role === 'admin')
                              <div class="sa-muted">{{ $orgs[$n->quizzard_org_id] ?? ('Organisation ' . $n->quizzard_org_id) }}</div>
                            @endif
                          @endif
                        </td>
                        <td>
                          @if($n->hub_admin)<span class="sa-pill role">Admin dashboard</span>@else<span class="sa-muted">No access</span>@endif
                        </td>
                        <td><span class="sa-pill {{ $n->is_active ? 'on' : 'off' }}">{{ $n->is_active ? 'On' : 'Off' }}</span></td>
                        <td>
                          <div class="sa-actions">
                            <a href="{{ route('admin.ssoAdminNumbers.edit', $n->id) }}" class="btn btn-default">Edit</a>
                            <form method="POST" action="{{ route('admin.ssoAdminNumbers.toggle', $n->id) }}">
                              @csrf
                              <button type="submit" class="btn btn-default">{{ $n->is_active ? 'Switch off' : 'Switch on' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.ssoAdminNumbers.delete', $n->id) }}"
                                  onsubmit="return confirm('Remove {{ addslashes($n->name) }} ({{ $n->mobile }}) from the list?');">
                              @csrf
                              <button type="submit" class="btn btn-danger">Remove</button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="6" class="text-center sa-muted">No numbers yet. Nobody has admin rights in Quizzard or the Games Hub.</td></tr>
                    @endforelse
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
