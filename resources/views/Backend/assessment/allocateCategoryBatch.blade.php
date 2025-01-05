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
                  <h2>Allocate Categories to Batch </h2>
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
                  <button type="button" class="btn btn-success" onclick="allocateCategory()">Update</button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
						<div class="col-md-12 col-sm-12 ">
							<div class="x_panel">
								<div class="x_title">
									<h2>Categories Allocated for  <b id="batchName"></b> Batch associated with <b id="BatchProfileName"></b> User Profile</h2>
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
                  <div id="allocated-categories">

                  </div>
								</div>
							</div>
						</div>
					</div>
          <div class="row">
						<div class="col-md-12 col-sm-12 ">
							<div class="x_panel">
								<div class="x_title">
									<h2>Unallocated categories</h2>
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
                  <div id="unallocated-categories">

                  </div>
								</div>
							</div>
						</div>
					</div>
        </div>
      </div>
    </div>

  </x-slot>
  <x-slot name="js">
  <script type="text/javascript">
  var allocatedCategoryIds = new Set();
  var unallocatedCategoryIds = new Set();
  $('#batch').on('change', function(e){
    allocatedCategoryIds.clear()
    unallocatedCategoryIds.clear()
     selectElement = e.target;
     batch_id = $(selectElement).find(":selected").val();
    $.ajax({
          type: "GET",
          url: "{{ route("admin.getBatchCategories.get") }}",
          data: {'batch_id': batch_id},
          success: function(data)
          {
            console.log(data);
            $('#batchName').text(data.message.allocated.name)
            $('#BatchProfileName').text(data.message.allocated.user_profile.name);
            var allocatedMainDiv = $('#allocated-categories');
            $(allocatedMainDiv).empty();
            $.each(data.message.allocated.batch_category, function(indx, obj){
              $(allocatedMainDiv).append(`<div class="checkbox"><label><input type="checkbox" class="flat allocate-checkbox" checked="checked" value="`+obj.category.id+`"> `+obj.category.name+`(`+obj.category.acronymn+`)`+`(  Questions: ${obj.questions_count})
													</label>
												</div>`);
              allocatedCategoryIds.add(obj.category.id);
            });
            var unallocatedMainDiv = $('#unallocated-categories');
            $(unallocatedMainDiv).empty();
            $.each(data.message.unallocated, function(indx, obj){
              $(unallocatedMainDiv).append(`<div class="checkbox"><label><input type="checkbox" class="flat unallocate-checkbox" value=`+obj.id+`> `+obj.name+`(`+obj.acronymn+`)`+`
													</label>
												</div>`);
              unallocatedCategoryIds.add(obj.id);
            });

            $('.allocate-checkbox').on('change', function(e){
              var category_id = parseInt(e.target.value);
              if($(e.target).is(":checked")){
                unallocatedCategoryIds.delete(category_id);
                allocatedCategoryIds.add(category_id);
              }
              else{
                unallocatedCategoryIds.add(category_id);
                allocatedCategoryIds.delete(category_id);
              }
            });

            $('.unallocate-checkbox').on('change', function(e){
              var category_id = parseInt(e.target.value);
              if($(e.target).is(":checked")){
                allocatedCategoryIds.add(category_id);
                unallocatedCategoryIds.delete(category_id);
              }
              else{
                unallocatedCategoryIds.add(category_id);
                allocatedCategoryIds.delete(category_id);
              }
            });
          }
        });
  });

  function allocateCategory(){
    console.log(allocatedCategoryIds);
    var batch_id = $('#batch').find(":selected").val();
    if(batch_id == '')
      return ;
    $.ajax({
          type: "GET",
          url: "{{ route("admin.updateBatchCategory.post") }}",
          data: {
            'batch_id': batch_id,
            "_token" : "{{ csrf_token() }}",
            'allocated_category': Array.from(allocatedCategoryIds).join(','),
            'unallocated_category': Array.from(unallocatedCategoryIds).join(',')
          },
          success: function(data)
          {
            console.log('updated');
            console.log(data);
            $('#batch').trigger('change');
          }
  });
}
  </script>
  </x-slot>
</x-backend-layout>
