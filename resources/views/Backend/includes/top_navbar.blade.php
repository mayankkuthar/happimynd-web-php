<?php
  $hmAdmin = Auth::guard('admin')->user();
  $hmName = trim($hmAdmin->full_name ?? '') ?: 'Admin User';
  $hmInitials = collect(preg_split('/\s+/', $hmName))->filter()->take(2)->map(function ($p) {
      return mb_strtoupper(mb_substr($p, 0, 1));
  })->implode('');
  try {
      $hmRole = str_replace('-', ' ', (string) $hmAdmin->getRoleNames()->first());
  } catch (\Throwable $e) {
      $hmRole = '';
  }
?>
<div class="top_nav">
  <div class="nav_menu">
      <div class="nav toggle">
        <a id="menu_toggle" role="button" aria-label="Collapse or expand the menu" title="Collapse / expand menu"><i class="fa fa-bars"></i></a>
      </div>

      <div class="hm-page-title">{{ trim(strip_tags($title ?? '')) ?: 'Admin Panel' }}</div>

      <nav class="nav navbar-nav">
      <ul class="navbar-right">
        <li class="nav-item dropdown open">
          <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
            <span class="hm-avatar" aria-hidden="true">{{ $hmInitials ?: 'A' }}</span>
            <span class="hm-user-meta">
              {{ $hmName }}
              @if($hmRole)<small>{{ $hmRole }}</small>@endif
            </span>
            <span class="fa fa-angle-down"></span>
          </a>
          <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fa fa-tachometer"></i>&nbsp; Dashboard</a>
            <a class="dropdown-item" href="{{ route('admin.logout') }}"><i class="fa fa-sign-out"></i>&nbsp; Log out</a>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</div>
