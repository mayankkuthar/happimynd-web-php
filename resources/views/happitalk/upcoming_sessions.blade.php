<x-backend-layout>
  <x-slot name="title">
    HappiTALK Upcoming Sessions
  </x-slot>
  <x-slot name="css">
    <style>
      .ht-when { font-weight: 600; white-space: nowrap; }
      .ht-rel { color: var(--hm-muted, #6b6b80); font-size: 12px; white-space: nowrap; }
      .ht-soon { color: #c0392b; font-weight: 600; }
      .ht-muted { color: var(--hm-muted, #6b6b80); font-size: 12px; }
      .ht-chips { display: flex; flex-wrap: wrap; gap: 4px; }
      .ht-chip { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; line-height: 16px;
        background: #f1f0f6; color: #6b6b80; border: 1px solid #e6e4ef; white-space: nowrap; }
      .ht-chip.done { background: #e7f6ee; color: #1f7a4d; border-color: #bfe6cf; }
      .ht-chip.skip { background: #fff6e5; color: #8a5a00; border-color: #f3dca8; }
      .ht-notify { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
      .ht-notify select { width: auto; display: inline-block; height: 30px; padding: 2px 6px; font-size: 12px; }
      .ht-help { margin: 0 0 14px; color: var(--hm-muted, #6b6b80); }
      table.ht-table td { vertical-align: top !important; }
    </style>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Upcoming Sessions <small>{{ $sessions->count() }} upcoming</small></h2>
              <a href="{{ route('admin.happitalkBookingList') }}" class="btn btn-default btn-sm pull-right">All bookings</a>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              @if(Session::has('error'))<div class="alert alert-danger">{{ Session::get('error') }}</div>@endif
              @if(Session::has('success'))<div class="alert alert-success">{{ Session::get('success') }}</div>@endif
              @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error){{ $error }} @endforeach</div>@endif

              <p class="ht-help">
                The user and the psychologist get a booking intimation when a session is booked, and reminders
                24 hours, 1 hour and 5 minutes before and when it starts (app notification; the booking intimation and
                the 24-hour and 1-hour reminders also go by email). Use <b>Notify</b> to send one now.
              </p>

              <div class="table-responsive">
                <table class="table table-striped table-bordered ht-table">
                  <thead>
                    <tr>
                      <th>Session</th>
                      <th>User</th>
                      <th>Psychologist</th>
                      <th>Notifications sent</th>
                      <th style="min-width: 250px;">Notify</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($sessions as $s)
                      @php
                        $start = $s->starts_at_carbon;
                        $forThisTime = $s->sentNotifications->filter(function ($n) use ($start) {
                            return $n->session_starts_at && $n->session_starts_at->equalTo($start);
                        });
                        $u = $s->userDetail;
                        $p = $s->psychologistDetail;
                      @endphp
                      <tr>
                        <td>
                          <div class="ht-when">{{ $start->format('D, d M Y') }}</div>
                          <div>{{ $s->time }}</div>
                          <div class="ht-rel {{ $start->isFuture() && $start->diffInMinutes() <= 60 ? 'ht-soon' : '' }}">
                            {{ $start->isFuture() ? 'starts ' . $start->diffForHumans() : 'started ' . $start->diffForHumans() }}
                          </div>
                          <div class="ht-muted">Booked {{ optional($s->created_at)->format('d M, g:i A') }}</div>
                        </td>
                        <td>
                          {{ ($u->nickname ?? null) ?: ($u->username ?? '-') }}
                          <div class="ht-muted">{{ $u->email ?? '' }} {{ $u->mobile ?? '' }}</div>
                          @if(empty($u->device_token))<div class="ht-muted">No app notifications (not logged in to the app)</div>@endif
                        </td>
                        <td>
                          {{ trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: '-' }}
                          <div class="ht-muted">{{ $p->email ?? '' }}</div>
                          @if(empty($p->device_token))<div class="ht-muted">No app notifications (not logged in to the app)</div>@endif
                        </td>
                        <td>
                          <div class="ht-chips">
                            @foreach(array_merge(['booking'], array_keys($schedule), ['reminder']) as $kind)
                              @php
                                $rows = $forThisTime->where('kind', $kind);
                                $ok = $rows->where('status', 'queued');
                              @endphp
                              @if($rows->isEmpty())
                                @if($kind !== 'reminder')<span class="ht-chip" title="Not sent yet">{{ $labels[$kind] }}</span>@endif
                              @else
                                <span class="ht-chip {{ $ok->isNotEmpty() ? 'done' : 'skip' }}"
                                  title="{{ $rows->map(function ($n) { return ucfirst($n->recipient) . ' ' . $n->channel . ': ' . $n->status . ($n->status === 'skipped' ? ' - ' . $n->detail : '') . ' (' . $n->created_at->format('d M g:i A') . ($n->admin_id ? ', by admin' : '') . ')'; })->implode("\n") }}">
                                  {{ $ok->isNotEmpty() ? '✓' : '!' }} {{ $labels[$kind] }}@if($rows->count() > 0 && $kind === 'reminder') ×{{ $rows->pluck('created_at')->map->format('YmdHi')->unique()->count() }}@endif
                                </span>
                              @endif
                            @endforeach
                          </div>
                        </td>
                        <td>
                          <form method="POST" action="{{ route('admin.happitalkSessionNotify', $s->id) }}" class="ht-notify"
                                data-busy-text="Sending…" onsubmit="return confirm('Send this notification now?');">
                            @csrf
                            <select name="type" class="form-control" aria-label="What to send">
                              <option value="reminder">Reminder</option>
                              <option value="booking">Booking intimation</option>
                            </select>
                            <select name="to" class="form-control" aria-label="Send to">
                              <option value="both">User + psychologist</option>
                              <option value="user">User only</option>
                              <option value="psychologist">Psychologist only</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm" data-busy-text="Sending…"><i class="fa fa-bell"></i> Notify</button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center ht-muted" style="padding: 24px;">No upcoming HappiTALK sessions.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <p class="ht-muted">Hover a ticked item to see who it went to and when. "!" means it could not be delivered (no app token or email).</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
