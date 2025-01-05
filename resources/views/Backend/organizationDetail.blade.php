<x-backend-layout>
    <x-slot name="title">
      Organization Detail
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
        <div class="x_content">

            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has( $msg))
                <p class="alert-{{ $msg }}">{{ Session::get($msg) }}</p>
                @endif
                @endforeach
            </div>
            <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.OrganizationDetail.post') }}">
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
                <div class="form-group">
                    <div class="col-md-9 col-sm-9  offset-md-3">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
            @if(isset($detailedOrganization))
                <div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="x_content">
                                    <h3 class="text-muted text-center font-13 m-b-30">
                                        Happimynd Token : {{ count($detailedOrganization->token)  ?? 0}}
                                    </h3>
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                            <th>S.No</th>
                                            <th>Organization</th>
                                            <th>Category</th>
                                            <th>Token</th>
                                            <th>Username</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>HappiTALK Session Limit</th>
                                            <th>HappiAPP Limit</th>
                                            <th>Services used</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detailedOrganization->token as $token)
                                            <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detailedOrganization->name }}</td>
                                            <td>
                                                {{implode(',',$token->category->pluck('category')->pluck('name')->toArray())}}
                                            </td>
                                            <td>
                                            {{ $token->token ?? '' }}
                                            </td>
                                            <td>
                                               @foreach($token->userToken as $user_token)
                                                {{ $user_token->user->username ?? '' }}
                                                <br/>
                                               @endforeach
                                            </td>
                                            <td>
                                                @foreach($token->userToken as $user_token)
                                                {{ $user_token->user->created_at ?? '' }}
                                                <br/>
                                                @endforeach
                                            </td>                                   
                                            <td>
                                                @php
                                                if($token->isUsable()) {
                                                    echo "Active";
                                                }
                                                elseif($token->isExpired()) {
                                                    echo "Used";
                                                }
                                                elseif($token->isDisabled()) {
                                                    echo "Disabled";
                                                }
                                                @endphp
                                            </td>
                                            <td>
                                                {{ ($token->tokenMetaData && (isset($token->tokenMetaData->meta_data['HappiTALK2'])) && $token->tokenMetaData->meta_data['HappiTALK2'] > 0) ? $token->tokenMetaData->meta_data['HappiTALK2'] . ' Hours':'-'}}
                                            </td>
                                            <td>
                                                {{ ($token->tokenMetaData && $token->tokenMetaData->meta_data['HappiAPP'] > 0) ? $token->tokenMetaData->meta_data['HappiAPP'] . ' Session':'-'}}
                                            </td>
                                            <td>
                                                - 
                                            </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 my-7">
                            <div class="card-box table-responsive">
                                <div class="x_content">
                                    <h3 class="text-muted text-center font-13 m-b-30">
                                      HappiApp Code : {{ count($detailedOrganization->thriveCode)  ?? 0}}
                                    </h3>
                                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                            <th>S.No</th>
                                            <th>Organization</th>
                                            <th>Code</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detailedOrganization->thriveCode as $thriveCode)
                                            <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detailedOrganization->name }}</td>
                                            <td>
                                            {{ $thriveCode->code ?? '' }}
                                            </td>
                                            <td>
                                                {{ $thriveCode->user->username ?? '' }}
                                            </td>
                                            <td>
                                                @php
                                                if($thriveCode->isUsable()) {
                                                    echo "Active";
                                                }
                                                elseif($thriveCode->isExpired()) {
                                                    echo "Used";
                                                }
                                                elseif($thriveCode->isDisabled()) {
                                                    echo "Disabled";
                                                }
                                                @endphp
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
            @endif
        </div>
        </div>
    </x-slot>
</x-backend-layout>
