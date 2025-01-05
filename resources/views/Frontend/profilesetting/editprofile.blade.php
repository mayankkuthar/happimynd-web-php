@extends('layouts.app')

@section('title', 'Happimynd | Edit Profile')
@section('description', 'HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance.')

@section('content')
<div id="container1">
  @include('Frontend.includes.dashboard.header')
  <div class="signup">
    <div class="container">
      <div class="row align-items-center signup__column-reverse">
        <div class="col-lg-7 col-md-6">
          <div class="signup_img">
            <img src="{{ asset('assets/Frontend/images/login_img.svg') }}" >
          </div>
        </div>
        <div class="col-lg-5 col-md-6">
          <div class="signup_form signup_form-create-profile signup_form-create-profile-individual">
            <form id="signupForm" class="edit__profile" method="post" action="{{ route('user.editProfile') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth('user')->user()->id }}">
              <h1>Edit Profile</h1>
              <div class="edit__profile-choose-avatar">
                <h2>Choose your Avatar</h2>
                <div class="edit__profile-choose-avatar-img">
                  @php $gender = auth('user')->user()->gender; @endphp
                  @if($gender == 'male' || $gender == 'female')
                    <img class="edit__profile-choose-avatar1 @if(auth('user')->user()->avatar == $gender.'1.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar1');$('[name=\'avatar\']').val('{{ $gender }}1.svg')" src="{{ asset('assets/Frontend/images/profile/'.$gender.'1.svg') }}" alt="avatar1">
                    <img class="edit__profile-choose-avatar2 @if(auth('user')->user()->avatar == $gender.'2.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar2');$('[name=\'avatar\']').val('{{ $gender }}2.svg')" src="{{ asset('assets/Frontend/images/profile/'.$gender.'2.svg') }}" alt="avatar2">
                    <img class="edit__profile-choose-avatar3 @if(auth('user')->user()->avatar == $gender.'3.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar3');$('[name=\'avatar\']').val('{{ $gender }}3.svg')" src="{{ asset('assets/Frontend/images/profile/'.$gender.'3.svg') }}" alt="avatar3">
                    <img class="edit__profile-choose-avatar4 @if(auth('user')->user()->avatar == $gender.'4.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar4');$('[name=\'avatar\']').val('{{ $gender }}4.svg')" src="{{ asset('assets/Frontend/images/profile/'.$gender.'4.svg') }}" alt="avatar4">
                    <img class="edit__profile-choose-avatar5 @if(auth('user')->user()->avatar == $gender.'5.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar5');$('[name=\'avatar\']').val('{{ $gender }}5.svg')" src="{{ asset('assets/Frontend/images/profile/'.$gender.'5.svg') }}" alt="avatar5">
                    @endif
                    @if($gender == 'other')
                      <img class="edit__profile-choose-avatar1 @if(auth('user')->user()->avatar == 'male1.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar1');$('[name=\'avatar\']').val('male1.svg')" src="{{ asset('assets/Frontend/images/profile/male1.svg') }}" alt="avatar1">
                      <img class="edit__profile-choose-avatar2 @if(auth('user')->user()->avatar == 'male2.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar2');$('[name=\'avatar\']').val('male2.svg')" src="{{ asset('assets/Frontend/images/profile/male2.svg') }}" alt="avatar2">
                      <img class="edit__profile-choose-avatar3 @if(auth('user')->user()->avatar == 'male5.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar3');$('[name=\'avatar\']').val('male5.svg')" src="{{ asset('assets/Frontend/images/profile/male5.svg') }}" alt="avatar3">
                      <img class="edit__profile-choose-avatar4 @if(auth('user')->user()->avatar == 'female1.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar4');$('[name=\'avatar\']').val('female1.svg')" src="{{ asset('assets/Frontend/images/profile/female1.svg') }}" alt="avatar4">
                      <img class="edit__profile-choose-avatar5 @if(auth('user')->user()->avatar == 'female2.svg') active @endif" onclick="selectAvatar('edit__profile-choose-avatar5');$('[name=\'avatar\']').val('female2.svg')" src="{{ asset('assets/Frontend/images/profile/female2.svg') }}" alt="avatar5">
                    @endif

                  <input type="hidden" value="" name="avatar">
                </div>
              </div>
              <div id="successDiv" class="text-success"></div>
              <div class="signup_form__username">
                <h2>Nickname</h2>
                <input type="text" placeholder="Nickname" name="nickname" value="{{ old('nickname') ?? auth('user')->user()->nickname }}"/>
              </div>
              <div class="signup_form__username">
                <h2>Username</h2>
                <input type="text" placeholder="Username" name="username" value="{{ old('username') ?? auth('user')->user()->username }}"/>
              </div>
              <div class="signup_form__username">
                <h2>Email</h2>
                <input type="email" placeholder="Email" name="email" value="{{ old('email') ?? auth('user')->user()->email }}"/>
              </div>
              <div class="signup_form__select">
                <h2>Country</h2>
                <select name="country_id">
                  <option value="">Select your Country</option>

                  @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ (old('country_id', auth('user')->user()->country_id) == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="signup_form__age">
                <h2>Phone</h2>
                <input type="phone" placeholder="Phone" name="mobile" value="{{ old('mobile') ?? auth('user')->user()->mobile }}"/>
              </div>
              <div class="signup_form__age">
                <h2>Age</h2>
                <input type="number" placeholder="Age" name="age" value="{{ old('age') ?? auth('user')->user()->age }}" />
              </div>
              <div class="signup_form__username">
                <h2>Gender</h2>
                <input type="text" placeholder="Gender" name="gender" value="{{ auth('user')->user()->gender }}" disabled />
              </div>
              <div class="editprofile__submitbtn">
                <button type="submit">Save Changes</button>
              </div>
              <div class="signup_form__cancelbtn">
                <button type="button" onclick="window.location.href='{{ url()->previous() }}'">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('assets/Frontend/js/prevent_CP.js') }}"></script>
@endsection