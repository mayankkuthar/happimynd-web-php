<style type="text/css">
    div#datatable-buttons_info {
        display: none;
    }
    ul.pagination {
        display: none;
    }
    table#datatable-buttons {
        font-size: 13px;
    }
</style>

<x-backend-layout>
    <x-slot name="title">
      Add Organization
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
            <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.addOrganization.post') }}">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Add organization</label>

                    <div class="field item col-sm-3 @error('name') bad @enderror">
                        <div class="input-group">
                            <input type="text" class="form-control col-md-6 col-sm-6" name="name" placeholder="Enter organization name" value="{{ Session::get('name') ?? old('name') }}" required>
                            @error('name')
                              <div class="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-2">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Add</button>
                    </span>
                    </div>
                </div>
            </form>
        </div>
            @if(isset($organizations))
                  <div>
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                    <p class="text-muted font-13 m-b-30">
                        Organizations
                    </p>
                    <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Organization</th>
                          <th>Tokens issued</th>
                          <th>Token Action</th>
                          <th>HappiApp Code</th>
                          <th>HappiApp Code Action</th>
                          <th>Delete</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach($organizations as $organization)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $organization->name }}</td>
                          <td>
                          {{ $organization->token_count }} (Active: {{ $organization->active_token_count }} Disabled: {{ $organization->disabled_token_count }} Used: {{ $organization->used_token_count }}) <br>
                          </td>
                          <td>
                            <a href="{{ route('admin.expireTokens',['organization_id'=>$organization->id, 'type'=>'token']) }}" title="invalidate/revoke tokens">Revoke active Codes</a><br>
                            <a href="{{ route('admin.activateTokens',['organization_id'=>$organization->id, 'type'=>'token']) }}" title="active tokens">Re-Activate Codes</a>
                          </td>
                          <td>
                          {{ $organization->thriveCode_count }} (Active: {{ $organization->active_thriveCode_count }} Disabled: {{ $organization->disabled_thriveCode_count }} Used: {{ $organization->used_thriveCode_count }}) <br>
                          </td>
                          <td>
                            <a href="{{ route('admin.expireTokens',['organization_id'=>$organization->id, 'type'=>'thriveCode']) }}" title="invalidate/revoke tokens">Revoke active Codes</a><br>
                            <a href="{{ route('admin.activateTokens',['organization_id'=>$organization->id, 'type'=>'thriveCode']) }}" title="active tokens">Re-Activate Codes</a>
                          </td>
                          <td>
                            <a href="{{ route('admin.deleteOrganization.post',['organization_id'=>$organization->id]) }}" title="Delete Organization">Delete</a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    {{$organizations->links()}}

                  </div>
                  </div>
                </div>
              </div>
            @endif
        </div>
        </div>
    </x-slot>
</x-backend-layout>
