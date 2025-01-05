<div class="flash-message">
  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has( $msg))
      <p class="alert-{{ $msg }}">{!! Session::get($msg) !!}</p>
    @endif
  @endforeach
</div>