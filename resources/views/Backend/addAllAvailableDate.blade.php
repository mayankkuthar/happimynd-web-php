<x-backend-layout>
  <x-slot name="title">
    Add Available Date
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
    <!-- page content -->
    <style>
      .plan-checkbox{
        margin-bottom: 5px;
      }
    </style>
    <div class="right_col" role="main">
      @foreach ($errors->all() as $error)
          <li class="text-danger ">{{ $error }}</li>
      @endforeach
      @if (session('status'))
      <div class="mb-4 font-medium text-sm text-green-600 alert alert-success">
          {{ __(session('status')) }}
      </div>
    @endif
      <form method="post" action="{{ route('admin.addAllAvailableDates.post') }}">
        @csrf
        <input type="hidden" value="{{ $psychologist_ids ?? ''}}" name="psychologist_ids">
        <h2>Date</h2>
        <input type="text" name="date" id="dateslot" placeholder="-" readonly required>
        <div class="row mt-4">
          <div class="col-md-6">

            <label for="">
              <input type="checkbox" name="slot-1month" id="slot-1month"> Add Month(s)
            </label>
            <label for="">
              <input type="number" name="months_no" id="months_no" max=12 min=1 style="width: 100px;height:25px;">
            </label>
          </div>
        </div>
        <div class="row p-2 mt-3">
          <div class="col-md-8">
            <legend>Set Default</legend>

            <div class="col-md-3">
              <input type="checkbox" name="slot-default" id="slot-default" value="Check all"> Check all
            </div>
          </div>

        </div>
        <div class="form-group">
          <div class="row mt-3">
            <div class="col-md-6">
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-1" >
                  <input class ="checkbox-input" type="checkbox"name="slots[]" id="slot-1" value="10AM-12PM"> 10AM-12PM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-2" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-2" value="12PM-2PM"> 12PM - 2PM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-3" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-3" value="2PM-4PM"> 2PM-4PM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-4" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-4" value="4PM-6PM"> 4PM-6PM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-5" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-5" value="6PM-8PM"> 6PM-8PM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-6" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-6" value="8PM-10PM"> 8PM-10PM
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-7" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-7" value="10PM-12AM"> 10PM-12AM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-8" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-8" value="12AM-2AM"> 12AM-2AM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-9" >
                  <input  class ="checkbox-input" type="checkbox" name="slots[]" id="slot-9" value="2AM-4AM"> 2AM-4AM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-10" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-10" value="4AM-6AM"> 4AM-6AM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-11" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-11" value="6AM-8AM"> 6AM-8AM
                </label>
              </div>
              <div class="form-check plan-checkbox">
                <label class="form-check-label custom-input-field" for="slot-12" >
                  <input class ="checkbox-input" type="checkbox" name="slots[]" id="slot-12" value="8AM-10AM"> 8AM-10AM
                </label>
            </div>
          </div>

          </div>

        </div>
            <input type="submit" class="btn btn-primary">
    </form>

    @isset($slotsBooked)
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30">
                    Available Dates
                </p>
                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th>S.NO</th>
                      <th>Date</th>
                      <th>Slots</th>
                      <th>Delete</th>
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
                      <td>
                        <a href="{{ route('admin.deleteAvailableDates',['date'=>$slots['date']]) }}"><i class="fa fa-trash-o"></i></a>
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
      <script src=""></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      {{-- https://code.jquery.com/jquery-3.5.1.js --}}



      <script>
   $(document).ready(()=>{
          $('#months_no').hide();
          $('#input-default').prop("disabled", true)
          $('#slot-1month').change(function(){
            if(this.checked){
              $('#months_no').show();
              $('#months_no').prop('required',true);
              $('#months_no').val(1);
            }else{
              $('#months_no').hide();
              $('#months_no').prop('required',false);
              $('#months_no').val('');
            }
          })

        })
   ///
   $('#slot-default').click(function(){
      if(this.checked){
        $('.checkbox-input').prop("checked",true)
        $('#input-default').prop("disabled", false)
        $('#input-slot-1').addClass('d-none');
        $('#input-slot-2').addClass('d-none');
        $('#input-slot-3').addClass('d-none');
        $('#input-slot-4').addClass('d-none');
        $('#input-slot-5').addClass('d-none');
        $('#input-slot-6').addClass('d-none');
        $('#input-slot-7').addClass('d-none');
        $('#input-slot-8').addClass('d-none');
        $('#input-slot-9').addClass('d-none');
        $('#input-slot-10').addClass('d-none');
        $('#input-slot-11').addClass('d-none');
        $('#input-slot-12').addClass('d-none');
      }else{
        $('.checkbox-input').prop("checked",false)
        $('#input-default').prop("disabled", true)
        $('#input-slot-1').removeClass('d-none');
        $('#input-slot-2').removeClass('d-none');
        $('#input-slot-3').removeClass('d-none');
        $('#input-slot-4').removeClass('d-none');
        $('#input-slot-5').removeClass('d-none');
        $('#input-slot-6').removeClass('d-none');
        $('#input-slot-7').removeClass('d-none');
        $('#input-slot-8').removeClass('d-none');
        $('#input-slot-9').removeClass('d-none');
        $('#input-slot-10').removeClass('d-none');
        $('#input-slot-11').removeClass('d-none');
        $('#input-slot-12').removeClass('d-none');
      }
   })
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
