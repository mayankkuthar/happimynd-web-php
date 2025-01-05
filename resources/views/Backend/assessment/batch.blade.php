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
                  <h2>Create Batch </h2>
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
                  <form class="user-form" action="{{ route('admin.addBatch.post') }}" method="post">
                    @csrf
                    <div class="field item form-group @error('batchName') bad @enderror">
                      <label class="col-form-label col-md-3 col-sm-3  label-align">Batch Name<span class="required">*</span></label>
                      <div class="col-md-6 col-sm-6">
                        <input class="form-control" name="batchName" placeholder="Student" required="required" value="{{ old('batchName') }}" required/>
                      </div>
                      @error('batchName')
                      <div class="alert" id="batchName-error"> {{ $message }}</div>
                      @enderror
                    </div>
                    <div class="field item form-group @error('Profile') bad @enderror">
                      <label class="col-form-label col-md-3 col-sm-3  label-align">Select profile<span class="required">*</span></label>
                      <div class="col-md-6 col-sm-6">
                        <select class="form-control" name="userProfileId"required="required">
                          @foreach($userProfiles as $userProfile)
                          <option value="{{ $userProfile->id }}">{{ $userProfile->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      @error('profile')
                      <div class="alert" id="profile-error"> {{ $message }}</div>
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
                              <th>Batch Name</th>
                              <th>Profile</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($batches as $batch)
                            <tr>
                              <td>{{ $batch->name }}</td>
                              <td>{{ $batch->userProfile->name }} (Pending Assessments: {{ $batch->assessment()->whereNull('ended_at')->get()->count() }})(Total Questions: {{ $batch->questions_count }})</td>
                              <td class="action">
                                <div class="fa-hover col-md-3 col-sm-4"
                                onclick="openEditbatchModal({{ $batch->id }}, '{{ $batch->name }}', '{{ $batch->user_profile_id }}' )"
                                >
                                <a href=""><i class="fa fa-edit"></i></a>
                              </div>
                              <div class="fa-hover col-md-3 col-sm-4" onclick="deleteBatch({{ $batch->id }});">
                                <a href=""><i class="fa fa-trash-o"></i></a>
                              </div>
                              <div class="fa-hover col-md-3 col-sm-4" href=""onClick = "cloneBatch({{ $batch->id }})">
                                <a href=""><i class="fa fa-copy"></i></a>
                              </div>
                              <div class="fa-hover col-md-3 col-sm-4" href=""onClick = "getAllCategories({{ $batch->id }})">
                                <a href=""><i class="fa fa-copy"></i>Copy from another Batches</a>
                              </div>
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
          <!-- modal for adding/editing Batch -->
          <div class="modal fade" id="batchModal" tabindex="-1" role="dialog" aria-labelledby="batchModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="batchModalLabel">Batch</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="batch-form" action="{{ route('admin.editBatch.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="batchId" id="batch-id">
                    <div class="form-group">
                      <label for="batch-name" class="col-form-label">Batch Name:</label>
                      <input type="text" class="form-control" id="batch-name" name="batchName">
                      <div class="invalid-feedback">
                        name already used
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="userProfiles" class="col-form-label">User Profiles:</label>

                      <div class="form-check profile-checkbox">
                        <select name="userProfileId" id="userProfileId-select" required>
                          @foreach($userProfiles as $userProfile)
                          <option value="{{ $userProfile->id }}">{{ $userProfile->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" value="submit" id="submit-button" onclick="$('#batch-form').submit();">Update</button>
                </div>
              </div>
            </div>
          </div>
          <!-- modal for adding/editing Batch -->
          <!-- modal for cloning Batch -->
          <div class="modal fade" id="cloneBatchModalLabel" tabindex="-1" role="dialog" aria-labelledby="cloneBatchModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="cloneBatchModalLabel">Clone Batch details</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="clone-batch-form" action="{{ route('admin.cloneBatch.get') }}" method="get">
                    @csrf
                    <input type="hidden" name="batch_id" id="batch_id">
                    <div class="form-group">
                      <label for="batch-name" class="col-form-label">New Batch Name:</label>
                      <input type="text" class="form-control" id="batch-name" name="batchName">
                      <div class="invalid-feedback">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="userProfiles" class="col-form-label">User Profiles:</label>

                      <div class="form-check profile-checkbox">
                        <select name="userProfileId" id="userProfileId-select" required>
                          @foreach($userProfiles as $userProfile)
                          <option value="{{ $userProfile->id }}">{{ $userProfile->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label">Check categories to copy</label>
                      <div class="form-check" id="clone-batch-category-list">
                      </div>
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" id="submit-button">Update</button>
                </div>
                </form>
              </div>
            </div>
          </div>
          <!-- modal for cloning Batch -->

          <!-- modal for Copying categories from another category -->
          <div class="modal fade  bs-example-modal-lg" id="copyCategoryModal" tabindex="-1" role="dialog" aria-labelledby="copyCategoryModal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="copyCategoryModal">Copy Categories</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="copy-categories-form" action="{{ route('admin.copyCategories.get') }}" method="get">
                    @csrf
                    <input type="hidden" name="batch_id" id="batch_id">
                    <div class="form-group">
                      <label class="control-label">Check categories to copy</label>
                      <div class="form-check" id="copy-category-list">
                      </div>
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" id="submit-button">Update</button>
                </div>
                </form>
              </div>
            </div>
          </div>
          <!-- modal for Copying categories from another category -->
        </div>
      </div>
    </div>
  </div>
</x-slot>
<x-slot name="js">
  <script type="text/javascript">
  function getAllCategories(batch_id) {
    $('#copyCategoryModal').modal('show');
    $('#copy-category-list').empty();
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.getAllBatchCategories.get') }}",
        data: {'batch_id': batch_id},
        success: function(data)
        {
          console.log(data);
          if(!data.error){
            $('#copy-categories-form input[name="batch_id"]').val(batch_id);
            checkBoxes = `<div class="table-responsive"><table class="table table-striped jambo_table bulk_action">
              <thead>
              <tr class="headings">
                <th>Category</th>
                <th>Questions Count</th>
                <th>Batch Name</th>
                <th>User Profile</th>
              </tr>
              </thead>`;
            data.message.forEach(function(obj, indx){
              console.log(obj);
              checkBoxes += `<tr>
              <td><div class="checkbox">
                <label>
              <input type="checkbox" value="${obj.id}" class="category-checkbox" name="category[]">${obj.name} (${obj.acronymn})</label></div></td>
              <td>${obj.question_count}</td>
              <td>${obj.batch[0].name}</td>
              <td>${obj.batch[0].user_profile.name}</td>
              </tr>
              `;
            })
            checkBoxes+='</table></div>'
            $('#copy-category-list').append(checkBoxes);
          }
        }
      });
  }
  $('#copy-categories-form').on('submit', function(e){
      e.preventDefault();
      var batch_id = $('#copy-categories-form input[name="batch_id"]').val();
      console.log($('#copy-categories-form').serialize());
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.copyCategories.get') }}",
        data: $('#copy-categories-form').serialize(),
        beforeSend:function(){
          $('#copyCategoryModal').modal('hide');
          $('#loader').show();
        },
        complete: function(){
          $('#loader').hide();
        },
        success: function(data)
        {
          console.log(data);
          if(data.error == false){
            //batch cloned
            location.reload();
          }
        }
      });
    })
    function cloneBatch(batch_id) {
      $('#cloneBatchModalLabel').modal('show');
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.batch.get') }}",
        data: {'batch_id': batch_id},
        success: function(data)
        {
          console.log(data);
          if(!data.error){
            $('#clone-batch-form input[name="batch_id"]').val(data.message.id);
            checkBoxes = '';
            data.message.batch_category.forEach(function(obj, indx){
              obj = obj['category']
              console.log(obj);
              checkBoxes += `
              <div class="checkbox">
                <label>
              <input type="checkbox" value="${obj.id}" class="category-checkbox" name="category[]">${obj.name} (${obj.acronymn}) (Questions: ${obj.question_count})
              </label>
              </div>
              `;
            })
            $('#clone-batch-category-list').append(checkBoxes);
          }
        }
      });
    }
    $('#clone-batch-form').on('submit', function(e){
      e.preventDefault();
      var batch_id = $('#clone-batch-form input[name="batch_id"]').val();
      console.log($('#clone-batch-from').serialize());
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.cloneBatch.get') }}",
        data: $('#clone-batch-form').serialize(),
        beforeSend:function(){
          $('#loader').show();
        },
        complete: function(){
          $('#loader').hide();
        },
        success: function(data)
        {
          if(data.error == false){
            //batch cloned
            location.reload();
          }
        }
      });
    })
    $('.action a').on('click', function(e){
      e.preventDefault();
    });
    // $('.category-checkbox').on('change', function(e){
    //   var element = e.target;
    //   if($(element).is(":checked")){

    //   }
    // })

    $('.status-button').on('change', function(e){
      var userProfileId = e.target.id.split('_')[1]
      console.log(e.target);
      console.log($(e.target).is(":checked"));
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.changeUserProfileStatus.get') }}",
        data: {'userProfileId':userProfileId, 'status' : ($(e.target).is(":checked") == true)? 1: 0 },
        success: function(data)
        {
          // location.reload();
        }
      });
    });
    // this is the id of the form
    $("#batch-form").submit(function(e) {
      console.log('s');

      e.preventDefault(); // avoid to execute the actual submit of the form.

      form = $(this);
      var url = form.attr('action');

      //return if name is empty
      if($(this).find('#batch-name').val() == ''){
        return;
      }
      $.ajax({
        type: form.attr('method'),
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
          // alert(batch added); // show response from the php script.
          location.reload();
        }
      });
    });
    function openEditbatchModal(batch_id, batch_name, user_profile_id) {
      modal = $('#batchModal');
      form = $('#batch-form');
      $(modal).modal('show');
      $('#batch-id').val(batch_id);
      $('#batch-name').val(batch_name);
      $('#userProfileId-select option[value="'+user_profile_id+'"]').attr('selected','selected')
    }


    $('#batchModal').on('hidden.bs.modal', function(e){
      $('#batch-form').trigger('reset');
      $('#submit-button').text('Add');
      $('#batch-form').find('#batch-name').removeClass('is-invalid')
    });

     $('#cloneBatchModalLabel').on('hidden.bs.modal', function(e){
      $('#clone-batch-form').trigger('reset');
      $('#clone-batch-category-list').empty();
    });


    function deleteBatch(batch_id){
      if(confirm("click ok to delete")){
        $.ajax({
          type: 'POST',
          url: "{{ route('admin.deleteBatch.post') }}",
          data: {'batch_id' : batch_id},
          beforeSend:function(){
            $('#loader').show();
          },
          complete: function(){
            $('#loader').hide();
          },
          success: function(data)
          {
            if(!data.error && data.message){
              location.reload();
            }
          }
        });
      }
    }
  </script>
</x-slot>
</x-backend-layout>
