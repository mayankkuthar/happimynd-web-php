<x-backend-layout>
  <x-slot name="title">
    Add Available Date
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <style>
      .plan-checkbox{
        margin-bottom: 5px;
      }
    </style>
    <div class="right_col" role="main">
      <div class="row">
        <form action="{{ route('admin.allBookedDate.post') }}" method="get">
          <div class="col">
            <div class="col-md-12">
              <label for=""> From :
                <input type="text" name="start_date" class="dateslot" placeholder="-" readonly required value="{{ old('start_date') }}">
                @error('start_date')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </label>
              <label for=""> To :
                <input type="text" name="end_date" class="dateslot" placeholder="-" readonly required value="{{ old('start_date') }}">
                @error('end_date')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <button class="btn btn-primary" type="submit">Display</button>
          </div>
        </div>
      </form>
      @isset($slotsBooked)
      <div class="row">
        <div class="col-sm-12">
          <div class="card-box table-responsive">
            <h4 class="font-13 m-b-30 text-center text-primary">
              Upcoming Appointments
            </h4>
            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>S.NO</th>
                  <th>Date</th>
                  <th>Slots</th>
                  <th>Customer Name</th>
                  <th>Email</th>
                  <th>Phone No.</th>
                  <th>Call Option</th>
                </tr>
              </thead>
              <tbody>
                @foreach($slotsBooked as $slots )
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    {{ date('d-M-Y', strtotime($slots->available_date)) }}
                  </td>
                  <td>
                    {{ $slots->slot }}
                  </td>
                  <td>
                    {{ $slots->assessment->user->username ?? ''}}
                  </td>
                  <td>
                    {{ $slots->assessment->user->email ?? '' }}
                  </td>
                  <td>
                    {{ $slots->assessment->user->mobile ?? '' }}
                  </td>
                  <td>
                    {{ $slots->callOption() }}
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{-- <a href="{{ route('admin.updateDateFormat.get') }}">Fix Date Format</a> --}}
        </div>
      </div>
      @endisset
      {{-- where second table --}}
      @isset($slotsBookedPast)
      <div class="row">
        <div class="col-sm-12">
          <div class="card-box table-responsive">
            <h4 class="font-1 text-center text-primary">
              Previously Booked Appointments
            </h4>
            <table id="datatablepast" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>S.NO</th>
                  <th>Date</th>
                  <th>Slots</th>
                  <th>Customer Name</th>
                  <th>Email</th>
                  <th>Phone No.</th>
                  <th>Call Option</th>
                </tr>
              </thead>
              <tbody>
                @foreach($slotsBookedPast as $slots )
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    {{ date('d-M-Y', strtotime($slots->available_date)) }}
                  </td>
                  <td>
                    {{ $slots->slot }}
                  </td>
                  <td>
                    {{ $slots->assessment->user->username ?? ''}}
                  </td>
                  <td>
                    {{ $slots->assessment->user->email ?? '' }}
                  </td>
                  <td>
                    {{ $slots->assessment->user->mobile ?? '' }}
                  </td>
                  <td>
                    {{ $slots->call_option == 2 ? 'Zoom Call':'Phone Call' }}
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endisset
    </div>



    <x-slot name="js">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
      <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src=""></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      {{-- https://code.jquery.com/jquery-3.5.1.js --}}


    </x-slot>
  </x-slot>
</x-backend-layout>
<script>
  $(document).ready(function(){

    $(".dateslot").datepicker({
    });
  })
</script>
