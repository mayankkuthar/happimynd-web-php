<x-backend-layout>
  <x-slot name="title">
    HappiLIFE Reminders
  </x-slot>
  <x-slot name="css">
    <style>
      .hl-progress { background:#eee; border-radius:6px; height:8px; width:120px; display:inline-block; vertical-align:middle; overflow:hidden; }
      .hl-progress > span { background:#7b61c9; display:block; height:100%; }
      .hl-muted { color:#888; font-size:12px; }
    </style>
  </x-slot>
  <x-slot name="content">
  <div class="right_col" role="main">
    <div class="x_panel">
      <div class="x_title">
        <h2>HappiLIFE — Unfinished Self Check Ins</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <p class="text-muted font-13">
          Users below started HappiLIFE but haven't submitted it. Their answers are saved, so they resume where they left off
          (website or app). Automatic reminders go out daily at {{ config('happilife.reminder_time') }}
          after {{ implode(', ', config('happilife.reminder_days')) }} day(s) of inactivity
          @if(!config('happilife.reminders_enabled')) <strong class="text-danger">(currently disabled)</strong>@endif.
          Use this page to send one now.
        </p>

        @if(session('happilife_reminder_status'))
          <div class="alert alert-success">{{ session('happilife_reminder_status') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="GET" action="{{ route('admin.happilifeReminders') }}" class="form-inline" style="margin-bottom:15px">
          <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search name, username, email, mobile">
          <button class="btn btn-default" type="submit">Search</button>
        </form>

        <form method="POST" action="{{ route('admin.happilifeReminders.send') }}" id="hl-reminder-form">
          @csrf
          <div class="form-group">
            <label for="hl-message">Message (optional — leave blank to use the personalised default with each user's progress)</label>
            <textarea id="hl-message" name="message" maxlength="500" rows="2" class="form-control"
              placeholder="e.g. You're almost there! Finish your HappiLIFE Self Check In to unlock your report.">{{ old('message') }}</textarea>
          </div>
          <button type="submit" class="btn btn-primary" onclick="return hlConfirm()">Send reminder to selected</button>

          <table class="table table-striped table-bordered" style="width:100%; margin-top:10px">
            <thead>
              <tr>
                <th><input type="checkbox" id="hl-check-all" title="Select all on this page"></th>
                <th>User</th>
                <th>Progress</th>
                <th>Last activity</th>
                <th>Reminders sent</th>
                <th>Auto reminder</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $row)
                <tr>
                  <td><input type="checkbox" class="hl-check" name="assessment_ids[]" value="{{ $row->assessment->id }}"></td>
                  <td>
                    {{ $row->user->nickname ?: $row->user->username }}
                    <div class="hl-muted">{{ $row->user->email }} {{ $row->user->mobile }}</div>
                  </td>
                  <td>
                    @if($row->progress)
                      <span class="hl-progress"><span style="width: {{ $row->progress['percent'] }}%"></span></span>
                      {{ $row->progress['percent'] }}%
                      <div class="hl-muted">{{ $row->progress['answered'] }} / {{ $row->progress['total'] }} answered</div>
                    @else
                      <span class="hl-muted">unavailable</span>
                    @endif
                  </td>
                  <td>
                    @if($row->progress)
                      {{ \Carbon\Carbon::parse($row->progress['last_activity_at'])->diffForHumans() }}
                      <div class="hl-muted">{{ \Carbon\Carbon::parse($row->progress['last_activity_at'])->format('d M Y, h:i A') }}</div>
                    @endif
                  </td>
                  <td>
                    {{ $row->sent_count }}
                    @if($row->last_sent_at)
                      <div class="hl-muted">last {{ \Carbon\Carbon::parse($row->last_sent_at)->diffForHumans() }}</div>
                    @endif
                  </td>
                  <td>
                    @if($row->next_auto_stage)
                      <span class="label label-warning">#{{ $row->next_auto_stage }} due next run</span>
                    @else
                      <span class="hl-muted">not due</span>
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-xs btn-success" onclick="hlSendOne({{ $row->assessment->id }})">Remind</button>
                  </td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center hl-muted">No unfinished HappiLIFE attempts.</td></tr>
              @endforelse
            </tbody>
          </table>
        </form>
        <x-pagination-dropdown :paginator="$assessments" />
      </div>
    </div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Recently sent reminders</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table class="table table-condensed">
          <thead><tr><th>When</th><th>User</th><th>Type</th><th>Progress then</th><th>Channels</th><th>Message</th></tr></thead>
          <tbody>
            @forelse($recent as $r)
              <tr>
                <td>{{ $r->created_at->format('d M Y, h:i A') }}</td>
                <td>{{ optional($r->user)->nickname ?: optional($r->user)->username }}</td>
                <td>{{ $r->source === 'auto' ? 'Automatic #'.$r->stage : 'Manual' }}</td>
                <td>{{ $r->answered }} / {{ $r->total }}</td>
                <td>{{ $r->channels }}</td>
                <td>{{ $r->message }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center hl-muted">No reminders sent yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </x-slot>
  <x-slot name="js">
    <script type="text/javascript">
      document.getElementById('hl-check-all').addEventListener('change', function () {
        var checked = this.checked;
        document.querySelectorAll('.hl-check').forEach(function (c) { c.checked = checked; });
      });
      function hlConfirm() {
        var n = document.querySelectorAll('.hl-check:checked').length;
        if (n === 0) { alert('Select at least one user.'); return false; }
        return confirm('Send a HappiLIFE reminder to ' + n + ' user(s)?');
      }
      function hlSendOne(id) {
        document.querySelectorAll('.hl-check').forEach(function (c) { c.checked = String(c.value) === String(id); });
        if (confirm('Send a HappiLIFE reminder to this user?')) document.getElementById('hl-reminder-form').submit();
      }
    </script>
  </x-slot>
</x-backend-layout>
