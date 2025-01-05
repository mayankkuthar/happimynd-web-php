<x-backend-layout>
  <x-slot name="title">
    Campaign
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2>
                All Campaigns
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#campaignModal" data-whatever="@mdo" onclick="$('#campaign-form').attr('action', '{{ route('admin.addCampaign.post') }}')">+</button>
              </h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
                @endif
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>S. No</th>
                          <th>Campaign Name</th>
                          <th>plans</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($campaigns as $campaign)
                        <tr id="{{ $campaign->name.'-'.$campaign->id }}">
                          <td>{{ $loop->iteration }}</td>
                          <td id="campaign-name">{{ $campaign->name ?? '-' }}</td>
                          <td id="{{ $campaign->name.'-'.$campaign->id }}-plans" data-campaign-plans='{{ implode(',', array_keys($campaign->plan_id)) }}'>
                            @foreach($campaign->plan_id as $planId => $planPrice) 


                              <?php  
                                $name = $plans[$planId]->name;
                                if($name == 'HappiLIFE Summary Reading'){
                                  $name = 'HappiLEARN';
                                }
                                if($name == 'HappiLIFE Screening'){
                                  $name = 'HappiLIFE Awareness Tool';
                                }
                              ?>

                              {{ $name .'|' }} @endforeach


                          </br>
                          URL: <a href="{{ route('campaign.plansPage.get') }}/?utm_campaign={{ $campaign->name }}">{{ route('campaign.plansPage.get') }}/?utm_campaign={{ $campaign->name }}</a>
                        </td>
                        <td>
                          <div class="custom-control custom-switch">
                            <input
                            type="checkbox"
                            class="custom-control-input status-button"
                            id="{{ $campaign->name.'_'.$campaign->id }}"
                            @if($campaign->status) checked='true' @endif
                            >
                            <label class="custom-control-label" for="{{ $campaign->name.'_'.$campaign->id }}"></label>
                          </div>
                        </td>
                        <td>
                          <span
                          onclick="openEditCampaignModal('{{ $campaign->name.'-'.$campaign->id }}', '{{ implode(',', array_keys($campaign->plan_id)) }}' )"
                          >
                          <i class="fa fa-edit"></i>
                        </span>
                        <span onclick="deleteCampaign({{ $campaign->id }});">
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
      <!-- modal for adding/editing campaign -->
      <div class="modal fade" id="campaignModal" tabindex="-1" role="dialog" aria-labelledby="campaignModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="campaignModalLabel">Campaign</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="campaign-form" action="{{ route('admin.addCampaign.post') }}" method="POST">
                @csrf
                <input type="hidden" name="campaign-id" id="campaign-id">
                <div class="form-group">
                  <label for="campaign-name" class="col-form-label">Campaign Name:</label>
                  <input type="text" class="form-control" id="campaign-name" name="campaign-name">
                  <div class="invalid-feedback">
                    name already used
                  </div>
                </div>
                <div class="form-group">
                  <label for="campaign-price" class="col-form-label">Amount:</label>
                  <input type="number" min="1" class="form-control" id="campaign-price" name="campaign-total-price">
                </div>
                <div class="form-group">
                  <label for="campaign-plans" class="col-form-label">Plans:</label>
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


                  @if($name != 'HappiAPP' && $name != 'HappiBUDDY+ HappiAPP')
                    <div class="form-check plan-checkbox">
                      <label class="form-check-label custom-input-field" for="plan-{{ $package->plan[0]->id }}" id="plan-{{ $package->plan[0]->id }}-label"  data-plan-id='{{ $package->plan[0]->id }}'>
                        <input type="checkbox"name="plans[]" value="{{ $package->plan[0]->id }}" id="plan-{{ $package->plan[0]->id }}" data-plan-name="{{ $package->name }}"  data-plan-id='{{ $package->plan[0]->id }}'>
                        {{ $name }}
                      </label>
                    </div>
                  @endif

                  @endforeach
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" value="submit" id="submit-button" onclick="$('#campaign-form').submit();">Add</button>
            </div>
          </div>
        </div>
      </div>
      <!-- modal for adding/editing campaign -->
    </div>
  </div>
</div>
</x-slot>
<x-slot name="js">
  <script type="text/javascript">
  $('.custom-input-field').on('click',function(e){
        // console.log($(this));
        planId = $(this).data('plan-id');
        inputElement = $('#plan-'+planId)
        // console.log(inputElement)
        if(inputElement.data('plan-name') == 'HappiAPP' || inputElement.data('plan-name') == 'HappiTALK'){
          if(inputElement.data('plan-name') == 'HappiAPP' && inputElement.is(':checked') && $('#HappiAPP-input').length == 0){
            // console.log('1')
            $('<input>').attr({
              type: 'file',
              id: 'HappiAPP-input',
              name: 'HappiAPP-input',
              min: '0',
              placeholder: 'upload HappiApp Codes',
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
    $('.status-button').on('change', function(e){
      var campaign_id = e.target.id.split('_')[1]
      console.log(e.target);
      console.log($(e.target).is(":checked"));
      $.ajax({
        type: 'GET',
        url: "{{ route('admin.changeStatusCampaign.get') }}",
        data: {'id':campaign_id, 'status' : ($(e.target).is(":checked") == true)? 1: 0 },
        success: function(data)
        {
          // location.reload();
        }
      });
    });
    // this is the id of the form
    $("#campaign-form").submit(function(e) {
      console.log('s');

      e.preventDefault(); // avoid to execute the actual submit of the form.

      form = $(this);
      var url = form.attr('action');

      //return if name is empty
      if($(this).find('#campaign-name').val() == ''){
        return;
      }
      //TODO: check below code
      if($('#submit-button').text() == 'Add'){

        $.ajax({
          type: 'GET',
          url: "{{ route('admin.checkName.get') }}",
          data: {'name': ''+$(this).find('#campaign-name').val()},
          success: function(data)
          {
            if(data == 0){
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

            }
            else{
              $(form).find('#campaign-name').addClass('is-invalid')
              console.log($(this));

            }
          }
        });
      }
      else{
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
      }
    });
    function openEditCampaignModal(campaign_id, plan_id) {
      $('#submit-button').text('Update');
      modal = $('#campaignModal');
      form = $('#campaign-form');
      td = $('#'+campaign_id);
      plans = $('#'+campaign_id + ' #'+campaign_id+'-plans').data('campaignPlans');
      if(typeof plans == 'string'){
        plans = plans.split(',');
      }
      console.log(plans)
      $(modal).modal('show');
      $(form).attr('action', "{{ route('admin.editCampaign.post') }}")
      if(typeof plans != 'number'){
        plans.forEach(function(value, index, array) {
          $('#campaign-form #plan-'+value).prop('checked', true);
          console.log('true for '+value)
        });
      }
      else{
        $('#campaign-form #plan-'+plans).prop('checked', true);
      }
      $('#campaign-id').val(campaign_id.split('-')[1])
      $('#campaign-form #campaign-name').val(td.find('#campaign-name').text())
    }


    $('#campaignModal').on('hidden.bs.modal', function(e){
      $('#campaign-form').trigger('reset');
      $('#submit-button').text('Add');
      $('#campaign-form').find('#campaign-name').removeClass('is-invalid')
    });


    function deleteCampaign(campaign_id){
      if(confirm("click ok to delete")){
        $.ajax({
          type: 'GET',
          url: "{{ route('admin.deleteCampaign.get') }}",
          data: {'id' : campaign_id},
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
