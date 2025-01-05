<x-backend-layout>
    <x-slot name="title">
        Customer list
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
        <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Users</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                  <div class="row">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive custom-change-table">


                <!-- <div class="excel-button">
                    <form action="{{route('admin.downloadUserListXL')}}">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" >


                    <button type="submit" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
                    <span id="buttonText">Get Excel</span>

                  </button>
                  </form>
                </div> -->

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Username</th>
                      <th>Nickname</th>
                      <th>E-mail</th>
                      <th>Mobile</th>

                      <th>Organization</th>
                      <th>Token</th>
                      <th>Coupon Used</th>

                      <th>Profile</th>

                      <th>Type</th>
                      <th>Score</th>
                      <th>Smoothness</th>
                      <th>Liveliness</th>
                      <th>Control</th>
                      <th>Energy Range</th>
                      <th>Clarity</th>
                      <th>Crispness</th>
                      <th>Speech Rate</th>
                      <th>Pause Duration</th>

                      {{-- <th>Profession</th>
                      <th>Account Status</th>
                      <th>Age</th>
                      <th>Gender</th>
                      <th>Date</th>
                      <th>Rating</th>
                      <th>Review</th>
                      <th style="text-align:center;">Action</th> --}}

                    </tr>
                  </thead>
                  <tbody>
                      @foreach($users as $user)

                      <?php

                        if($user->usersRating != null){
                          $emoji_path = $user->usersRating->applicationRatingEmoji->image;
                        }else{
                          $emoji_path = '';
                        }

                      ?>
                        <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->username ?? '-' }}</td>
                        <td>{{ $user->nickname ?? '-' }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>{{ $user->mobile ?? '-' }}</td>

                        {{-- Organization --}}
                        <td>{{ ($user->isOrganizationUser())? $user->userToken->token->organization()->withTrashed()->first()->name : '-' }}</td>

                        {{-- Token/code --}}
                        <td>{{ $user->isOrganizationUser() ? $user->userToken->token->token: '-'}}</td>

                        {{-- Used coupon code --}}
                        <td>{{ $user->getUsedCouponCodes()??'-'}}</td>

                        {{-- Profile --}}
                        <td>{{ $user->profileType->name ?? '-' }}</td>

                        {{-- Score fields --}}
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>

                        </tr>
                        @endforeach
                </div>
                  </tbody>
                </table>

                <div class="custompaginationbar">
                  {{$users->links()}}
                </div>

              </div>
            </div>
          </div>
            </div>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

    </x-slot>
</x-backend-layout>
