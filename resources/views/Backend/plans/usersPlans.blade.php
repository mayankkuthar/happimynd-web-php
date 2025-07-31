
<x-backend-layout>
  <x-slot name="title">
    Dashboard
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Details of plans bought by users</h3>
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


                
                <!-- <div class="excel-button">
                    <form action="{{route('admin.downloadUserPlanXL')}}">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" >
                    

                    <button type="submit" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
                    <span id="buttonText">Get Excel</span>

                  </button>
                  </form>
                </div> -->

              <?php 
                $plan_col_limit = 1;
              ?>
              @foreach($users as $user)
               
                <?php 
                 $count = $user->bundleStatus->pluck('plans.package.name')->count();
                 if($count > $plan_col_limit){
                  $plan_col_limit = $count;
                 }
                ?>
              @endforeach


              <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>username</th>
                    <th>email</th>
                    <th>B2B / B2C</th>
                    <th>Organization</th>
                    <th>No. of Plans Bought</th>
                    @for($i=0 ; $i < $plan_col_limit ; $i++)
                      <th>Plans</th>
                    @endfor
                    <!-- <th>Plans</th> -->
                  </tr>
                </thead>
                <tbody>
                  @foreach($users as $user)
                    <tr>
                      <td>
                        {{ $loop->iteration }}
                      </td>
                      <td>
                        {{ $user->username }}(User id: {{ $user->id }})
                      </td>
                      <td>
                        {{ $user->email }}
                      </td>
                      <td>
                        @if($user->isOrganizationUser()) B2B @else B2C @endif
                      </td>

                      <td>
                        {{ ($user->isOrganizationUser())? $user->userToken->token->organization()->withTrashed()->first()->name : 'Individual' }}
                      </td>
                      <td>
                        {{ $user->bundleStatus->count() }}
                      </td>
                      <!-- <td>fsfs</td> -->
                     <!-- <td>
                        <?php 
                          $array = $user->bundleStatus->pluck('plans.package.name')->toArray();
                          $search = 'HappiLIFE Summary Reading';
                          $replace = 'HappiLearn';
                          foreach ($array as $key => $value) {
                              if ($value == $search) {
                                  $array[$key] = $replace;
                                  break;
                              }
                          }
                        ?>
                        {{ implode(" || ", $array) }}
                      </td> -->

                      <?php 
                        $array = $user->bundleStatus->pluck('plans.package.name')->toArray();
                        $search = 'HappiLIFE Summary Reading';
                        $replace = 'HappiLearn';

                        $td_count = count($array);
                        $diff = $plan_col_limit - $td_count;

                      ?>
                      @foreach ($array as $key => $value) 
                          @if(!$value)
                            <td></td>
                          @elseif ($value == $search) 
                            <td>HappiLEARN</td>
                          @else
                            <td>{{$value}}</td>
                          @endif
                      @endforeach
                      @for($i=0 ; $i < $diff ; $i++)
                        <th>-</th>
                      @endfor 

                    </tr>
                  @endforeach
                </tbody>

              </table>

             <div class="custompaginationbar"> {{$users->links()}}</div>

              
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /page content -->


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
