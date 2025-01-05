<div class="nav-md" id="mainbody">
    <div class="container body">
      <div class="main_container">
<div class="col-md-3 left_col menu_fixed">
    <div class="left_col scroll-view">
      <div class="navbar nav_title" style="border: 0;">
        <a href="{{ route('admin.dashboard') }}" class="site_title"> <span>Admin Panel</span></a>
      </div>

      <div class="clearfix"></div>

      <br />

      <?php
        // $admin_Details  = auth('admin')->user();
        // $get_role_id = DB::table('model_has_roles')->where('model_id' , $admin_Details->id)->pluck('role_id')->toArray();
        // $get_name_of_permission_options = DB::table('roles')->whereIn('id' , $get_role_id)->get();
      ?>

      <!-- sidebar menu -->
      <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu_section">
          <h3>General</h3>
          <ul class="nav side-menu">
            <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
              </ul>
            </li>
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))
                <li><a><i class="fa fa-users"></i> User <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    {{-- <li><a href="index.html">Add user</a></li> --}}
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.addUserProfile.get') }}">Add User Profile</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.customerListView') }}">User List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.addAdminView') }}">Add Admin</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.adminListView') }}">Admin List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.notifyUserView') }}">Notify User</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.raisedQueryView') }}">Raised Query</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.assesmentList') }}">Assessment Lists</a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.assesmentListByUsername') }}">Assessment List By Username</a></li> @endif


                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.getFeedbackList') }}">Feedback Lists</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.getRewardPointsInstanceList') }}">Reward Points Instance</a></li> @endif


                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'user' ]))<li><a href="{{ route('admin.pushNotification') }}">Push Notification</a></li> @endif



                    </ul>
                </li>
            @endif

            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'coupen']))
                <li><a><i class="fa fa-tag"></i> Coupon <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'coupen']))<li><a href="{{ route('admin.coupon.show') }}">Coupons</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'coupen']))<li><a href="{{ route('admin.coupon.coupon-user') }}">Coupon User</a></li> @endif
                </ul>
              </li>
            @endif

            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans' ]))
            <li><a><i class="fa fa-dollar"></i>Plans <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">

                    <!-- @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.createBundle') }}">Create Bundles</a></li> @endif -->

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.bundleDetail') }}">Bundles Detail</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.paymentDetail') }}">Payment Detail</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.paymentDetailIos') }}">IOS Purchases</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.usersPlans.get') }}">User's Plans</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.usersPlans.additional.get') }}">HappiBUDDY / HappiTALK</a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.addAllAvailableDates.get') }}"> Add All Available Dates</a></li>@endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))<li><a href="{{ route('admin.allBookedDate.get') }}">  All Booked Dates </a></li>@endif



                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'plans']))
                    <li><a> Services <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('admin.otherServices.get') }}"><i class="fa fa-cubes"></i> Other Services</span></a></li>
                            <li><a href="{{ route('admin.educationalServices.get') }}"><i class="fa fa-cubes"></i> Educational Services</span></a></li>
                            <li><a href="{{ route('admin.purchasedServices.get') }}"><i class="fa fa-cubes"></i> Services Purchased</span></a></li>
                        </ul>
                    </li>
                    @endif



                </ul>
            </li>
            @endif
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'happimynd-code' ]))
            <li><a><i class="fa fa-cubes"></i> Happimynd Code <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.generateTokenView') }}">Generate Tokens</a></li>
                    <li><a href="{{ route('admin.tokenListView') }}">Tokens List</a></li>
                    <li><a href="{{ route('admin.thriveCodeListView') }}">HappiApp Codes List</a></li>
                </ul>
            </li>
            @endif
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'organizations']))
            <li><a><i class="fa fa-building"></i>Organizations <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.OrganizationView') }}"> Organizations List</a></li>
                    <li><a href="{{ route('admin.OrganizationDetail') }}"> Organizations</a></li>
                </ul>
            </li>
            @endif
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'static-data']))
            <li><a><i class="fa fa-pencil-square-o"></i>Static Data <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.staticData.ourteam') }}"> Our Team</a></li>
                    <li> <a>Landing page cms</a>
                      <ul class="nav child_menu">
                        <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section1'])}}">section1</a></li>
                        <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section3'])}}">section3</a></li>
                        <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section4'])}}">section4</a></li>
                        <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section5'])}}">section5</a></li>
                        <li><a>section6</a>
                          <ul class="nav child_menu">
                            <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section6'])}}">Header</a></li>
                            <li><a href="{{route('admin.staticData.showCarouselContent', ['carousel' => 'feelings_carousel'])}}">feelings_carousel</a></li>
                          </ul>
                        </li>
                        <li><a>section7</a>
                        <ul class="nav child_nav">
                          <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section7'])}}">Header</a></li>
                          <li><a href="{{route('admin.staticData.showCarouselContent', ['carousel' => 'people_carousel'])}}">people_carousel</a></li>
                        </ul>
                        </li>

                        <li><a>section8</a>
                          <ul class="nav child_nav">
                            <li><a href="{{route('admin.staticData.landingPageSection', ['section' => 'section8'])}}">Header</a></li>
                            <li><a href="{{route('admin.staticData.showCarouselContent', ['carousel' => 'people_carousel'])}}">people_carousel</a></li>
                          </ul>
                        </li>

                        <li><a>section9</a>
                          <ul class="nav child_nav">
                            <li><a href="{{route('admin.staticData.showCarouselContent', ['carousel' => 'achievement_carousel'])}}">achievement_carousel</a></li>
                          </ul>
                        </li>
                        <li> <a href=" {{ route('admin.staticData.editLandingButtons') }}">buttons</a></li>


                      </ul>
                    </li>
                    <li><a href="{{ route('admin.staticData.ourClientsGet') }}">Our Clients</a></li>
                    <li><a href="{{ route('admin.staticData.quotes') }}"> Quotes</a></li>
                    <li><a href="{{ route('admin.staticData.termServices') }}"> Privacy</a></li>
                    <li><a href="{{ route('admin.staticData.organization') }}"> Organization</a></li>
                    <li><a href="{{ route('admin.staticData.terms') }}"> Terms and Services</a></li>
                    <li><a href="{{ route('admin.staticData.uploadDashboardCoverPic.get') }}"> Dashboard Cover Image</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageVideoUploadView') }}"> Landing Page Video</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageBitrixFormView') }}"> Bitrix Form</a></li>
                    <li><a href="{{ route('admin.staticData.dashboardAppDownloadView') }}">App Download Link</a></li>
                    <li><a href="{{ route('admin.staticData.blogFormView') }}"> Blog</a></li>
                    <li><a href="{{ route('admin.staticData.landingFaqView') }}">FAQ General</a></li>
                    <li><a href="{{ route('admin.staticData.faqOrganizationView') }}">FAQ Organisation</a></li>
                    <li><a href="{{ route('admin.staticData.exploreServices') }}">Explore Services</a></li>
                    <li><a href="{{ route('admin.staticData.editOrientationEmail.get') }}">Orientation Email</a></li>

                    <li><a href="{{ route('admin.staticData.offerScreen') }}">Offer Screen</a></li>


                    <li><a href="{{ route('admin.organizationDetailsWithLogo') }}"> White Labelling</a></li>

                </ul>
            </li>
            @endif
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'campaigns']))
            <li><a><i class="fa fa-pencil-square-o"></i>Campaigns <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <li><a href="{{ route('admin.campaigns.get') }}"><i class="fa fa-cubes"></i> Campaign</span></a></li>
                </ul>
            </li>
            @endif
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))
                <li><a><i class="fa fa-users"></i> Assessment <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.getAllBatches.get") }}">Batches</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.getAllCategories.get") }}">Categories</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.allocateCategoryToBatch') }}">Allocate Categories to Batch</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.modifyQuestions.get') }}">Questions</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.importQuestions') }}">Import Questions</a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.batchCategoryIds') }}">Batch & Category IDs</a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.ratingImages.get') }}">Upload Rating Images</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.scoreCalculation.get') }}">Score Calculation and Report data</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.reportOrder.get') }}">Report Order</a></li> @endif
                    </ul>
                </li>
            @endif
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))
                <li><a><i class="fa fa-users"></i> Psychologist <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))<li><a href="{{ route('admin.psychologist.all.get') }}">All Psychologist</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))<li><a href="{{ route('admin.languages.get') }}">Languages</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))<li><a href="{{ route('admin.city.get') }}">Cities</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))<li><a href="{{ route('admin.specializations.get') }}">Specializations</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))<li><a href="{{ route('admin.psychologist.allAppointments.get') }}">Appointments</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'psychologists']))
                      <li class="active"><a>Expert Levels<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="display: block;">
                          <li class="sub_menu"><a href="{{ route('admin.expertLevels.get') }}">all expert levels</a>
                          </li>
                          <li>
                            <a href="{{ route('admin.addExpertLevel.get') }}">Add Expert Level</a>
                          </li>
                        </ul>
                      </li>
                    @endif
                    </ul>
                </li>
            @endif



            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiBUDDY' ]))
                <li><a><i class="fa fa-solid fa-database"></i> HappiBuddy <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiBUDDY']))<li><a href="{{ route('admin.allPsychologistListForBuddy')}}">All Psychologist List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiBUDDY']))<li><a href="{{ route('admin.buddyPsyList')}}">HappiBUDDY Psychologist List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiBUDDY']))<li><a href="{{ route('admin.userListToWhomPsyAssigned.get') }}">  Buddy UserList</span></a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiBUDDY']))<li><a href="{{ route('admin.userListToWhomPsyAssignedByUsername.get') }}">  Buddy UserList By Username</span></a></li> @endif


                    </ul>
                </li>
            @endif





            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiLEARN' ]))
                <li><a><i class="fa fa-solid fa-database"></i> HappiLearn <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiLEARN']))<li><a href="{{ route('admin.import-happilearn-content')}}">Import HappiLearn excel file</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiLEARN']))<li><a href="{{ route('admin.uploadLearnMedia')}}">HappiLearn Import Media</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiLEARN']))<li><a href="{{ route('admin.happilearn-content-list')}}">HappiLearn Content List</a></li> @endif

                    </ul>
                </li>
            @endif


            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))
                <li><a><i class="fa fa-solid fa-database"></i> HappiSelf <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.dateForExportSelfdata')}}">Export HappiSelf User Data</a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.uploadSelfMedia')}}">Upload HappiSelf Media</a></li> @endif

                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.addHappiselfCourses')}}">Add HappiSelf Courses</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.happiselfCoursesList')}}">HappiSelf Courses List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.importHappiself')}}">Import HappiSelf Course excel file</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.addHappiselflibrary')}}">Add Happiself Library</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.happiselfLibraryList')}}">Happiself Library List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiSELF']))<li><a href="{{ route('admin.importHappiselfLibraryContent')}}">Import Happiself Library excel</a></li> @endif
                    </ul>
                </li>
            @endif



            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))
                <li><a><i class="fa fa-solid fa-database"></i> HappiTalk <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    <!-- @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.talkTds')}}">TDS</a></li> @endif -->
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.penaltyClause')}}">Penalty Clause</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.allPsychologistListForTalk')}}">All Psychologist List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.happitalkPsychologistList')}}">HappiTalk Psychologist List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.allOrgListForHappitalk')}}">Organization List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.happitalkBookingList')}}">Booking List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.happitalkBookingListByUsername')}}">Booking List By Username</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiTALK']))<li><a href="{{ route('admin.usersCredit')}}">User's Credit</a></li> @endif


                    </ul>
                </li>
            @endif



            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiGUIDE']))
                <li><a><i class="fa fa-solid fa-database"></i> HappiGuide <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiGUIDE']))<li><a href="{{ route('admin.allPsychologistListForGuide')}}">All Psychologist List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiGUIDE']))<li><a href="{{ route('admin.happiguidePsychologistList')}}">HappiGuide Psychologist List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiGUIDE']))<li><a href="{{ route('admin.happiguideSessionList')}}">HappiGuide Session List</a></li> @endif
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiGUIDE']))<li><a href="{{ route('admin.happiguideSessionListByUsername')}}">HappiGuide Session List By Username</a></li> @endif

                    </ul>
                </li>
            @endif

            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiVOICE']))
                <li><a><i class="fa fa-tag"></i> HappiVoice <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiVOICE']))<li><a href="{{ route('admin.prompt.show') }}">Prompts</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'HappiVOICE']))<li><a href="{{ route('admin.score.all') }}">Scores</a></li> @endif
                </ul>
              </li>
            @endif

            {{-- Chat Bot --}}

            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'ChatBot']))
                <li><a><i class="fa fa-tag"></i> ChatBot <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'ChatBot']))<li><a href="{{ route('admin.chat-bot.discussion-topics.index') }}">Discussion Topics</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'ChatBot']))<li><a href="{{ route('admin.chat-bot.suicidal-thoughts') }}">Suicidal Thoughts</a></li> @endif

                  {{-- Recommendations --}}
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'recommendations']))
                  <li>
                    <a href="{{ route('admin.chat-bot.recommendation-categories.index') }}">Categories</a>
                  </li>
                  @endif

                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'recommendations']))
                  <li>
                    <a href="{{ route('admin.chat-bot.recommendations.index') }}">Recommendations</a>
                  </li>
                  @endif
                </ul>
              </li>
            @endif

            {{-- Chat Bot Assessments --}}
            @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))
              <li><a><i class="fa fa-users"></i> ChatBot Assessment <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.chat-bot.categories.index") }}">Categories</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.chat-bot.categories.import") }}">Import Categories</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route('admin.chat-bot.report-characteristics.index') }}">Report Characteristics</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.chat-bot.questions.index") }}">Questions</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.chat-bot.questions.import") }}">Import Questions</a></li> @endif
                  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin' , 'assessments']))<li><a href="{{ route("admin.chat-bot.assessments.index") }}">Assessments</a></li> @endif
                </ul>
              </li>
            @endif

            <!-- @if(auth('admin')->user()->hasAnyRole(['admin','super-admin']))
                <li><a><i class="fa fa-solid fa-sitemap"></i> WhiteLabelling <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    @if(auth('admin')->user()->hasAnyRole(['admin','super-admin']))<li><a href="{{ route('admin.organizationDetailsWithLogo')}}">Organization Logo</a></li> @endif
                    </ul>
                </li>
            @endif -->


           <!--  @if(auth('admin')->user()->hasAnyRole(['admin','super-admin']))
                <li><a href="{{ route('admin.pushNotification')}}"><i class="fa fa-solid fa-bell"></i></i>Push Notification</span></a></li>
            @endif -->


            <!-- @if(auth('admin')->user()->hasAnyRole(['admin','super-admin']))
                <li><a href="{{ route('admin.addAllAvailableDates.get') }}"><i class="fa fa-cubes"></i> Add All Available Dates</span></a></li>
                <li><a href="{{ route('admin.allBookedDate.get') }}"><i class="fa fa-cubes"></i> All Booked Dates</span></a></li>
            @endif
             -->

            <!-- @if(auth('admin')->user()->hasAnyRole(['admin','super-admin']))
                <li><a><i class="fa fa-pencil-square-o"></i>Services <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.otherServices.get') }}"><i class="fa fa-cubes"></i> Other Services</span></a></li>
                        <li><a href="{{ route('admin.educationalServices.get') }}"><i class="fa fa-cubes"></i> Educational Services</span></a></li>
                        <li><a href="{{ route('admin.purchasedServices.get') }}"><i class="fa fa-cubes"></i> Services Purchased</span></a></li>
                    </ul>
                </li>
            @endif -->
          </ul>
        </div>
      </div>
      <!-- /sidebar menu -->

      <!-- /menu footer buttons -->
      <div class="sidebar-footer hidden-small">
        <a data-toggle="tooltip" data-placement="top" title="Settings">
          <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="FullScreen">
          <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Lock">
          <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('admin.logout') }}">
          <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
        </a>
      </div>
      <!-- /menu footer buttons -->
    </div>
  </div>
