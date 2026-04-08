<div class="modal fade" id="emailInput" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered sendfullreport__popup">
    <div class="modal-content">
      <div class="sendfullreport__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="sendfullreport__popup__content">
        <h1>Your Email</h1>
        <p>Please provide your email ID so that we can email you your details in 48hrs.</p>
        <div id=form-errors></div>
        <form method = "Post" action = "{{ route('OtherServicesMailList.post') }}" class="sendfullreport__popup__form">
          @csrf
          <div class="sendfullreport__popup__form__input">
            <h2>Name</h2>
            <input type="text" name="name" id = "name" required class=" @error('name') is-invalid @enderror"">
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
          </div>
          <div class="sendfullreport__popup__form__input">
            <h2>Email</h2>
            <input type="email" name="email" id = "email" required class=" @error('email') is-invalid @enderror"">
            @error('email')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
          </div>
          <div class="sendfullreport__popup__form__input">
            <h2>Phone</h2>
            <input type="text" name="mobile" id = "mobile" required class=" @error('mobile') is-invalid @enderror"">
            @error('mobile')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <input type="hidden" name="other_service" id="other_service">
          </div>
          <div class="sendfullreport__popup__form__button">
            {{-- <button type="button" onclick="EducationServiceMailList();">Continue</button> --}}
            <button type="submit">Continue</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@section('js')
<script src="{{ asset('assets/Frontend/js/services.js') }}"></script>
@endsection
