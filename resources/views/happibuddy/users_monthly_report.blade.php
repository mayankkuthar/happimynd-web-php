
<x-backend-layout>
  <x-slot name="title">
    Users Monthly Report
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Users Monthly Report</h3>
          </div>
        </div>
        <div class="clearfix"></div>

      </div>
      <div class="x_content">
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive custom-change-table">
              <p class="text-muted font-13 m-b-30">
              </p>


                 

              <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                <thead>
                        <tr>
                          <th>S.NO</th> 
                          <th>Session status</th> 
                          <th>Presenting complaints</th> 
                          <th>Session summary</th> 
                          <th>Homework asigned</th> 
                          <th>Plan for next session / Remarks</th> 
                        </tr>
                </thead>
                <tbody>
                        <?php  
                          $i=0;
                        ?>
                        @foreach($list as $row)

                        <tr>
                          <td>{{ ++$i }}</td> 
                          <td>{{ $row->session_status }}</td> 
                          <td>{{ $row->presenting_complaints }}</td>  
                          <td>{{ $row->session_summary }}</td>  
                          <td>{{ $row->hardword_asigned }}</td>  
                          <td>{{ $row->plan_for_next_session }}</td>  
                        </tr>
                        @endforeach
                </tbody>

              </table>


              
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /page content -->
 

  </x-slot>
</x-backend-layout>
