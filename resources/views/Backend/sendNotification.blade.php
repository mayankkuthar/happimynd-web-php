<x-backend-layout>
    <x-slot name="title">
      Create User
    </x-slot>
    <x-slot name="content">
      <!-- page content -->
      <div class="right_col" role="main">
        @if(Session::has('notification_sent'))
        <div class="x_content bs-example-popovers">
          <div class="alert alert-success alert-dismissible " role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            <strong>Notification Sent!</strong> notified {{ Session::get("count") }} users.
          </div>
        </div>
        @endif
          <form method="post" action="{{ route('admin.sendNotificationToUser') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="text" name="message" placeholder="Type message here" required>
            <button class="btn btn-primary" type="submit">Notify</button>
        </form>
      </div>
      <x-slot name="js">

      </x-slot>
    </x-slot>
  </x-backend-layout>
