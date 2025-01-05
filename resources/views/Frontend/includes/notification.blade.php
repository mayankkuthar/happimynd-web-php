@php
function getIcon($type) {
  $iconHtml;
  switch($type) {
    case "Direct Message": $iconHtml =  '
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 2L11 13" stroke="#60D6C3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="#60D6C3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          ';
          break;
    case "Quote": $iconHtml =  '
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.72361 15.5528C6.39116 16.2177 6.87465 17 7.61803 17H8.38197C8.76074 17 9.107 16.786 9.27639 16.4472L10.8944 13.2111C10.9639 13.0723 11 12.9192 11 12.7639V9C11 7.89543 10.1046 7 9 7H7C5.89543 7 5 7.89543 5 9V11.3416C5 12.2575 5.74247 13 6.65836 13C7.27476 13 7.67566 13.6487 7.4 14.2L6.72361 15.5528ZM14.7236 15.5528C14.3912 16.2177 14.8747 17 15.618 17H16.382C16.7607 17 17.107 16.786 17.2764 16.4472L18.8944 13.2111C18.9639 13.0723 19 12.9192 19 12.7639V9C19 7.89543 18.1046 7 17 7H15C13.8954 7 13 7.89543 13 9V11.3416C13 12.2575 13.7425 13 14.6584 13C15.2748 13 15.6757 13.6487 15.4 14.2L14.7236 15.5528Z" fill="#EA7097"/>
            </svg>
         ';
         break;
    case "Order": $iconHtml = '

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" stroke="#6584DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" stroke="#6584DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="#6584DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          ';
          break;
  }
  return $iconHtml;
}
@endphp
<div class="dashboard__notification">
  <div class="dropdown" >
    <button class="dashboard__notification__img" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M18 8.00037C18 6.40907 17.3679 4.88294 16.2426 3.75773C15.1174 2.63251 13.5913 2.00037 12 2.00037C10.4087 2.00037 8.88258 2.63251 7.75736 3.75773C6.63214 4.88294 6 6.40907 6 8.00037C6 15.0004 3 17.0004 3 17.0004H21C21 17.0004 18 15.0004 18 8.00037Z" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M13.7299 21C13.5541 21.3031 13.3017 21.5547 12.9981 21.7295C12.6945 21.9044 12.3503 21.9965 11.9999 21.9965C11.6495 21.9965 11.3053 21.9044 11.0017 21.7295C10.6981 21.5547 10.4457 21.3031 10.2699 21" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>

      <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        @if(count(auth('user')->user()->unreadNotifications)>0)
          <circle cx="5" cy="5" r="5" fill="#3C92C6" id="unread_badge"/>
        @endif
      </svg>
    </button>
    <div class="dashboard__notification__dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
      <div class="dashboard__notification__content">
        <h1>Notifications</h1>
        <p id = "mark_all_as_read">Mark all as read</p>
      </div>
      <div class="dashboard__notification__options__overflow">
        @foreach (auth('user')->user()->notifications as $notification)
          <div class="dashboard__notification__options" data-notification-id={{ $notification->id }}>
            <div class="dashboard__notification__options__img dashboard__notification__options__img__message">
              {!! getIcon($notification->data['type']) !!}
            </div>
            <div class="dashboard__notification__options__text">
              <h2>
                {!! $notification->data['header'] !!}
                @if(is_null($notification->read_at))
                  <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="4" cy="4" r="4" fill="#EA7097" class="unread_badge"></circle>
                  </svg>
                @endif
              </h2>
              <p>{!! $notification->data['msg'] !!}</p>
              <span>
                {{ $notification->created_at->timezone('Asia/Kolkata')->format('d M') }}
                <svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="2" cy="2" r="2" fill="#C4C4C4"></circle>
                </svg>
                {{ $notification->created_at->timezone('Asia/Kolkata')->format('g:i A') }}
              </span>
            </div>
          </div>
          <hr style="margin: 0; border-top: 0.5px solid #9297A6;">
        @endforeach
      </div>
    </div>
  </div>
</div>
<script>
  $('#mark_all_as_read').click(function() {
    $('#unread_badge').hide();
    $('.unread_badge').hide();
    $.ajax({
    type: "GET",
    url: "{{ route("user.markNotificationsAsRead") }}"
  });
});
</script>