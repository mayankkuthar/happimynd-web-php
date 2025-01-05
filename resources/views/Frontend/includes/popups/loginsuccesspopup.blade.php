@auth
<div class="modal fade" id="login__popup" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog login__popup">
    <div class="modal-content">
      <div class="login__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <diV class="login__popup__content">
        <h1>Hey {{ auth()->user()->nickname }} 👋🏻</h1>
        <p>You’re already logged in, Jump ahead and view dashboard.</p>
        <button type="button"><a href="{{ route('user.dashboard') }}">View Dashboard</a></button>
      </diV>
    </div>
  </div>
</div>
@endauth
