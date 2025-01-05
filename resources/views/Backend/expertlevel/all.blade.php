<x-backend-layout>
  <x-slot name="title">
    All Psychologist
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30">

              </p>
              <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Expert Level</th>
                    <th>Pricing</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($expertLevels as $expertLevel)
                  <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td id="{{ $expertLevel->id.'-name' }}">{{ $expertLevel->name }}</td>
                    <td>
                    @foreach($expertLevel->plan as $plan)
                    <table>
                        <tr>
                          <td>Sessions</td>
                          <td>Cost Price</td>
                          <td>Discount</td>
                          <td>Discounted Price</td>
                        </tr>
                        <tr>
                          <td>{{ $plan->getSessionDuration() }}</td>
                          <td>{{ $plan->getCostPrice() }}</td>
                          <td>{{ $plan->getDiscount() }}</td>
                          <td>{{ $plan->getSellingPrice() }}</td>
                        </tr>
                    </table>
                    @endforeach
                    </td>
                    <td>
                      <a href="{{ route('admin.editExpertLevel.get',['id'=>$expertLevel->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                      <a href="#" onclick="del('{{ route('admin.expertLevel.delete',['id'=>$expertLevel->id]) }}')" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete </a>
                    </td>
                    {{-- <td>
                      <span
                      onclick="openEditModal({{ $expertLevel->id }}, '')"
                      >
                      <i class="fa fa-edit"></i>
                    </span>
                    <span onclick="del('{{ route('admin.expertLevel.delete',['id'=>$expertLevel->id]) }}')">
                      <i class="fa fa-trash-o"></i>
                    </span>
                  </td> --}}
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
</x-slot>

<x-slot name="js">
  <script type="text/javascript">
    var formModal = '#expert-level-modal';
    var form = "#expert-level-form";
    var formElements = '#expert-level';
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
