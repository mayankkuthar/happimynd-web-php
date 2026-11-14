<x-backend-layout>
  <x-slot name="title">
    HappiLEARN Users
  </x-slot>

  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">

        {{-- Summary --}}
        <div class="row">
          <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
            <div class="tile-stats">
              <div class="count">{{ $summary['users'] }}</div>
              <h3>Users using HappiLEARN</h3>
            </div>
          </div>
          <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
            <div class="tile-stats">
              <div class="count">{{ $summary['contents'] }}</div>
              <h3>Different contents opened</h3>
            </div>
          </div>
          <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
            <div class="tile-stats">
              <div class="count">{{ $summary['views'] }}</div>
              <h3>Total content views</h3>
            </div>
          </div>
          <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
            <div class="tile-stats">
              <div class="count">{{ $summary['likes'] }}</div>
              <h3>Total likes</h3>
            </div>
          </div>
        </div>

        <div class="x_panel">
          <div class="x_title">
            <h2>HappiLEARN User List</h2>
            <div class="clearfix"></div>
          </div>

          <form method="GET" action="{{ route('admin.happilearnUserList') }}" class="form-inline" style="margin-bottom:15px;">
            <input type="text" name="username" class="form-control" placeholder="Search username / nickname" value="{{ request('username') }}" style="margin-right:10px;">
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('username'))
              <a href="{{ route('admin.happilearnUserList') }}" class="btn btn-default">Clear</a>
            @endif
          </form>

          @unless($hasViewCount)
            <p class="alert alert-warning">Repeat views are not being counted yet. Run <code>php artisan migrate</code> to enable view counts.</p>
          @endunless

          <div class="card-box table-responsive custom-change-table">
            <table class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Username</th>
                  <th>Nickname</th>
                  <th>Type</th>
                  <th>Contents Opened</th>
                  @if($hasViewCount)<th>Total Views</th>@endif
                  <th>Contents Liked</th>
                  <th>Last Viewed</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                <tr>
                  <td>{{ $users->firstItem() + $loop->index }}</td>
                  <td>{{ $user->username ?? '-' }}</td>
                  <td>{{ $user->nickname ?? '-' }}</td>
                  <td>
                    @if($user->userToken && $user->userToken->token)
                      B2B{{ $user->userToken->token->organization ? ' - ' . $user->userToken->token->organization->name : '' }}
                    @else
                      B2C
                    @endif
                  </td>
                  <td>{{ $user->contents_viewed_count }}</td>
                  @if($hasViewCount)<td>{{ (int)$user->total_views_count }}</td>@endif
                  <td>{{ $user->contents_liked_count }}</td>
                  <td>{{ $user->last_viewed_at ? \Carbon\Carbon::parse($user->last_viewed_at)->format('M d,Y h:i a') : '-' }}</td>
                  <td><a href="{{ route('admin.happilearnUserDetail', $user->id) }}" class="btn btn-primary btn-sm">View Content Used</a></td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;">No HappiLEARN users found.</td></tr>
                @endforelse
              </tbody>
            </table>
            <x-pagination-dropdown :paginator="$users" />
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
