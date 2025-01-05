<x-backend-layout>
    <x-slot name="title">
        Customer list
    </x-slot>
    <x-slot name="content">
        <div class="right_col" role="main">
        <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Admin List</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                  <div class="row">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive">

                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>S. No</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>E-mail</th>
                      <th>Roles</th>
                      <th>Account Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($admins as $admin)
                        <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $admin->first_name ?? '-' }}</td>
                        <td>{{ $admin->last_name ?? '-' }}</td>
                        <td>{{ $admin->email ?? '-' }}</td>
                        <td>{{implode( ',', $admin->getRoleNames()->toArray()) ?? '-' }}</td>
                        <td>{{ $admin->account_status ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.editAdminView',['id'=>$admin->id]) }}"><i class="fa fa-edit"></i> </span></a>
                            <a href="{{ route('admin.deleteAdmin',['id'=>$admin->id]) }}"><i class="fa fa-trash-o"></i></a>
                        </td>
                        </tr>
                        @endforeach
                </div>
                  </tbody>
                </table>


              </div>
            </div>
          </div>
            </div>
    </x-slot>
</x-backend-layout>
