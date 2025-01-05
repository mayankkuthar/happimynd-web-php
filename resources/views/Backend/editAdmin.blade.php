<style type="text/css">
.icheckbox_flat-green {
    margin-bottom: 3px!important;
}
</style>

<x-backend-layout>
    <x-slot name="title">
      Edit Admin
    </x-slot>
    <x-slot name="content">
      <!-- page content -->
      <div class="right_col" role="main">
        <div class="">
          <div class="page-title">
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit admin user </h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="flash-message">
                          @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has( $msg))
                            <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
                            @endif
                          @endforeach
                        </div>
                      <form class="user-form" action="{{ route('admin.editAdmin') }}" method="post" novalidate>
                          @csrf
                          <input type="text" name="user_id" value="{{ $admin->id }}" hidden>
                          <div class="field item form-group @error('first_name') bad @enderror">
                              <label class="col-form-label col-md-3 col-sm-3  label-align">First Name<span class="required">*</span></label>
                              <div class="col-md-6 col-sm-6">
                                  <input class="form-control" name="first_name" placeholder="John" required="required" value="{{ old('first_name') ?? $admin->first_name }}" />
                              </div>
                              @error('first_name')
                              <div class="alert" id="first_name-error"> {{ $message }}</div>
                              @enderror
                          </div>
                          <div class="field item form-group @error('last_name') bad @enderror">
                              <label class="col-form-label col-md-3 col-sm-3  label-align">Last Name<span class="required"></span></label>
                              <div class="col-md-6 col-sm-6">
                                  <input class="form-control" name="last_name" type="text" placeholder="Doe" value="{{ old('last_name') ?? $admin->last_name }}"/>
                              </div>
                              @error('last_name')
                              <div class="alert" id="last_name-error"> {{ $message }}</div>
                              @enderror
                          </div>

                          <div class="field item form-group @error('gender') bad @enderror">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Gender<span class="required"></span></label>
                            <div class="col-md-6 col-sm-6 ">
                              <select class="form-control" name="gender">
                                @foreach(config('constants.gender') as $gender)
                                  <option value="{{ $gender }}" @if($admin->gender == $gender) selected @endif >{{ Str::ucfirst($gender) }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="field item form-group @error('email') bad @enderror">
                              <label class="col-form-label col-md-3 col-sm-3  label-align">email<span class="required">*</span></label>
                              <div class="col-md-6 col-sm-6">
                                  <input class="form-control" name="email" class='email' required="required" type="email" value="{{ old('email')  ?? $admin->email }}"/>
                              </div>
                              @error('email')
                              <div class="alert" id="email-error"> {{ $message }}</div>
                              @enderror
                          </div>

                          <div class="field item form-group @error('mobile') bad @enderror">
                              <label class="col-form-label col-md-3 col-sm-3  label-align">Number <span class="required"></span></label>
                              <div class="col-md-6 col-sm-6">
                                  <input class="form-control" type="number" class='number' name="mobile" required='required' value="{{ old('mobile')  ?? $admin->mobile }}">
                              </div>
                              @error('mobile')
                              <div class="alert" id="mobile-error"> {{ $message }}</div>
                              @enderror
                          </div>

                          <div class="field item form-group @error('roles') bad @enderror">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Roles<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                @foreach($roles as $role)
                                  @if($role->name != 'content-writer' && $role->name != 'psychologist')
                                    <input type="checkbox" name="roles[]" id="role{{ $role->id }}" value="{{ $role->name }}" required class="flat" @if($admin->hasRole($role->name)) checked="true" @endif /> {{ $role->name }}
                                    <br>
                                  @endif
                                @endforeach
                            </div>
                            @error('roles')
                            <div class="alert" id="role-error"> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field item form-group @error('account_status') bad @enderror">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Account Status<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                <div class="radio">
                                    <label class="">
                                        <div class="iradio_flat-green" style="position: relative;"><input type="radio" class="flat" @if($admin->isActive()) checked="true" @endif name="account_status" value="active" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> Active
                                    </label>
                                </div>
                                <div class="radio">
                                    <label class="">
                                        <div class="iradio_flat-green" style="position: relative;"><input type="radio" class="flat" @if(! $admin->isActive()) checked="true" @endif name="account_status" value="blocked" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> Blocked
                                    </label>
                                </div>
                            </div>
                            @error('account_status')
                            <div class="alert" id="role-error"> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field item form-group @error('password') bad @enderror">
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Password<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                <input class="form-control" type="password" id="password1" name="password"required value="{{ old('password')}}"/>

                                <span style="position: absolute;right:15px;top:7px;" onclick="hideshow()" >
                                    <i id="slash" class="fa fa-eye-slash"></i>
                                    <i id="eye" class="fa fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                            <div class="alert" id="password-error"> {{ $message }}</div>
                            @enderror
                        </div>
                          <div class="ln_solid">
                              <div class="form-group">
                                  <div class="col-md-6 offset-md-3">
                                      <button type='submit' class="btn btn-primary">Submit</button>
                                      <button type='reset' class="btn btn-success">Reset</button>
                                  </div>
                              </div>
                          </div>
                      </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </x-slot>
    <x-slot name="js">
      <script>
          function hideshow(){
              var password = document.getElementById("password1");
              var slash = document.getElementById("slash");
              var eye = document.getElementById("eye");

              if(password.type === 'password'){
                  password.type = "text";
                  slash.style.display = "block";
                  eye.style.display = "none";
              }
              else{
                  password.type = "password";
                  slash.style.display = "none";
                  eye.style.display = "block";
              }

          }
      </script>

      <script>
          // $('form.user-form').submit(function(e) {
          //     e.preventDefault();
          //     $.ajax({
          //         cache: false,
          //         type: "POST",
          //         timeout: 5000,
          //         data: $('form.user-form').serialize(),
          //         url: "{{ route('admin.addAdminUser') }}",
          //         success: function(msg) {

          //         },
          //         error: function(msg) {
          //             var errors = msg.responseJSON['errors'];
          //             console.log(msg.responseJSON);
          //             for(e in errors)
          //             console.log('#'+e+"-error"+" => "+errors[e][0]);
          //             $('#'+e+"-error")[0].innerText = errors[e][0];
          //             $('#'+e+"-error").parent().addClass('bad');
          //         }
          //     });
          // })
      </script>
    </x-slot>
  </x-backend-layout>
