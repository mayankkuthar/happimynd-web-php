<x-backend-layout>
  <x-slot name="title">
    Dates for HappiGUIDE notes
  </x-slot>

  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="flash-message">
          
          @if(Session::has("error"))
          <div class="alert alert-danger">{{Session::get("error")}}</div>
          @endif
          @if(Session::has("success"))
          <div class="alert alert-success">{{Session::get("success")}}</div>
          @endif
          @if ($errors->any())
          <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                {{$error}}
                @endforeach
          </div>
          @endif

        </div>
        <div class="x_panel">

          <div class="x_title">
                <h2>Dates for HappiGUIDE Notes</h2>
                <div class="clearfix"></div>
          </div>


          <form method="POST" action="{{url('admin/happiguide-notes-based-on-dates')}}">
            {{csrf_field()}}
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date">
            <label for="end_date">Last Date:</label>
            <input type="date" id="end_date" name="end_date" >

            <button type="submit" id="downloadbtn" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
            <span id="buttonText">Submit</span>
          </form>

        </div>
      </div>
    </div>
  </x-slot>
  <x-slot name="js">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {
          $('#organizations').select2();
      });
      function reGenerateReport(assessment_id) {
        url = "{{ url('/') }}/admin/regenerate-report/"+assessment_id;
        $.ajax({
          url: url,
          method: "GET",
          success: function(data){

          }
        });
      }

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