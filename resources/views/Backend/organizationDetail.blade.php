<x-backend-layout>
    <x-slot name="title">
      <i class="fa fa-building"></i> Organization Detail
    </x-slot>
    <x-slot name="css">
        <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
        <link href="{{ asset('assets/Backend/css/plugins/nprogress.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/buttons.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/Backend/css/plugins/responsive.bootstrap.min.css') }}" rel="stylesheet">
        <style>
            .dropdown {
                position: relative;
                display: inline-block;
                width: 100%;
            }
            .dropdown-content {
                display: none;
                position: absolute;
                background-color: #f9f9f9;
                min-width: 100%;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                z-index: 1;
                max-height: 300px;
                overflow-y: auto;
                border-radius: 4px;
            }
            .dropdown-content.show {
                display: block;
            }
            .dropdown-content input[type=text] {
                padding: 12px 16px;
                text-decoration: none;
                display: block;
                border: none;
                border-bottom: 1px solid #ddd;
                width: 100%;
                box-sizing: border-box;
            }
            .dropdown-content__list {
                max-height: 200px;
                overflow-y: auto;
            }
            .dropdown-content a {
                color: black;
                padding: 12px 16px;
                text-decoration: none;
                display: block;
                border-bottom: 1px solid #eee;
            }
            .dropdown-content a:hover {
                background-color: #f1f1f1;
            }
            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
                margin: 0;
                padding: 0;
            }
            .pagination li {
                list-style: none;
                margin: 0 2px;
            }
            .pagination a, .pagination span {
                display: inline-block;
                padding: 10px 15px;
                text-decoration: none;
                border: 1px solid #ddd;
                border-radius: 4px;
                color: #333;
                font-size: 14px;
                font-weight: 500;
                min-width: 40px;
                text-align: center;
                transition: all 0.3s ease;
            }
            .pagination a:hover {
                background-color: #f8f9fa;
                border-color: #007bff;
                color: #007bff;
            }
            .pagination .active span {
                background-color: #007bff;
                border-color: #007bff;
                color: white;
                font-weight: 600;
            }
        </style>
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
            <div class="card-box">
                <div class="x_content">
                    <h4 class="mb-4"><i class="fa fa-search"></i> Select Organization</h4>
                    <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.OrganizationDetail.post') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="control-label col-md-3 col-sm-3 form-label">
                                <i class="fa fa-building"></i> Organization
                            </label>
                            <div class="col-md-9 col-sm-9">
                                <div class="dropdown">
                                    <input onclick="myFunction()" id="organizationdropdown1" type="text" 
                                           placeholder="Search and select an organization..." 
                                           value="{{ $detailedOrganization ? $detailedOrganization->name : '' }}"
                                           readonly 
                                           class="form-control" />
                                    <div id="myDropdown" class="dropdown-content">
                                        <input type="text" placeholder="Type to search organizations..." id="myInput" onkeyup="filterFunction()">
                                        <div class="dropdown-content__list">
                                            @foreach($organizations as $organization)
                                                <a href="javascript:void(0);" onclick="selectOrg1('{{ $organization->name }}', '{{ $organization->id }}');">
                                                    <i class="fa fa-building"></i> {{ $organization->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <select class="form-control" id="tokenlist__search" name="organization_id" hidden>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" 
                                                @if($detailedOrganization && $detailedOrganization->id == $organization->id) selected="selected" @endif>
                                            {{ $organization->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Load Organization Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            @if(isset($detailedOrganization))
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="x_content">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="mb-0">
                                        <i class="fa fa-key"></i> Happimynd Tokens
                                    </h3>
                                    <span class="badge badge-info">
                                        <i class="fa fa-database"></i> {{ $tokens ? $tokens->total() : 0 }} Total Records
                                    </span>
                                </div>
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
                                            @if($tokens && $tokens->count() > 0)
                                                @foreach($tokens as $token)
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
                                                    echo '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Active</span>';
                                                }
                                                elseif($token->isExpired()) {
                                                    echo '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Used</span>';
                                                }
                                                elseif($token->isDisabled()) {
                                                    echo '<span class="badge badge-danger"><i class="fa fa-ban"></i> Disabled</span>';
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
                                            @else
                                            <tr>
                                                <td colspan="10" class="text-center">No tokens found for this organization.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    
                                    <!-- Pagination for tokens -->
                                    @if($tokens && $tokens->hasPages())
                                    <div class="text-center mt-3">
                                        {{ $tokens->links() }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-4">
                            <div class="card-box table-responsive">
                                <div class="x_content">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h3 class="mb-0">
                                            <i class="fa fa-mobile"></i> HappiApp Codes
                                        </h3>
                                        <span class="badge badge-info">
                                            <i class="fa fa-database"></i> {{ $thriveCodes ? $thriveCodes->total() : 0 }} Total Records
                                        </span>
                                    </div>
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
                                            @if($thriveCodes && $thriveCodes->count() > 0)
                                                @foreach($thriveCodes as $thriveCode)
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
                                                    echo '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Active</span>';
                                                }
                                                elseif($thriveCode->isExpired()) {
                                                    echo '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Used</span>';
                                                }
                                                elseif($thriveCode->isDisabled()) {
                                                    echo '<span class="badge badge-danger"><i class="fa fa-ban"></i> Disabled</span>';
                                                }
                                                @endphp
                                            </td>
                                            </tr>
                                                @endforeach
                                            @else
                                            <tr>
                                                <td colspan="5" class="text-center">No thrive codes found for this organization.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    
                                    <!-- Pagination for thrive codes -->
                                    @if($thriveCodes && $thriveCodes->hasPages())
                                    <div class="text-center mt-3">
                                        {{ $thriveCodes->links() }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        </div>
    </x-slot>
    
    <script>
        // Function to select organization from dropdown
        function selectOrg1(name, id) {
            document.getElementById('organizationdropdown1').value = name;
            document.getElementById('tokenlist__search').value = id;
            document.getElementById('myDropdown').classList.remove('show');
            
            // Submit the form to load the selected organization
            document.querySelector('form[action="{{ route("admin.OrganizationDetail.post") }}"]').submit();
        }
        
        // Function to show/hide dropdown
        function myFunction() {
            document.getElementById('myDropdown').classList.toggle('show');
        }
        
        // Function to filter dropdown
        function filterFunction() {
            var input, filter, ul, li, a, i;
            input = document.getElementById('myInput');
            filter = input.value.toUpperCase();
            div = document.getElementById('myDropdown');
            a = div.getElementsByTagName('a');
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = '';
                } else {
                    a[i].style.display = 'none';
                }
            }
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('#organizationdropdown1')) {
                var dropdowns = document.getElementsByClassName('dropdown-content');
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</x-backend-layout>
