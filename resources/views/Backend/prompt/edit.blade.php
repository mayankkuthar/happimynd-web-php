<x-backend-layout>
  <x-slot name="title">
    Edit Prompt
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Edit Prompt</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                @include('Backend.includes.flash_message')
                <br>
                <form class="form-horizontal form-label-left" id="add-prompt-form" enctype="multipart/form-data" method="POST" action="{{ route('admin.prompt.update' ,['id' => $prompt->id]) }}">
                  @csrf
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Description<span class="required">*</span></label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="Describe the prompt" name="description" value="{{$prompt->description}}"required>
                    </div>
                  </div>
                  <div class="ln_solid"></div>
                  <div class="form-group">
                    <div class="col-md-9 col-sm-9  offset-md-3">
                      <button type="reset" class="btn btn-primary">Reset</button>
                      <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
<script>
 var t= {!! json_encode($prompt) !!}
 console.log(t)
</script>
