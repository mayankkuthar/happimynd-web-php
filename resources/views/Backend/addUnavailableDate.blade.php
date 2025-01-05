<x-backend-layout>
  <x-slot name="title">
    Add Unavailable Date
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <form method="post" action="{{ route('admin.addUnavailableDates.post') }}">
        @csrf
        <input type="text" name="date" id="dateslot" placeholder="-" readonly required>
        <h2>Date</h2>
        <div class="form-group">
          <label for="plans[]">Date</label>
                <div class="form-check plan-checkbox">
                  <label class="form-check-label custom-input-field" for="slot-1" >
                    <input type="checkbox"name="slots[]" id="slot-1" value="10AM-12PM"> 10AM-12PM
                  </label>
                </div>
                <div class="form-check plan-checkbox">
                  <label class="form-check-label custom-input-field" for="slot-2" >
                    <input type="checkbox" name="slots[]" id="slot-2" value="12PM-2PM"> 12PM - 2PM
                  </label>
                </div>
                <div class="form-check plan-checkbox">
                  <label class="form-check-label custom-input-field" for="slot-3" >
                    <input type="checkbox" name="slots[]" id="slot-3" value="2PM-4PM"> 2PM-4PM
                  </label>
                </div>
                <div class="form-check plan-checkbox">
                  <label class="form-check-label custom-input-field" for="slot-4" >
                    <input type="checkbox" name="slots[]" id="slot-4" value="4PM-6PM"> 4PM-6PM
                  </label>
                </div>
                <div class="form-check plan-checkbox">
                  <label class="form-check-label custom-input-field" for="slot-5" >
                    <input type="checkbox" name="slots[]" id="slot-5" value="6PM-8PM"> 6PM-8PM
                  </label>
                </div>
        </div>
            <input type="submit" class="btn btn-primary">
    </form>

    @isset($slotsBooked)
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30">
                    Unavailable Dates
                </p>
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>S.NO</th>
                      <th>Date</th>
                      <th>Slots</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($slotsBooked as $slots )
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        {{ $slots['date'] }}
                      </td>
                      <td>
                        {{ $slots['slots'] }}
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
        </div>
      </div>
        @endisset
    <x-slot name="js">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
      <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script>
        function selectTimeSlot(e) {
  $(".time1").removeClass('active');
  $(".time2").removeClass('active');
  $(".time3").removeClass('active');
  $(".time4").removeClass('active');
  $(".time5").removeClass('active');
  $("." + e).addClass('active');
  $("#timeslot").val($("." + e).text());
}
        var slotsBooked = {!! $slotBooked !!};

          function unavailable(date) {
              month = date.getMonth();
              day = date.getDate();
              if (month < 10) month = '0' + month;
              if (day < 10) day = '0' + day;
              dmy = month + "/" + day + "/" + date.getFullYear();
              if (slotsBooked[dmy] == undefined) {
                  return [true];
              } else {
                console.log(dmy+' => '+slotsBooked[dmy].length)
                if(slotsBooked[dmy].length == 5)
                  return [false];
                return [true];
              }
          }

        $("#dateslot").datepicker({
          minDate: 0,
          maxDate: "+1M +10D",
          beforeShowDay: unavailable,
          onSelect: function(dateText) {
            $("#timeslot").val("");
            $('.time').removeClass('disable');
              console.log("Selected date: " + dateText + "; input's current value: " + this.value);
              if(slotsBooked[dateText] != undefined){
              for(var i=0;i<slotsBooked[dateText].length;i++){
                  $('#'+slotsBooked[dateText][0].split(' ').join('')).addClass('disable');
                }
              }
          }
          });
      </script>
    </x-slot>
  </x-slot>
</x-backend-layout>
