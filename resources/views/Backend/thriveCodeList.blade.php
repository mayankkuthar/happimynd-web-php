<x-backend-layout>
    <x-slot name="title">
      Happimynd Codes
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
          @if($happimynd)
            <form class="form-horizontal" method="POST" action="{{ route('admin.thriveCodeList.post') }}" enctype="multipart/form-data">
              @csrf
              <div class="form-group col-sm-8">
                <label for="generate_unique_id">Upload HappiApp Code for Happimynd</label>
                <input type="file" name="thrive_file" accept=".csv, .xlsx">
                <input type="hidden" name="happimynd" value="{{ $happimynd->id }}">
                <button type="submit" class="btn btn-success">Submit</button>
              </div>
            </form>
            <hr>
          @endif
            <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.thriveCodeList.post') }}">
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
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        @isset($thriveCodes)
          <div class="col">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                    HappiApp Codes: {{ count($thriveCodes) }}
                  </p>
                  <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Code ID</th>
                        <th>Organization</th>
                        <th>Code</th>
                        <th>User</th>
                        <th>Availed at</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($thriveCodes as $code)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $code->id }}</td>
                        <td>{{ $code->organization->name }}</td>
                        <td>{{ $code->code }}</td>
                        <td>{{ $code->user->email ?? '-' }} @if($code->user)(Username: {{ $code->user->username }})@endif</td>
                        <td>@if($code->expired_at){{ $code->expired_at->format('d-M-y g:i a') }}@else - @endif</td>
                        <td>
                            @php
                              if($code->isUsable()) {
                                  echo "Active";
                              }
                              elseif($code->isExpired()) {
                                  echo "Used";
                              }
                              elseif($code->isDisabled()) {
                                  echo "Disabled";
                              }
                            @endphp
                          </td>
                          <td>
                              @if(!$code->isDisabled() && !$code->isExpired())
                                <a href="{{ route('admin.expireToken',['id'=>$code->id, 'type'=>'thriveCode']) }}" title="invalidate/revoke tokens">De-Activate Token</a><br>
                              @endif
                              @if($code->isDisabled())
                                <a href="{{ route('admin.reactivateToken',['id'=>$code->id, 'type'=>'thriveCode']) }}" title="re-activate tokens">Re-Activate Token</a>
                              @endif
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
        @endisset
    </x-slot>
</x-backend-layout>
