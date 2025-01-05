<x-backend-layout>
  <x-slot name="title">
    Download Happimynd Token
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            {{-- <h3>Download Happimynd Token</h3> --}}
          </div>
        </div>
        <div class="clearfix"></div>

      </div>
      <div class="x_content">
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30">
              </p>
              @if(isset($tokens))
              <h3 class="text-muted text-center font-13 my-10">
                @if(count($tokens)>0)
                {{ $tokens[0]->organization->name ?? ''}} Happimynd Tokens : {{ count($tokens) ?? ''}}
                @endif
              </h3>
              <h2 class="text-muted text-center font-13 my-10">
                Used tokens: {{ $userCount }}
              </h2>
              <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Organization</th>
                    <th>Token</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>HappiTALK Session Limit</th>
                    <th>HappiApp Limit</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tokens as $token)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $token->organization->name ?? ''}}</td>
                    <td>{{ $token->token ?? '' }}</td>
                    <td>
                      @foreach($token->userToken as $user_token)
                      {{ $user_token->user->username ?? '' }}
                      <br/>
                      @endforeach
                    </td>
                    <td>
                      @foreach($token->userToken as $user_token)
                      {{ $$user_token->user->email ?? ''}}
                      <br/>
                      @endforeach
                    </td>
                    <td>
                      @if($token->isUsable())
                        Active
                      @elseif($token->isExpired())
                        expired
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
                      @foreach($token->userToken as $user_token)
                      {{ $user_token->user->created_at ?? '' }}
                      <br/>
                       @endforeach
                    </td>  
                    <td>
                      {{ ($token->tokenMetaData && (isset($token->tokenMetaData->meta_data['HappiTALK2'])) && $token->tokenMetaData->meta_data['HappiTALK2'] > 0) ? $token->tokenMetaData->meta_data['HappiTALK2'] . ' Hours':'-'}}
                    </td>
                    <td>
                      {{ ($token->tokenMetaData && $token->tokenMetaData->meta_data['HappiAPP'] > 0) ? $token->tokenMetaData->meta_data['HappiAPP'] . ' Session':'-'}}
                    </td>
                  </tr>
                  @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="text-center my-10">
                No HappiApp Code
                </div>
            @endif
        </div>
        </div>
      </div>
    </div>
    <!-- /page content -->
  </x-slot>
</x-backend-layout>
