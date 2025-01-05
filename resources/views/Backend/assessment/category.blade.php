<x-backend-layout>
  <x-slot name="title">
    Create User Profile
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
                  <form class="user-form" action="{{ route('admin.addCategory.post') }}" method="post">
                    @csrf
                    <div class="form-group row @error('categoryName') bad @enderror">
                      <label class="control-label col-md-3 col-sm-3 ">category Name<span class="required">*</span></label>
                      <div class="col-md-9 col-sm-9 ">
                        <input class="form-control" name="categoryName" placeholder="Student" required="required" value="{{ old('categoryName') }}" required/>
                      </div>
                      @error('categoryName')
                      <div class="alert" id="categoryName-error"> {{ $message }}</div>
                      @enderror
                    </div>
                    <div class="form-group row">
                      <label for="category-acronymn" class="control-label col-md-3 col-sm-3">Acronymn:</label>
                      <div class="col-md-9 col-sm-9 ">
                        <input type="text" class="form-control" id="category-acronymn" name="categoryAcronymn" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="nameInReport" class="control-label col-md-3 col-sm-3">Name in report:</label>
                      <div class="col-md-9 col-sm-9  ">
                        <input type="text" class="form-control" id="category-nameInReport" name="categoryNameInReport" required>
                        </div>
                    </div>
                    <div class="form-group row">
                      <label class="control-label col-md-3 col-sm-3">Wheel of life Color</label>
                      <div class="col-md-9 col-sm-9  ">
                        <div class="input-group demo2">
                          <input type="text" value="#e01ab5" class="form-control" name="color" />
                          <span class="input-group-addon"><i></i></span>
                        </div>
                      </div>
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
                              <th>Batch</th>
                              <th>Category Name</th>
                              <th>Acronym</th>
                              <th>Name in Report</th>
                              <th> Wheel of life color</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($categories as $category)
                            <tr>
                              <th>{{ $category->batch->name }}</th>
                              <td id="{{ $category->category->id.'-name' }}" data-name="{{ $category->category->name }}">{{ $category->category->name }}</td>
                              <td id="{{ $category->category->id.'-acronymn' }}" data-acronymn="{{ $category->category->acronymn }}">{{ $category->category->acronymn }}(questions: {{ $category->questions_count }})</td>
                              <td id="{{ $category->category->id.'-nameInReport' }}" data-name-in-report="{{ $category->category->name_in_report }}">{{ $category->category->name_in_report }}</td>
                              <td id="{{ $category->category->id.'-color' }}" data-color="{{ $category->category->color }}">{{ $category->category->color }} <span class="color-box" style="background: {{ $category->category->color }};"></span></td>
                              <td>
                                <span
                                onclick="openEditCategoryModal({{ $category->category->id }})"
                                >
                                <i class="fa fa-edit"></i>
                              </span>
                              <span onclick="deleteCategory({{ $category->category->id }});">
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
          <!-- modal for adding/editing category -->
          <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="categoryModalLabel">Category</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="category-form" action="{{ route('admin.editCategory.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="categoryId" id="category-id">
                    <div class="form-group">
                      <label for="category-name" class="col-form-label">category Name:</label>
                      <input type="text" class="form-control" id="category-name" name="categoryName">
                      <div class="invalid-feedback">
                        name already used
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="category-acronymn" class="col-form-label">Acronymn:</label>
                      <input type="text" class="form-control" id="category-acronymn" name="categoryAcronymn">
                    </div>
                    <div class="form-group">
                      <label for="nameInReport" class="col-form-label">Name in report:</label>
                      <input type="text" class="form-control" id="category-nameInReport" name="categoryNameInReport">
                    </div>
                     <div class="form-group">
                      <label class="control-form-label">Wheel of life Color</label>
                        <div class="input-group demo2">
                          <input type="text" value="#e01ab5" class="form-control" id="category-color" name="color" />
                          <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" value="submit" id="submit-button" onclick="$('#category-form').submit();">Update</button>
                </div>
              </div>
            </div>
          </div>
          <!-- modal for adding/editing category -->
        </div>
      </div>
    </div>
  </div>
</x-slot>
<x-slot name="css">
  <style>
    .color-box {
    display: inline-flex;
    height: 13px;
    width: 14px
    }
  </style>
</x-slot>
<x-slot name="js">
  <script type="text/javascript">
    // this is the id of the form
    $("#category-form").submit(function(e) {
      console.log('s');

      e.preventDefault(); // avoid to execute the actual submit of the form.

      form = $(this);
      var url = form.attr('action');

      //return if name is empty
      if($(this).find('#category-name').val() == ''){
        return;
      }
      $.ajax({
        type: form.attr('method'),
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
          // alert(Campaign added); // show response from the php script.
          location.reload();
        }
      });
    });
    function openEditCategoryModal(category_id) {
      modal = $('#categoryModal');
      form = $('#category-form');
      $(modal).modal('show');
      console.log(category_id);
      $('#category-id').val(category_id);
      $('#categoryModal #category-name').val($('#'+category_id+'-name').data('name'));
      $('#categoryModal #category-acronymn').val($('#'+category_id+'-acronymn').data('acronymn'));
      $('#categoryModal #category-nameInReport').val($('#'+category_id+'-nameInReport').data('nameInReport'));
      $('#categoryModal #category-color').val($('#'+category_id+'-color').data('color'));
      $('#categoryModal #category-color').trigger('change')
    }


    function deleteCategory(category_id){
      if(confirm("click ok to delete")){
        $.ajax({
          type: 'POST',
          url: "{{ route('admin.deleteCategory.post') }}",
          data: {'category_id' : category_id, "_token": "{{ csrf_token() }}" },
          success: function(data)
          {
            location.reload();
          }
        });
      }
    }
  </script>
</x-slot>
</x-backend-layout>
