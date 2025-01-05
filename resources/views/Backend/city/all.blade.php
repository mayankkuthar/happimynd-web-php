<x-backend-layout>
  <x-slot name="title">
    Cities
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <button class="btn btn-primary btn-round" onclick="openAddModal('{{ route('admin.city.post') }}')">Add</button>
            <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30">

              </p>
              <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>city</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($cities as $city)
                  <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td id="{{ $city->id.'-name' }}">{{ $city->name }}</td>
                    <td>
                      <span
                      onclick="openEditModal({{ $city->id }}, '{{ route('admin.city.put',['id'=>$city->id]) }}')"
                      >
                      <i class="fa fa-edit"></i>
                    </span>
                    <span onclick="del('{{ route('admin.city.delete',['id'=>$city->id]) }}')">
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
  <div class="modal fade" id="city-modal" tabindex="-1" role="dialog" aria-labelledby="cityLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cityLabel">Expert Level</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="city-form" action="" method="POST">
            @csrf
            <input type="hidden" name="categoryId" id="city-id">
            <div class="form-group">
              <label for="city-name" class="col-form-label">Name:</label>
              <input type="text" class="form-control" id="city-name" name="name">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" value="submit" id="submit-button" onclick="$('#city-form').submit();">Update</button>
        </div>
      </div>
    </div>
  </div>
  <!-- modal for adding/editing category -->
</x-slot>

<x-slot name="js">
  <script type="text/javascript">
  var formModal = '#city-modal';
  var form = "#city-form";
  var formElements = '#city';
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
