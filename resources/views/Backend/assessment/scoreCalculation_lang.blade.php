<x-backend-layout>
  <x-slot name="title">
    Score Calculation
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Scores</h3>
          </div>
          <div class="title_right">

          </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Batches</h2>
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
                <h2>Categories Allocated for  <b id="batchName"></b> Batch associated with <b id="BatchProfileName"></b> User Profile</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link" id="categories-collapse"><i class="fa fa-chevron-up"></i></a>
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
                <h2>Report Generation Data</h2>
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
                <div id="reportCharacteristics">

                </div>
                <button class="btn btn-round btn-success" id="addReportCharacterictic">Add</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- modal for cloning Batch -->
    <div class="modal fade bd-example-modal-lg" id="ratingPictureModal" tabindex="-1" role="dialog" aria-labelledby="ratingPictureModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ratingPictureModal">Select Rating picture</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row rating-pictures">
                <form id="ratingPictureForm" class="row">
                  <input type="hidden" name="report_characteristic_id" value="">
                  <input type="hidden" id="old_rating_picture_id">
                @foreach($ratingPictures as $picture)
                <div class="checkbox col">
                  <label>
                    <input type="radio" name="ratingPictureId" value="{{ $picture->id }}"><img src="{{ $picture->image }}" data-picture-id="{{ $picture->id }}" data-image-url="{{ $picture->image }}" class="img-fluid img-thumbnail" alt="Responsive image" width="100px" height="100px">
                  </label>
                </div>
                @endforeach
                </form>
              </div>
            </div>
            <button class="btn btn-round btn-success" onclick='saveRatingPicture()'>Save</button>
          </div>
        </div>
      </div>
      <!-- modal for cloning Batch -->

    </x-slot>
    <x-slot name="js">
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script>
        function makeid(l){
          var text = "";
          var char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
          for(var i=0; i < l; i++ ){
            text += char_list.charAt(Math.floor(Math.random() * char_list.length));
          }
          return "new-"+text;
        }

        $('#addReportCharacterictic').on("click", function(e){
          var rc = {
            "id" : makeid(4),
            "category_id": category_id,
            "batch_id": batch_id,
            "minimum_score": 0,
            "maximum_score": 1,
            "meter_scale_level_name": "",
            "summary": "",
            "summary_hindi": "",

            "WOL_fill_area":"",
            "included_in_report": ""
          }
          var allocatedMainDiv = $('#reportCharacteristics');
          $(allocatedMainDiv).append(createReportCharacteristic(rc));
          initializeEditor("summary-"+rc.id);
          initializeEditorHindi("summary_hindi-"+rc.id);

        });

        function createReportCharacteristic(rc) {
          var image='';
          if(rc.emoji){
            image=rc.emoji.image;
          }
          return `<form id="reportCharacteristicForm_${rc.id}" class="reportCharacteristicForm">
            <input type="text" name="category_id" value="${category_id}">
            <input type="text" name="batch_id" value="${batch_id}">
            <input type="text" name="report_characteristic_id" value="${rc.id}">
            <input type="text" name="rating_picture_id" value="${rc.rating_picture_id}">
            <div class="form-group">
              <div class="col-md-6 col-sm-12 form-group">
                <label class="control-label">Min Score:</label> <input type="text" name="min-score" class="form-control" value="${rc.minimum_score}" >
              </div>
              <div class="col-md-6 col-sm-12 form-group">
                <label class="control-label">Max Score:</label> <input type="text" name="max-score" class="form-control" value="${rc.maximum_score}" >
              </div>
            </div>
            <div class="form-group">
              <label class="control-label">Meter Scale Name</label>
              <input type="text" name="meter_scale_level_name" class="form-control" placeholder="Ex: extreme, severe" value="${rc.meter_scale_level_name}">
            </div>

            


            <label>English</label>
            <div class="form-group">
              <input type="hidden" name="summary" value="">
              <div id="summary-${rc.id}">${rc.summary}</div>
            </div>

            <label>Hindi</label>
            <div class="form-group">
              <input type="hidden" name="summary_hindi" value="">
              <div id="summary_hindi-${rc.id}">${rc.summary_hindi}</div>
            </div>

            <div class="form-group">
              <div class="control-label">WOL Fill area:</div>
              <input type="number" min="0" max="10" class="form-control" name="WOL_fill_area" value="${rc.WOL_fill_area}">
            </div>
            <div class="form-group">
              <label class="control-label">
                Include in report? <input type="checkbox" class="js-switch" name="included_in_report" checked="${rc.included_in_report}"/>
              </label>
            </div>
            <div class="form-inline">
              <label class="control-label">
                Rating Image
              </label>
              <button type="button" class="btn btn-primary btn-round" onclick="selectRatingPicture('${rc.id}','${rc.rating_picture_id}')">Select Emoji</button>
              <img class="img-responsive img-thumbnail" src="${image}" id="picture_${rc.id}" width="100px" height="100px">
            </div>
            <button class="btn btn-round btn-success submitButton" type="submit">Update</button>
            <button class="btn btn-round btn-success deleteButton">Delete</button>
          </form><hr>`;
        }
      </script>
      <script>
        function selectRatingPicture(id, picture_id){
          $('#ratingPictureModal').modal("show");
          console.log(id);
          if(picture_id.trim() != '')
            $('#ratingPictureForm input[name="ratingPictureId"][value="'+picture_id+'"]').prop('checked', true);
          $('#ratingPictureForm input[name="report_characteristic_id"]').val(id);
          $('#old_rating_picture_id').val(id);
        }
        function saveRatingPicture(){
          var rating_picture_id = $('#ratingPictureForm input[name="ratingPictureId"]:checked').val();
          var report_characteristic_id =$('#ratingPictureForm input[name="report_characteristic_id"]').val();
          console.log('saving')
          console.log(rating_picture_id)
          $('#reportCharacteristicForm_'+report_characteristic_id+' input[name="rating_picture_id"]').val(rating_picture_id);
          console.log('old='+$('#old_rating_picture_id').val());
          var picture_url = $('#ratingPictureForm').find('[data-picture-id="'+rating_picture_id+'"]').data('imageUrl');
          console.log(picture_url);
          $('#picture_'+$('#old_rating_picture_id').val()).attr('src', picture_url);
          $('#ratingPictureModal').modal("hide");
        }
        $('#ratingPictureModal').on('hidden.bs.modal', function(e){
          $('#ratingPictureForm input[name="ratingPictureId"]:checked').attr('checked',false)
        });


        let ckeditors = new Map();
        function initializeEditor(id = "summary"){
          ClassicEditor.create( document.querySelector( '#'+id ) ).then(editor => {
            ckeditors.set(editor.sourceElement.id, editor);
            // console.log(editor)
          })
          .catch( error => {
            console.error( error );
          } );
        }

        let ckeditors_hindi = new Map();
        function initializeEditorHindi(id = "summary_hindi"){
          ClassicEditor.create( document.querySelector( '#'+id ) ).then(editor => {
            ckeditors_hindi.set(editor.sourceElement.id, editor);
            // console.log(editor)
          })
          .catch( error => {
            console.error( error );
          } );
        }

        // initializeEditor();aasd
        function saveCalculationStep(category_id, batch_id) {
          console.log($('#calculation-step_'+category_id).val());
          data = {
            "category_id": category_id,
            "batch_id": batch_id,
            "calculation_step": $('#calculation-step_'+category_id).val(),
          }
          // console.log(data);
          $.ajax({
            type: "POST",
            url: "{{ route("admin.saveCalculationStep.post") }}",
            data: data,
            success: function(data){
              console.log(data);
            }
          });
        }
      </script>
      <script type="text/javascript">
        $('#reportCharacteristics').on("click", '.deleteButton', function(e){
          formId = $(this).parent().attr('id');
          if(isNaN(formId.split("_")[1])){
            $('#'+formId).remove();
            console.log("not number "+formId)
            return;
          }
          $.ajax({
            type: "GET",
            url: "{{ route("admin.deleteReportCharacteristic.get") }}",
            data: {'id': formId.split("_")}, // serializes the form's elements.
            success: function(data)
            {
              console.log(data);
              if(!data.error){
                $('#'+formId).remove();
              }
            }
          });
        });
        $('#reportCharacteristics').on("submit", '.reportCharacteristicForm', function(e){
          e.preventDefault();
          var formId = e.target.id
          console.log(e.target.id);
          var report_id = e.target.id.split('_')[1];
          console.log("report_id="+report_id)
          if(report_id == "new"){
            report_id = e.target.id.split('-')[1]+'-'+e.target.id.split('-')[2];
          }
          $('#reportCharacteristicForm_'+report_id+' input[name="summary"]').val(ckeditors.get('summary-'+report_id).getData());

          $('#reportCharacteristicForm_'+report_id+' input[name="summary_hindi"]').val(ckeditors_hindi.get('summary_hindi-'+report_id).getData());

          formData = new FormData(this);
          $.ajax({
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            url: "{{ route("admin.saveReportCharacteristic.post") }}",
            data: formData, // serializes the form's elements.
            success: function(data)
            {
              console.log(data);
              if(data.message){
                $('#submitButton').show().text('Update');
                $('#'+formId+' input[name="report_characteristic_id"]').val(data.message.id);
              }
            }
          });
        })
        $('#batch').on('change', function(e){
          selectElement = e.target;
          batch_id = $(selectElement).find(":selected").val();

          // // ckeditor.destroy();
          // $('#summary').empty();
          // initializeEditor();
          // $('#reportCharacteristicForm').trigger("reset");
          // $('input[name="report_characteristic_id"]').val('new');
          // $('input[name="category_id"]').val('');
          // $('input[name="batch_id"]').val('');

          if(batch_id == ""){
            $('#allocated-categories').empty();
            $('#add-questions').empty();
            return;
          }
          $.ajax({
            type: "GET",
            url: "{{ route("admin.getBatchCategories.get") }}",
            data: {'batch_id': batch_id}, // serializes the form's elements.
            success: function(data)
            {

              console.log(data);
              $('#batchName').text(data.message.allocated.name)
              $('#BatchProfileName').text(data.message.allocated.user_profile.name);
              var allocatedMainDiv = $('#allocated-categories');
              $(allocatedMainDiv).empty();
              $.each(data.message.allocated.batch_category, function(indx, obj){
                $(allocatedMainDiv).append(`
                <div class="form-group">
                  <label>
                    <input type="radio" class="flat question-category-checkbox" name="icheck" value="`+obj.category.id+`"> `+obj.category.name+`(`+obj.category.acronymn+`)`+`
                  </label>
                </div>
                <div class="">
                  <label class="control-label" for="calculation-step_${obj.category.id}">
                    Calculation Step
                  </label>
                  <input type="text" id="calculation-step_${obj.category.id}" class="form-control">
                  <button class="btn btn-round btn-success" id="calculation-${obj.category_id}" onclick="saveCalculationStep(${obj.category_id}, ${obj.batch_id})">Save</button>
                </div><br>`);
              });

              $('.question-category-checkbox').on('change', function(e){
                category_id = $(e.target).val()
                // $('#reportCharacteristicForm').trigger("reset");
                $('input[name="report_characteristic_id"]').val('new');
                // ckeditor.destroy();
                $('#summary').empty();
                $.ajax({
                  type: "GET",
                  url: "{{ route("admin.getBatchCategoryReportCharacteristics.get") }}",
                  data: {'batch_id': batch_id, 'category_id': category_id}, // serializes the form's elements.
                  success: function(data)
                  {
                    // console.log(e.target);
                    console.log(data);
                    $('#calculation-step_'+category_id).val(data.message.calculation_step);
                    $('input[name="category_id"]').val(category_id);
                    $('input[name="batch_id"]').val(batch_id);
                    $('#submitButton').show().text('Save');
                    if(data.message){
                      var allocatedMainDiv = $('#reportCharacteristics');
                      $(allocatedMainDiv).empty();
                      $.each(data.message.characteristics, function(indx, obj){
                        $(allocatedMainDiv).append(createReportCharacteristic(obj));
                        initializeEditor("summary-"+obj.id);
                        initializeEditorHindi("summary_hindi-"+obj.id);

                      });
                      // $('#submitButton').show().text('Update');
                      // $('input[name="report_characteristic_id"]').val(data.message.id);
                      // $('input[name="min-score"]').val(data.message.minimum_score);
                      // $('input[name="max-score"]').val(data.message.maximum_score);
                      // $('input[name="meter_scale_level_name"]').val(data.message.meter_scale_level_name);
                      // $('input[name="WOL_fill_area"]').val(data.message.WOL_fill_area);
                      // $('#summary').html(data.message.summary);
                      // if(data.message.included_in_report){
                        //   if(!$('input[name="included_in_report"]').is(":checked")){
                          //     $('input[name="included_in_report"]').trigger("click");
                          //   }
                          // }
                          // else{
                            //   $('input[name="included_in_report"]').trigger("click");
                            //   if($('input[name="included_in_report"]').is(":checked")){
                              //     $('input[name="included_in_report"]').trigger("click");
                              //     console.log('s');
                              //   }
                              // }
                            }
                          }
                        });
                      });
                    }
                  });
                });
              </script>
            </x-slot>
          </x-backend-layout>
