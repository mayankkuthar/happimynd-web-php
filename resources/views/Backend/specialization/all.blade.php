<x-backend-layout>
  <x-slot name="title">
    Specializations
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <button class="btn btn-primary btn-round" onclick="openAddModal('{{ route('admin.specialization.post') }}')">Add</button>
            <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30">

              </p>
              <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>specialization</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($specializations as $specialization)
                  <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td id="{{ $specialization->id.'-name' }}">{{ $specialization->name }}</td>
                    <td>
                      <span
                      onclick="openEditModal({{ $specialization->id }}, '{{ route('admin.specialization.put',['id'=>$specialization->id]) }}')"
                      >
                      <i class="fa fa-edit"></i>
                    </span>
                    <span onclick="del('{{ route('admin.specialization.delete',['id'=>$specialization->id]) }}')">
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
  <!-- page content -->
  <!-- modal for adding/editing category -->
  <div class="modal fade" id="specialization-modal" tabindex="-1" role="dialog" aria-labelledby="specializationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="specializationLabel">Specialization</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="specialization-form" action="" method="POST">
            @csrf
            <input type="hidden" name="categoryId" id="specialization-id">
            <div class="form-group">
              <label for="specialization-name" class="col-form-label">Name:</label>
              <input type="text" class="form-control" id="specialization-name" name="name">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" value="submit" id="submit-button" onclick="$('#specialization-form').submit();">Update</button>
        </div>
      </div>
    </div>
  </div>
  <!-- modal for adding/editing category -->
</x-slot>

<x-slot name="js">
  <script type="text/javascript">
  var formModal = '#specialization-modal';
  var form = "#specialization-form";
  var formElements = '#specialization';
  $(formModal).on('hidden.bs.modal', function(e){
    $(form).attr('action','');
    $(form).attr('method','POST');
    $(form).trigger('reset');
    });
    // this is the id of the form
    $(form).submit(function(e) {
      console.log('s');

      e.preventDefault(); // avoid to execute the actual submit of the form.

      form = $(this);
      var url = form.attr('action');

      //return if name is empty
      if($(form).find(formElements+'-name').val() == ''){
        return;
      }
      $.ajax({
        type: form.attr('method'),
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
          if(data.error == false){
            location.reload();
          }
        }
      });
    });
    function openEditModal(id, url) {
      modal = $(formModal);
      form = $(form);
      $(modal).modal('show');
      $(form).attr('action', url);
      $(form).attr('method','PUT');
      $('#submit-button').text('Update');
      console.log(id);
      $(formElements+'-id').val(id);
      $(formElements+'-name').val($('#'+id+'-name').text());
    }

    function openAddModal(url) {
      modal = $(formModal);
      $(modal).modal('show');
      $(form).attr('action', url);
      $(form).attr('method','POST');
      $('#submit-button').text('Add');
    }


    function del(url){
      if(confirm("confirm to delete")){
        $.ajax({
          type: 'DELETE',
          url: url,
          success: function(data)
          {
            if(data.error == false){
              location.reload();
            }
          }
        });
      }
    }
  </script>
</x-slot>
</x-backend-layout>
