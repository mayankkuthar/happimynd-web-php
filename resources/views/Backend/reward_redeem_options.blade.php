<x-backend-layout>
  <x-slot name="title">
    Redeem Options
  </x-slot>
  <x-slot name="css">
    <style>
      .rr-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #e6e4ef; background: #f6f5fb; }
      .rr-thumb-empty { display: inline-flex; align-items: center; justify-content: center; color: #b3b0c4; }
      .rr-muted { color: var(--hm-muted, #6b6b80); font-size: 12px; }
      .rr-points { font-weight: 700; white-space: nowrap; }
      .rr-actions { display: flex; gap: 6px; align-items: center; flex-wrap: nowrap; }
      .rr-actions form { display: inline; margin: 0; }
      .rr-actions .btn { padding: 3px 9px; font-size: 12px; line-height: 1.5; margin: 0; white-space: nowrap; }
      .rr-status { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 11px; font-weight: 600; white-space: nowrap; border: 1px solid transparent; }
      .rr-status.live { background: #e7f6ee; color: #1f7a4d; border-color: #bfe6cf; }
      .rr-status.off { background: #f1f0f6; color: #6b6b80; border-color: #e6e4ef; }
      .rr-status.warn { background: #fff6e5; color: #8a5a00; border-color: #f3dca8; }
      table.rr-table td { vertical-align: middle !important; }
    </style>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Redeem Options <small>what users can redeem their reward points for</small></h2>
              <a href="{{ route('admin.rewardRedeemOptions.create') }}" class="btn btn-primary btn-sm pull-right">+ Add option</a>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              @if(Session::has('error'))<div class="alert alert-danger">{{ Session::get('error') }}</div>@endif
              @if(Session::has('success'))<div class="alert alert-success">{{ Session::get('success') }}</div>@endif

              <p class="rr-muted">
                Options that are <b>Live</b> are shown in the app (in this order) from <code>GET /api/v1/reward-redeem-options</code>.
                Redeeming will be added with the app update.
              </p>

              <div class="table-responsive">
                <table class="table table-striped table-bordered rr-table">
                  <thead>
                    <tr>
                      <th style="width: 64px;"></th>
                      <th>Option</th>
                      <th>Category</th>
                      <th>Points</th>
                      <th>Stock / per user</th>
                      <th>Valid</th>
                      <th>Status</th>
                      <th>Order</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($options as $o)
                      @php($status = $o->statusLabel())
                      <tr>
                        <td>
                          @if($o->image)
                            <img src="{{ $o->image }}" alt="" class="rr-thumb" loading="lazy">
                          @else
                            <span class="rr-thumb rr-thumb-empty"><i class="fa fa-gift fa-lg"></i></span>
                          @endif
                        </td>
                        <td>
                          <b>{{ $o->title }}</b>
                          @if($o->reward_value)<div>{{ $o->reward_value }}</div>@endif
                          @if($o->description)<div class="rr-muted">{{ \Illuminate\Support\Str::limit($o->description, 90) }}</div>@endif
                        </td>
                        <td>{{ $o->categoryLabel() }}</td>
                        <td class="rr-points">{{ number_format($o->points_required) }}</td>
                        <td>
                          {{ $o->stock === null ? 'Unlimited' : number_format($o->stock) . ' left' }}
                          <div class="rr-muted">{{ $o->max_per_user ? $o->max_per_user . ' per user' : 'No limit per user' }}</div>
                        </td>
                        <td class="rr-muted" style="white-space: nowrap;">
                          {{ $o->valid_from ? $o->valid_from->format('d M Y') : 'Any time' }}
                          @if($o->valid_until) – {{ $o->valid_until->format('d M Y') }} @endif
                        </td>
                        <td>
                          <span class="rr-status {{ $status === 'Live' ? 'live' : ($status === 'Off' ? 'off' : 'warn') }}">{{ $status }}</span>
                        </td>
                        <td>{{ $o->sort_order }}</td>
                        <td><div class="rr-actions">
                          <a href="{{ route('admin.rewardRedeemOptions.edit', $o->id) }}" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                          <form method="POST" action="{{ route('admin.rewardRedeemOptions.toggle', $o->id) }}" data-no-busy>
                            @csrf
                            <button type="submit" class="btn btn-default btn-xs">{{ $o->is_active ? 'Switch off' : 'Switch on' }}</button>
                          </form>
                          <form method="POST" action="{{ route('admin.rewardRedeemOptions.delete', $o->id) }}"
                                onsubmit="return confirm('Delete &quot;{{ addslashes($o->title) }}&quot;? This cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-xs" title="Delete" aria-label="Delete"><i class="fa fa-trash"></i></button>
                          </form>
                        </div></td>
                      </tr>
                    @empty
                      <tr><td colspan="9" class="text-center rr-muted" style="padding: 24px;">
                        No redeem options yet. <a href="{{ route('admin.rewardRedeemOptions.create') }}">Add the first one</a>.
                      </td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <x-pagination-dropdown :paginator="$options" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
