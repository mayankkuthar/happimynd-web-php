<x-backend-layout>
  <x-slot name="title">
    Happimynd Codes
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
 
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

                {{ $tokens->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
      @endisset
    </x-slot>
  </x-backend-layout>
