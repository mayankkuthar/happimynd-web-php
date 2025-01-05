<x-backend-layout>
  <x-slot name="title">
    Notify User
  </x-slot>
  <x-slot name="css">
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
  </x-slot>
  <x-slot name="js">
    <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
  </x-slot>
  <x-slot name="content">
  <div class="right_col" role="main">
  @if(isset($users))
    <div class="x_content">
        <div class="x_content">
          <div class="x_panel">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30">
                  Select Users
                  <a href="#" class="pull-right">Notify </a>
                </p>
                <button class="btn btn-primary" onclick="notifyUsers()">Notify selected users</button>
                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>Select <span class="badge badge-info" onclick='$(".checkboxes").prop("checked") ? $(".checkboxes").prop("checked", false) : $(".checkboxes").prop("checked", true) ;'> check all</span></th>
                      <th>S.No</th>
                      <th>User</th>
                      <th>Notify</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users->take(600) as $user)
                    <tr>
                      <td><input type="checkbox" class="checkboxes" data-user-id="{{ $user->id }}" ></td>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $user->username }}</td>
                      <td><a href="{{ route('admin.getSendNotificationView',['user_id' => $user->id]) }}">Notify</a></td>
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
  </x-slot>
  <x-slot name="js">
    <script type="text/javascript">
      function notifyUsers() {
        var selected = [];
        $('.checkboxes:checked').each(function() {
            selected.push($(this).data('userId'));
        });
        $user_id = selected.join();
        if(selected.length>0){
          window.location = "{{ route('admin.getSendNotificationView') }}"+"?user_id="+$user_id;
        }
      }
    </script>
  </x-slot>
</x-backend-layout>
