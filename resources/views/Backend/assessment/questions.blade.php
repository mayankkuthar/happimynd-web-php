<x-backend-layout>
  <x-slot name="title">
    Create User Profile
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Add/Modify Questions</h3>
          </div>
          <!-- <div class="title_right">
              <a style="    float: right;" href="{{url('admin/import-questions')}}">
                <p class="btn btn-primary">Import Questions</p>
              </a>
          </div> -->
        </div>


          <!-- <div> -->
            <!-- <a href="{{url('admin/import-questions')}}">
              <p class="btn btn-primary">Import Questions</p>
            </a> -->
          <!-- </div> -->
        
          <div class="clearfix"></div>
          <div class="row">
            <div class="col-md-12 col-sm-12 ">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Batch Questions </h2>
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
                  <h2>Modify Question</h2>
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
                  <div id="add-questions">
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
      function makeid(l){
        var text = "";
        var char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for(var i=0; i < l; i++ ){
          text += char_list.charAt(Math.floor(Math.random() * char_list.length));
        }
        return "new-"+text;
      }
      function createNewOptionForQuestion(question_id) {

        //dummy data just to create feilds
        optns = [{ "id": makeid(4), "option": "","pivot":{"id":makeid(4),"weightage":""}}];

        $('#options-for-questionid-'+question_id).append(createOptions(optns, question_id));
      }
      function createOptions(options, question_id){
        op = '';
        $.each(options, function(indx, obj){

          op += `<div class="col-md-6 col-sm-12 form-group">
            <label class="control-label">Option:</label> <input type="text" name="option-${obj.id}" class="questionid-${question_id} optionid-${obj.id} form-control" value="${obj.option}" >
          </div>
          <div class="col-md-6 col-sm-12 form-group">
            <label class="control-label">Score:</label> <input type="text" name="score-${obj.id}" class="questionid-${question_id} optionid-${obj.id}-score form-control" value="${obj.pivot.weightage}" >
          </div>
          `;
        });
        return op;
      }

      function addQuestion(){
        //dummy data just to create feilds
        question = { "id": makeid(4), "question": "start typing here....", "option":[{ "id": makeid(4), "option": "","pivot":{"id":makeid(4),"weightage":""}}]};
        $('#add-questions').append(createQuestion(question));
      }

      function createQuestion(question) {
        return `<form id="addQuestionForm-${question.id}" class="addQuestionForm">
          <input type="hidden" name="_token" value="${csrf_token}">
          <input type="hidden" name="category_id" value="${category_id}">
          <input type="hidden" name="check_new_question" value="${question.id}">
          <input type="hidden" name="batch_id" value="${batch_id}">
        <div class="form-group">
          <div class="form-group row">
            <label class="control-label" for="question-${question.id}">
              Question:
            </label>
            <textarea id="question-${question.id}" name="question-${question.id}" type="text" class="questionid-${question.id} resizable_textarea form-control question-category-checkbox" name="question-${question.id} value="${question.question}">${question.question}</textarea>
          </div>
          <div id="options-for-questionid-${question.id}" class="row">`+createOptions(question.option, question.id)+`</div>
          <button class="btn btn-round btn-success" onclick="createNewOptionForQuestion('${question.id}')">Add option</button>
          <button class="btn btn-round btn-success" onclick="updateQuestion('${question.id}')">Update</button>
          <button class="btn btn-round btn-success" onclick="deleteQuestion('${question.id}')">Delete</button>
        </div>
        <hr>
        </form>`;
      }
      $(document).on("submit", ".addQuestionForm", function(e){
          e.preventDefault();
          return  false;
      });
      function updateQuestion(id) {
        question_class = "questionid-"+id;
        complete_div = $('.'+question_class);
        console.log($('#addQuestionForm-'+id).serialize());
        $.ajax({
          type: "POST",
          url: "{{ route("admin.updateQuestion.post") }}",
          data: $('#addQuestionForm-'+id).serialize(), // serializes the form's elements.
          success: function(data){
            console.log(data);
            $('.question-category-checkbox[value="'+category_id+'"]').trigger("change");
          }
        });
      }

      function deleteQuestion(question_id) {
        if(question_id.includes('new-')){
          $('#addQuestionForm-'+question_id).remove();
        }
        else{
          $.ajax({
            type: "POST",
            url: "{{ route("admin.deleteQuestion.post") }}",
            data: {'question_id': question_id, "_token": csrf_token}, // serializes the form's elements.
            success: function(data){
              if(data.error == false){
                $('#addQuestionForm-'+question_id).remove();
              }
            }
          });
        }
      }

      $('#batch').on('change', function(e){
        selectElement = e.target;
        batch_id = $(selectElement).find(":selected").val();
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
              <div class="checkbox">
                <label>
                  <input type="radio" class="flat question-category-checkbox" name="icheck" value="`+obj.category.id+`"> `+obj.category.name+`(`+obj.category.acronymn+`)`+`  (Questions: ${obj.questions_count})
                </label>
              </div>`);
            });

            $('.question-category-checkbox').on('change', function(e){
              category_id = $(e.target).val()
              $.ajax({
                type: "GET",
                url: "{{ route("admin.getBatchCategoryQuestions.get") }}",
                data: {'batch_id': batch_id, 'category_id': category_id}, // serializes the form's elements.
                success: function(data)
                {
                  console.log(data);
                  var allocatedMainDiv = $('#add-questions');
                  if($('#add-questions').next().text() == "Add Question"){
                    $('#add-questions').next().remove();
                  }

                  $(allocatedMainDiv).empty();
                  $.each(data.message.questions, function(indx, obj){
                    console.log(obj);
                    $(allocatedMainDiv).append(createQuestion(obj));
                  });
                  $('<button class="btn btn-round btn-success" onclick="addQuestion()">Add Question</button>').insertAfter(allocatedMainDiv);
                }
              });
            });
          }
        });
      });
    </script>
  </x-slot>
</x-backend-layout>
