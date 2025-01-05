<x-backend-layout>
  <x-slot name="title">
    Order for report
  </x-slot>
  <x-slot name="css">
    <style id="jsbin-css">
      .tinted {
        background-color: #fff6b2 !important;
      }

      .selected {
        background-color: #f9c7c8 !important;
        border: solid red 1px !important;
        z-index: 1 !important;
      }

    </style>
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>List Order for report</h3>
          </div>
          <div class="title_right">

          </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Batch </h2>
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
                <div class="form-group">
                  <label for="batch">Select Batch</label>
                  <select id="batch" name="batchId">
                    <option></option>
                    @foreach($batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Category order for  <b id="batchName"></b> Batch associated with <b id="BatchProfileName"></b> User Profile</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <br>
                <div id="list" class="list-group">

                </div>
                <br>
                <button class="btn btn-rounder btn-primary" onclick="updateOrder()">Update Order</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </x-slot>
  <x-slot name="js">
    <!-- Latest Sortable -->
    <script src="https://raw.githack.com/SortableJS/Sortable/master/Sortable.js"></script>
    <script>
      category_list = Sortable.create(list, {
        group: 'shared',
        multiDrag: true,
        selectedClass: "selected",
        animation: 150,
        dataIdAttr: 'data-category-id',
        onSort: function (/**Event*/evt) {
          console.log(evt);
        },
      });
      $('#batch').on('change', function(e){
        selectElement = e.target;
        batch_id = $(selectElement).find(":selected").val();
        if(batch_id == ""){
          $('#list').empty();
          $('#add-questions').empty();
          return;
        }
        $.ajax({
          type: "GET",
          url: "{{ route("admin.categoryReportOrder.get") }}",
          data: {'batch_id': batch_id}, // serializes the form's elements.
          success: function(data)
          {
            console.log(data);
            $('#batchName').text(data.message.name)
            $('#BatchProfileName').text(data.message.user_profile.name);
            var allocatedMainDiv = $('#list');
            $(allocatedMainDiv).empty();
            $.each(data.message.batch_category, function(indx, obj){
              $(allocatedMainDiv).append(`
              <div class="list-group-item" data-category-id="${obj.category_id}"> ${obj.category.name} (${obj.category.name_in_report})</div>`);
            });
          }
        });
      });
      function updateOrder() {
        var list = category_list.toArray();
        if(list.length == 0){
          return;
        }
        $.ajax({
          type: "POST",
          url: "{{ route('admin.reportOrder.post') }}",
          data: {'category_id': list, 'batch_id' : batch_id},
          Success: function(data) {

          }
        })
      }
    </script>
  </x-slot>
</x-backend-layout>
