<x-backend-layout>
  <x-slot name="title">
    Happimynd Codes
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <form class="form-horizontal form-label-left" id='validate-form' method="POST" action="{{ route('admin.tokenList.post') }}">
          @csrf
          <div class="form-group row">
            <label class="control-label col-md-3 col-sm-3 ">Select organization</label>
            <div class="col-md-9 col-sm-9 ">
              <div class="dropdown">
                <input onclick="myFunction()" id="organizationdropdown1" type="text" placeholder="Select organization" readonly />
                <div id="myDropdown" class="dropdown-content">
                  <input type="text" placeholder="Search.." id="myInput" onkeyup="filterFunction()">
                  <div class="dropdown-content__list">
                    @foreach($organizations as $organization)
                    <a href="javascript:void(0);" onclick="selectOrg1('{{ $organization->name }}', '{{ $organization->id }}');" >{{ $organization->name }}</a>
                    @endforeach
                  </div>
                </div>
              </div>
              <select class="form-control" id="tokenlist__search" name="organization_id" hidden>
                @foreach($organizations as $organization)
                <option value="{{ $organization->id }}" @if(old('organization_id') == $organization->id) @php $organization_name = $organization->name @endphp selected="selected" @endif >{{ $organization->name }}</option>
                @endforeach
              </select>
            </div>
          </div>


          <!-- <div class="form-group row">
            <label class="control-label col-md-3 col-sm-3 ">Select Date</label>
            <div style="    margin-right: 20px;">
              <label for="start_date">Start Date:</label><br>
              <input type="date" id="start_date" name="start_date">
            </div>
            <div>
              <label for="end_date">End Date:</label><br>
              <input type="date" id="end_date" name="end_date" >
            </div>
          </div> -->


          <div class="form-group row">
            <label class="control-label col-md-3 col-sm-3 ">Filter</label>
            <div class="col-md-9 col-sm-9">
              <div class="radio">
                <label class="">
                  <div class="iradio_flat-green" style="position: relative;"><input type="radio" class="flat" @if(old('token_status') == 'expired') checked="" @endif value="expired" name="token_status" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> Expired
                </label>
              </div>
              <div class="radio">
                <label class="">
                  <div class="iradio_flat-green" style="position: relative;"><input type="radio" class="flat" @if(old('token_status') == 'active') checked="" @endif value="active" name="token_status" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> Active
                </label>
              </div>
              <div class="radio">
                <label class="">
                  <div class="iradio_flat-green" style="position: relative;"><input type="radio" class="flat" @if(old('token_status') == 'disabled') checked="" @endif value="disabled" name="token_status" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> Disabled
                </label>
              </div>
              <div class="radio">
                <label class="">
                  <div class="iradio_flat-green" style="position: relative;"><input type="radio" class="flat" @if(old('token_status') == 'all') checked="" @endif value="all" name="token_status" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> All
                </label>
              </div>

            </div>
          </div>
          <div class="form-group">
            <div class="col-md-9 col-sm-9  offset-md-3">
              <!-- <button type="submit" class="btn btn-success" onclick="return validateDate(start_date,end_date);">Submit</button> -->
              <input type="submit" class="btn btn-success" value="Submit" onclick="return validateDate(start_date,end_date);">

            </div>
          </div>
        </form>
      </div>
      @isset($tokens)
      <div class="col">
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30">
                Tokens
              </p>
              <div class="x_content">
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Token Type</th>
                      <th>Token</th>
                      <th>token generated for Email</th>
                      <th>Category</th>
                      <th>Organization</th>
                      <th>Status</th>
                      <th>HappiTALK Session Limit</th>
                      <th>HappiApp Limit</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($tokens as $token)
                    {{-- {{ dd($token->tokenMetaData) }} --}}
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        @foreach($token->plans as $plan)
                        {{ $plan->plan->package->name }}( @if($plan->plan->name == 'HappiTALK' && $plan->plan->duration->frequency != '1'){{$plan->plan->duration->name }}@else{{ ' '.$plan->plan->duration->name }} @endif)|
                        @endforeach
                      </td>
                      <td>{{ $token->token }}</td>
                      <td>{{ $token->email }}</td>
                      <td>{{implode(',',$token->category->pluck('category')->pluck('name')->toArray())}}</td>
                      <td>{{ $token->organization->name }}</td>
                      <td>
                        @if($token->isUsable())
                        Active
                        @elseif($token->isExpired())
                        Expired
                        @elseif($token->isDisabled())
                        Disabled
                        @endif
                        <br>
                        @if($token->userToken->count())
                        Used by
                        <br>
                        @foreach($token->userToken as $user_token) 
                        <b>username:</b>
                        {{ $user_token->user->username }}
                        <br>
                        @endforeach
                        @endif
                      </td>
                      <td>
                        {{ ($token->tokenMetaData && (isset($token->tokenMetaData->meta_data['HappiTALK2'])) && $token->tokenMetaData->meta_data['HappiTALK2'] > 0) ? $token->tokenMetaData->meta_data['HappiTALK2'] . ' Hours':'-'}}
                      </td>
                      <td>
                        {{ ($token->tokenMetaData && $token->tokenMetaData->meta_data['HappiAPP'] > 0) ? $token->tokenMetaData->meta_data['HappiAPP'] . ' Session':'-'}}
                      </td>
                      <td>
                        @if(!$token->isDisabled() && !$token->isExpired())<a href="{{ route('admin.expireToken',['id'=>$token->id, 'type'=>'token']) }}" title="invalidate/revoke tokens">De-Activate Token</a><br>@endif
                        @if($token->isDisabled())<a href="{{ route('admin.reactivateToken',['id'=>$token->id, 'type'=>'token']) }}" title="re-activate tokens">Re-Activate Token</a>@endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                
                <x-pagination-dropdown :paginator="$tokens" />
                
              </div>
            </div>
          </div>
        </div>
      </div>
      @endisset


    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      function validateDate(start,end){
        const _MS_PER_DAY = 1000 * 60 * 60 * 24;
        const start_date = new Date('"'+start.value+'"'),
        end_date = new Date('"'+end.value+'"');
        const utc1 = Date.UTC(start_date.getFullYear(), start_date.getMonth(), start_date.getDate());
        const utc2 = Date.UTC(end_date.getFullYear(), end_date.getMonth(), end_date.getDate());
        if(Math.floor((utc2 - utc1) / _MS_PER_DAY)>31){
            alert("End Date must be atmost 30 days from start date");
            return(false);
          }     
      }
    </script>



<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      $("#validate-form").submit(function(e){

        var org = $('#organizationdropdown1').val();
        if(org == ''){
            alert("Please select organization.");
            e.preventDefault();
            return false;
        }

        var start_date =  $('#start_date').val();
        var end_date =  $('#end_date').val();

        if(start_date == '' || end_date == ''){
            alert("Please select start and end date.");
             e.preventDefault();
        }

      });
    });
  </script> -->
    </x-slot>
  </x-backend-layout>
