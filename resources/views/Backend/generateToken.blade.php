<x-backend-layout>
  <x-slot name="title">
    Generate Tokens
  </x-slot>
  <x-slot name="css">
      <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
      <link href="{{ asset('assets/Backend/css/plugins/nprogress.css') }}" rel="stylesheet">
      <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('assets/Backend/css/plugins/buttons.bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('assets/Backend/css/plugins/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('assets/Backend/css/plugins/responsive.bootstrap.min.css') }}" rel="stylesheet">
  </x-slot>
  <x-slot name="js">
      <script src="{{ asset('assets/Backend/js/plugins/fastclick.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/nprogress.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/icheck.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.buttons.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/buttons.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/buttons.flash.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/buttons.html5.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/buttons.print.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.fixedHeader.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/responsive.bootstrap.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/jszip.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/pdfmake.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/vfs_fonts.js') }}"></script>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row mt-4">
        <div class="col-md-6">
          @if((session('error')))
          <div class="alert alert-danger alert-dismissible fade show terms__addtitle" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          @endif
        </div>
      </div>

      <div class="x_content">
        <form class="form-horizontal form-label-left col-12" method="POST" action="{{ route('admin.generateToken.post') }}" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="generate_unique_id">Number of Token</label>
              <input name="token_count" type="number" class="form-control" id="generate_unique_id" placeholder="Enter count" value="{{ Session::get('ticket_count') }}" required="true" min="1">
            </div>
            <div class="form-group col-sm-6">
              <label for="organization">Organization</label>
              <select name="organization_id" class="form-control" required="true" id="organization">
                <option>
                </option>
                @foreach($organizations as $organization)
                  <option value="{{ $organization->id }}" @if(Session::has('organization_id') && Session::get('organization_id') == $organization->id) @php $organization_name = $organization->name @endphp selected="selected" @endif >{{ $organization->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-sm-6">
              <div class="form-group ">
                <label for="plans[]">Token Plan</label>
                  @foreach($packages as $package)

                  <?php  
                    $name = $package->name;
                    if($name == 'HappiLIFE Summary Reading'){
                        $name = 'HappiLEARN';
                    } 
                    if($name == 'HappiLIFE Screening'){
                      $name = 'HappiLIFE Awareness Tool';
                    }
                  ?>  
                    
                  @if($name != 'HappiApp' && $name != 'HappiBUDDY+ HappiAPP')
                    <div class="form-check plan-checkbox">
                      <label class="form-check-label custom-input-field" for="plan-{{ $package->plan[0]->id }}" id="plan-{{ $package->plan[0]->id }}-label"  data-plan-id='{{ $package->plan[0]->id }}'>
                        <input type="checkbox"name="plans[]" value="{{ $package->plan[0]->id }}" id="plan-{{ $package->plan[0]->id }}" data-plan-name="{{ $package->name }}"  data-plan-id='{{ $package->plan[0]->id }}'>
                        {{ $name }}
                      </label>
                    </div>
                  @endif

                @endforeach
              </div>
              <div class="form-group">
                <div class="plan-checkbox">
                  <label class="form-check-label" for="thrive_code">
                    <input type="checkbox" name="thrive_code" id="thrive_code">
                    HappiApp Code
                  </label>

                  <input type="file" name="thrive_file" id="thrive_file_id" accept=".csv, .xlsx">
                </div>
              </div>
              <div class="form-group">
                <div class="plan-checkbox">
                  <label class="form-check-label" for="email_address">
                    <input type="checkbox" name="email_address" id="email_address">
                    Email Address
                  </label>
                  <input type="file" name="email_file" id="email_file_id" accept=".csv, .xlsx">
                </div>
              </div>
              <div class="form-group">
                <div class="plan-checkbox">
                  <label class="form-check-label" for="pdf_file_id" id="upload_label">
                    Upload Attachment
                  </label>
                  <input type="file" name="pdf_file" id="pdf_file_id" accept=".pdf">
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <label for="tokenCategories[]">Category</label>
                  @foreach($tokenCategory as $category)
                    <div class="form-check plan-checkbox">
                      <label class="form-check-label" for="category-{{ $category->id }}">
                        <input type="checkbox" name="tokenCategories[]" value="{{ $category->id }}" id="category-{{ $category->id }}">
                          {{ $category->name }}
                      </label>
                    </div>
                  @endforeach
            </div>
            <div class="form-group col-sm-6">
                <label for="generate_unique_id"> Max Limit users registered through the coupon </label>
              <input name="use_limit" type="number" class="form-control" id="generate_unique_id" placeholder="Enter max Limit user resitered happimynd" value="{{ Session::get('use_limit') }}" required="false" min="1">
            </div>
            <div class="form-group" id='customize-email'>
                <label class="form-label">Email Body</label>
                <textarea  id="orientation-email-body-content" name="orientation-email-body">{{ $orientationMailText }}</textarea>
              </div>
            <div class="form-group col-sm-12">
               <label for="deal_id">Deal ID</label>
                <input name="deal_id" type="number" class="form-control" id="deal_id" placeholder="For pushing data to bitrix enter deal ID">
            </div>
            <div class="form-group col-auto mr-auto">
              <button type="submit" class="btn btn-primary mt-4">Generate</button>
            </div>
          </div>
        </form>
        @isset($tokens)
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30">
                    Tokens
                </p>
                <b>Total HappiTALK Hours:  | {{ $tokens[0]->tokenMetaData ? $tokens[0]->tokenMetaData->meta_data['HappiTALK'] :0 }} Hours</b>
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Token Type</th>
                      <th>Token</th>
                      <th>Category</th>
                      <th>Organization</th>
                      <th>HappiApp Limit</th>
                      <th>HappiTalk Session Limit</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($tokens as $token)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                          @foreach($token->plans as $plan)

                          <?php  
                            $name = $plan->plan->package->name;
                            if($name == 'HappiLIFE Summary Reading'){
                                $name = 'HappiLEARN';
                            } 
                            if($name == 'HappiLIFE Screening'){
                              $name = 'HappiLIFE Awareness Tool';
                            }
                          ?>  
                  
                          {{ $name }}  |
                          @endforeach
                        </td>
                        <td>{{ $token->token }}</td>
                        <td>{{implode(',',$token->category->pluck('category')->pluck('name')->toArray())}}</td>
                        <td>{{ $organization_name }}</td>
                        <td>
                          {{ $token->tokenMetaData ? $token->tokenMetaData->meta_data['HappiAPP'] :0 }}
                        </td>
                        <td>
                          @if ($token->tokenMetaData )
                            @if ($token->tokenMetaData->meta_data['HappiTALK2'] == 0)
                             -
                             @else
                             {{ $token->tokenMetaData->meta_data['HappiTALK2'] }} Session
                            @endif
                          @endif
                        </td>
                        <td>
                          {{ $token->email ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
        </div>
        @endisset
        @isset($thriveCodes)
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
               <b>HappiApp Codes | {{ count($thriveCodes) }}</b>
                @if(count($thriveCodes)>0)
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Organization</th>
                      <th>HappiApp Code</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($thriveCodes as $thriveCode)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $organization_name }}</td>
                        <td>{{ $thriveCode->code }} </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                @endif
              </div>
            </div>
        </div>
        @endisset
      </div>
    </div>
  </x-slot>

  <x-slot name="js">
    <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
  <script>
    $(document).ready(()=>{
      $('#thrive_file_id').hide();
      $('#thrive_code').change(function(){
        if(this.checked){
          $('#thrive_file_id').show();
          $('#thrive_file_id').prop('required',true);
        }else{
          $('#thrive_file_id').hide();
          $('#thrive_file_id').prop('required',false);
          $('#thrive_file_id').val('');
        }
      })

      $('#email_file_id').hide();
      $('#pdf_file_id').hide();
      $('#upload_label').hide();
      $('#customize-email').hide();
      $('#email_address').change(function(){
        if(this.checked){
          $('#email_file_id').show();
          $('#email_file_id').prop('required',true);
          $('#pdf_file_id').show();
          $('#pdf_file_id').prop('required',true);
          $('#upload_label').show();
          $('#customize-email').show();
        }else{
          $('#email_file_id').hide();
          $('#email_file_id').prop('required',false);
          $('#email_file_id').val('');
          $('#pdf_file_id').hide();
          $('#pdf_file_id').prop('required',false);
          $('#pdf_file_id').val('');
          $('#upload_label').hide();
          $('#customize-email').hide();
        }
      })

      $('.custom-input-field').on('click',function(e){
        // console.log($(this));
        planId = $(this).data('plan-id');
        inputElement = $('#plan-'+planId)
        // console.log(inputElement)
        if(inputElement.data('plan-name') == 'HappiAPP' || inputElement.data('plan-name') == 'HappiTALK'){
          if(inputElement.data('plan-name') == 'HappiAPP' && inputElement.is(':checked') && $('#HappiAPP-input').length == 0){
            // console.log('1')
            $('<input>').attr({
              type: 'number',
              id: 'HappiAPP-input',
              name: 'HappiAPP-input',
              min: '0',
              placeholder: 'Enter the limit of the HappiApp Code per happimynd token',
              class: 'form-control',
              required: 'true',
            }).insertAfter($('#plan-'+planId+'-label'));
          }
          else if($('#plan-5').is(':checked') == false){
            if($('#HappiAPP-input').length>0)
              $('#HappiAPP-input').remove()
          }
          if(inputElement.data('plan-name') == 'HappiTALK' && inputElement.is(':checked') && $('#HappiTALK-input').length == 0){
            // console.log('2')
            $('<input>').attr({
              type: 'number',
              id: 'HappiTALK-input2',
              name: 'HappiTALK-input2',
              min: '0',
              placeholder: 'Enter session limit per happimynd token',
              class: 'form-control',
              required: 'true',
            }).insertAfter($('#plan-'+planId+'-label'));
            $('<input>').attr({
              type: 'number',
              id: 'HappiTALK-input',
              name: 'HappiTALK-input',
              min: '0',
              placeholder: 'Enter number of hours for organization',
              class: 'form-control',
              required: 'true',
            }).insertAfter($('#plan-'+planId+'-label'));
          }
          else if($('#plan-6').is(':checked') == false){
            if($('#HappiTALK-input').length>0)
              $('#HappiTALK-input').remove()
            if($('#HappiTALK-input2').length>0)
              $('#HappiTALK-input2').remove()
          }
        }
      });
    })

        function initializeCKEditor(id) {
          ClassicEditor.create( document.querySelector( '#'+id ) )
            .catch( error => {
                console.error( error );
            } );
        }

        initializeCKEditor('orientation-email-body-content');
  </script>
  </x-slot>
</x-backend-layout>
