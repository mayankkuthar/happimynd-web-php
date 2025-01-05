<x-backend-layout>
  <x-slot name="title">
    Dashboard
  </x-slot>
  <x-slot name="content">
    <style>
      table{
   overflow-y:scroll;
   height:400px;
   width: 100%;
   display:block;
}
    </style>
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Dashboard</h3>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12">
            <div class="">
              <div class="x_content">
                <div class="row">
                  <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
                    <div class="tile-stats">
                      <h3>Total users: {{ $userCount+$organizationUsersCount }}</h3>
                      <h3>Total test users: {{ $testUserCount }}</h3>
                    </div>
                  </div>
                  <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
                    <div class="tile-stats">
                      <div class="count">{{ $userCount }}</div>
                      <h3>B2C Users</h3>
                    </div>
                  </div>
                  <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
                    <div class="tile-stats">
                      <div class="count">{{ $organizationUsersCount }}</div>

                      <h3>B2B users</h3>
                      {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                    </div>
                  </div>
                  <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
                    <div class="tile-stats">
                      <div class="count">{{ $organizationCount }}</div>

                      <h3>Total Organizations</h3>
                      {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                    </div>
                  </div>
                  <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
                    <div class="tile-stats">
                      <div class="count">{{ $tokenCount }}</div>

                      <h3>HappiMynd codes generated</h3>
                      {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                    </div>
                  </div>
                </div>


                <div class="row">
                  <div class="col-md-4 col-sm-4 ">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Organization Details</h2>
                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </li>
                          <li><a class="close-link"><i class="fa fa-close"></i></a>
                          </li>
                        </ul>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div class="dashboard-widget-content">
                          <div class="hidden-small">
                            <h2 class="line_30">Organization Token details</h2>
                            <table class="countries_list">
                              <thead>
                                <tr>
                                  <th>Organization</th>
                                  <th>Tokens Generated</th>
                                  <th>Used</th>
                                  <th>not Used</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($organizations as $organization)
                                <tr>
                                  <td>{{ $organization->name }}</td>
                                  <td class="fs15 fw700 text-right">{{ $organization->token_count }}</td>
                                  <td class="fs15 fw700 text-right">{{ $organization->token_used_count }}</td>
                                  <td class="fs15 fw700 text-right">{{ $organization->token_unused_count }}</td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-sm-4 ">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Product details</h2>
                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </li>
                          <li><a class="close-link"><i class="fa fa-close"></i></a>
                          </li>
                        </ul>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div class="dashboard-widget-content">
                          <div class="hidden-small">
                            <h2 class="line_30">Products bought by users</h2>
                            <table class="countries_list">
                              <thead>
                                <tr>
                                  <th>Product</th>
                                  <th>B2B</th>
                                  <th>B2C</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($products as $product)

                                @if($product->package)
                                  <tr>
                                    <td>{{ $product->package->name }}({{ $product->duration->name }}:{{ $product->duration->frequency }})</td>
                                    <td class="fs15 fw700 text-right">{{ $product->B2C_user_count }}</td>
                                    <td class="fs15 fw700 text-right">{{ $product->B2B_user_count }}</td>
                                  </tr>
                                @endif
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-sm-4 ">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Assessment related details</h2>
                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </li>
                          <li><a class="close-link"><i class="fa fa-close"></i></a>
                          </li>
                        </ul>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div class="dashboard-widget-content">
                          <div class="hidden-small">
                            <table class="countries_list">
                              <thead>
                                <tr>
                                  <th></th>
                                  <th>Count</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>Total Assessments</td>
                                  <td class="fs15 fw700 text-center">{{ $totalAssessmentCount }}</td>
                                </tr>
                                <tr>
                                  <td>Pending Assessments</td>
                                  <td class="fs15 fw700 text-center">{{ $pendingAssessmentCount }}</td>
                                  </tr>
                                  <tr>
                                    <td>Completed Assessments</td>
                                    <td class="fs15 fw700 text-center">{{ $completedAssessmentCount }}</td>
                                  </tr>
                                  <tr>
                                    <td>Reports Genearted</td>
                                    <td class="fs15 fw700 text-center">{{ $totalReportsGenerated }} / {{ $completedAssessmentCount }}</td>
                                  </tr>
                                </tr>
                              </tbody>



                              <thead>
                                <tr>
                                  <th></th>
                                  <th>Platform</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>Website</td>
                                  <td class="fs15 fw700 text-center">{{ $report_generated_on_website }}</td>
                                </tr>
                                <tr>
                                  <td>Android</td>
                                  <td class="fs15 fw700 text-center">{{ $report_generated_on_android }}</td>
                                  </tr>
                                  <tr>
                                    <td>IOS</td>
                                    <td class="fs15 fw700 text-center">{{ $report_generated_on_ios }}</td>
                                  </tr>
                                 
                                </tr>
                              </tbody>

                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>





                 <!--  <div class="col-md-4 col-sm-4 ">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Sessions Detail</h2>
                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </li>
                          <li><a class="close-link"><i class="fa fa-close"></i></a>
                          </li>
                        </ul>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div class="dashboard-widget-content">
                          <div class="hidden-small">
                            <table class="countries_list">
                               
                              <tbody>
                                <tr>
                                  <td>Total Completed Session</td>
                                  <td class="fs15 fw700 text-center">{{ $session_Details['session_done_count'] }}</td>
                                </tr>
                                <tr>
                                  <td>B2B Completed Session</td>
                                  <td class="fs15 fw700 text-center">{{ $session_Details['b2b_talk_session_done'] }}</td>
                                  </tr>
                                  <tr>
                                    <td>B2C Completed Session</td>
                                    <td class="fs15 fw700 text-center">{{ $session_Details['b2c_talk_session_done'] }}</td>
                                  </tr>
                                  <tr>
                                    <td>Fees Earned</td>
                                    <td class="fs15 fw700 text-center">{{ $session_Details['total_earned'] }}</td>
                                  </tr>
                                  <tr>
                                    <td>Fees To Be Shared</td>
                                    <td class="fs15 fw700 text-center">{{ $session_Details['be_to_shared'] }}</td>
                                  </tr>
                                </tr>
                              </tbody>

 

                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div> -->



                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /page content -->
  </x-slot>
</x-backend-layout>
