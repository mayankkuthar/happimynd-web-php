<x-backend-layout>
  <x-slot name="title">
    HappiLEARN User Detail
  </x-slot>

  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="x_panel">
          <div class="x_title">
            <h2>HappiLEARN content used by {{ $user->username }} @if($user->nickname)({{ $user->nickname }})@endif</h2>
            <a href="{{ route('admin.happilearnUserList') }}" class="btn btn-default btn-sm pull-right">&larr; Back to HappiLEARN users</a>
            <div class="clearfix"></div>
          </div>

          <p>
            <b>Contents opened:</b> {{ $rows->where('views', '>', 0)->count() }}
            @if($hasViewCount) &nbsp;|&nbsp; <b>Total views:</b> {{ $rows->sum('views') }} @endif
            &nbsp;|&nbsp; <b>Liked:</b> {{ $rows->where('liked', true)->count() }}
          </p>

          <div class="card-box table-responsive custom-change-table">
            <table class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Language</th>
                  @if($hasViewCount)<th>Views</th>@endif
                  <th>Liked</th>
                  <th>First Viewed</th>
                  <th>Last Viewed</th>
                </tr>
              </thead>
              <tbody>
                @forelse($rows as $row)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    {{ $row->content->title ?? 'Content not found' }}
                    @if($row->content && $row->content->is_deleted) <span class="label label-default">deleted</span> @endif
                  </td>
                  <td>{{ $row->content->type ?? '-' }}</td>
                  <td>{{ $row->content->language ?? '-' }}</td>
                  @if($hasViewCount)<td>{{ $row->views }}</td>@endif
                  <td>{{ $row->liked ? 'Yes' : 'No' }}</td>
                  <td>{{ $row->first_viewed ? $row->first_viewed->format('M d,Y h:i a') : '-' }}</td>
                  <td>{{ $row->last_viewed ? $row->last_viewed->format('M d,Y h:i a') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;">This user has not used HappiLEARN yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
