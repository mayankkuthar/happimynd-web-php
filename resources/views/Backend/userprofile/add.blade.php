<x-backend-layout>
  <x-slot name="title">
    Create User Profile
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    @include('Backend.includes.loader')
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="clearfix"></div>
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Create Profile </h2>
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
                  <form class="user-form" action="{{ route('admin.addUserProfile.post') }}" method="post" novalidate>
                    @csrf
                    <div class="field item form-group @error('userProfileName') bad @enderror">
                      <label class="col-form-label col-md-3 col-sm-3  label-align">Profile Name<span class="required">*</span></label>
                      <div class="col-md-6 col-sm-6">
                        <input class="form-control" name="userProfileName" placeholder="Student" required="required" value="{{ old('userProfileName') }}" />
                      </div>
                      @error('userProfileName')
                      <div class="alert" id="userProfileName-error"> {{ $message }}</div>
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
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                <div class="x_content">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
                        <p class="text-muted font-13 m-b-30">

                        </p>
                        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($userProfiles as $userProfile)
                            <tr>
                              <td>{{ $userProfile->name }} (Users: {{ $userProfile->users_count }})</td>
                              <td>
                                <div class="custom-control custom-switch">
                                  <input
                                  type="checkbox"
                                  class="custom-control-input status-button"
                                  id="{{ $userProfile->name.'_'.$userProfile->id }}"
                                  @if($userProfile->status) checked='true' @endif
                                  >
                                  <label class="custom-control-label" for="{{ $userProfile->name.'_'.$userProfile->id }}"></label>
                                </div>
                              </td>
                              <td>
                                <span
                                onclick="openEditModal('{{ $userProfile->name}}', '{{ $userProfile->id }}' )"
                                >
                                <i class="fa fa-edit"></i>
                              </span>
                              <span onclick="deleteProfile({{ $userProfile->id }});">
                                <i class="fa fa-trash-o"></i>
                              </span>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- modal for adding/editing Batch -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="editModal-form" action="{{ route('admin.updateUserProfile.post') }}" method="POST">
                  @csrf
                  <input type="hidden" name="userProfileId" id="profile-id">
                  <div class="form-group">
                    <label for="batch-name" class="col-form-label">Profile Name:</label>
                    <input type="text" class="form-control" id="profile-name" name="userProfileName">
                    <div class="invalid-feedback" id="profile-name-error">

                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" value="submit" id="submit-button" onclick="$('#editModal-form').submit();">Update</button>
              </div>
            </div>
          </div>
        </div>
        <!-- modal for adding/editing Batch -->
      </div>
    </div>
  </div>
</x-slot>
<x-slot name="js">
  <script type="text/javascript">
    $('.status-button').on('change', function(e){
      var userProfileId = e.target.id.split('_')[1]
      console.log(e.target);
      console.log($(e.target).is(":checked"));
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.changeUserProfileStatus.get') }}",
        data: {'userProfileId':userProfileId, 'status' : ($(e.target).is(":checked") == true)? 1: 0 },
        beforeSend: function()
        {
          $('#loader').show();
        },
        success: function(data)
        {
          // location.reload();
        },
        complete: function()
        {
          $('#loader').hide();
        }
      });
    });
    // this is the id of the form
    $("#editModal-form").submit(function(e) {
      console.log('s');

      e.preventDefault(); // avoid to execute the actual submit of the form.

      form = $(this);
      var url = form.attr('action');

      //return if name is empty
      if($(this).find('#profile-name').val() == ''){
        return;
      }
        $.ajax({
          type: form.attr('method'),
          url: url,
          data: form.serialize(), // serializes the form's elements.
          beforeSend: function()
          {
            $('.invalid-feedback').text('')
            $('#loader').show();

          },
          success: function(data)
          {
            if(data.error == false){
              location.reload();
            }
          },
          error: function(data)
          {
            console.log(data);
            response = data.responseJSON;
            console.log(response);
            if(response.error){
              for(key in response.message){
                $('#'+key+'-error').text(response.message[key]).show();
              }
            }
          },
          complete: function()
          {
            $('#loader').hide();
          }
        });
    });
    function openEditModal(profileName, profileId) {
      modal = $('#editModal');
      form = $('#editModal-form');
      td = $('#'+profileId);
      $(modal).modal('show');
      $('#editModal-form #profile-name').val(profileName);
      $('#editModal-form #profile-id').val(profileId);

    }


    $('#editModal').on('hidden.bs.modal', function(e){
      $('#editModal-form').trigger('reset');
      $('.invalid-feedback').text('')
    });


    function deleteProfile(profile_id){
      if(confirm("click ok to delete")){
        $.ajax({
          type: 'GET',
          url: "{{ route('admin.deleteUserProfile.get') }}",
          data: {'userProfileId' : profile_id},
          beforeSend: function()
          {
            $('#loader').show();
          },
          success: function(data)
          {
            if(data.error == false)
              location.reload();
          },
          complete: function()
          {
            $('#loader').hide();
          }
        });
      }
    }
  </script>
</x-slot>
</x-backend-layout>
