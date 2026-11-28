<div class="nav-md" id="mainbody">
    <div class="container body">
      <div class="main_container">
<div class="col-md-3 left_col menu_fixed">
    <div class="left_col scroll-view">
      <div class="navbar nav_title" style="border: 0;">
        <a href="{{ route('admin.dashboard') }}" class="site_title"><span class="hm-logo-dot">H</span><span>HappiMynd Admin</span></a>
      </div>

      <div class="clearfix"></div>

      <?php
        $admin = auth('admin')->user();
        $can = function (array $roles) use ($admin) {
            return $admin->hasAnyRole(array_merge(['admin', 'super-admin'], $roles));
        };

        // Users per product (cached) - shown in each product menu
        $canSeeUserList = $admin->hasAnyRole(['admin', 'super-admin']);
        try {
            $productCounts = \App\Services\UserProductService::counts();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Sidebar product counts failed: ' . $e->getMessage());
            $productCounts = [];
        }
        $productUsersLink = function ($product, $label) use ($canSeeUserList, $productCounts) {
            if (!$canSeeUserList) {
                return '';
            }
            return '<li><a href="' . e(route('admin.customerListView', ['product' => $product])) . '">'
                . e($label) . ' Users <span class="badge">' . number_format((int)($productCounts[$product] ?? 0)) . '</span></a></li>';
        };
      ?>

      <!-- menu search -->
      <div class="hm-menu-search hidden-print">
        <i class="fa fa-search" aria-hidden="true"></i>
        <input type="search" id="hm-menu-search" placeholder="Search menu  ( / )" autocomplete="off" aria-label="Search the menu">
      </div>
      <div class="hm-menu-empty">No menu item matches.</div>

      <!-- sidebar menu -->
      <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

        {{-- ================= GENERAL ================= --}}
        <div class="menu_section">
          <h3>General</h3>
          <ul class="nav side-menu">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>

            @if($can(['user']))
                <li><a><i class="fa fa-users"></i> Users <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.customerListView') }}">User List <span class="badge">{{ number_format((int)($productCounts['all'] ?? 0)) }}</span></a></li>
                        <li><a href="{{ route('admin.addUserProfile.get') }}">Add User Profile</a></li>
                        <li><a href="{{ route('admin.assesmentList') }}">Assessment List</a></li>
                        <li><a href="{{ route('admin.assesmentListByUsername') }}">Assessment List by Username</a></li>
                        <li><a href="{{ route('admin.raisedQueryView') }}">Raised Queries</a></li>
                        <li><a href="{{ route('admin.getFeedbackList') }}">Feedback</a></li>
                    </ul>
                </li>
                <li><a><i class="fa fa-bullhorn"></i> Engagement <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.notifyUserView') }}">Notify User</a></li>
                        <li><a href="{{ route('admin.pushNotification') }}">Push Notification</a></li>
                        <li><a href="{{ route('admin.getRewardPointsInstanceList') }}">Reward Points</a></li>
                        <li><a href="{{ route('admin.rewardRedeemOptions') }}">Redeem Options</a></li>
                    </ul>
                </li>
                <li><a><i class="fa fa-user-secret"></i> Admins <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.adminListView') }}">Admin List</a></li>
                        <li><a href="{{ route('admin.addAdminView') }}">Add Admin</a></li>
                        <li><a href="{{ route('admin.ssoAdminNumbers') }}">App Admin Numbers</a></li>
                    </ul>
                </li>
            @endif
          </ul>
        </div>

        {{-- ================= PRODUCTS ================= --}}
        <div class="menu_section">
          <h3>Products</h3>
          <ul class="nav side-menu">
            @if($can(['assessments']))
                <li><a><i class="fa fa-heartbeat"></i> HappiLIFE <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        {!! $productUsersLink('happilife', 'HappiLIFE') !!}
                        <li><a href="{{ route('admin.happilifeReminders') }}">HappiLIFE Reminders</a></li>
                        <li><a href="{{ route('admin.getAllBatches.get') }}">Batches</a></li>
                        <li><a href="{{ route('admin.getAllCategories.get') }}">Categories</a></li>
                        <li><a href="{{ route('admin.allocateCategoryToBatch') }}">Allocate Categories to Batch</a></li>
                        <li><a href="{{ route('admin.modifyQuestions.get') }}">Questions</a></li>
                        <li><a href="{{ route('admin.importQuestions') }}">Import Questions</a></li>
                        <li><a href="{{ route('admin.batchCategoryIds') }}">Batch &amp; Category IDs</a></li>
                        <li><a href="{{ route('admin.ratingImages.get') }}">Upload Rating Images</a></li>
                        <li><a href="{{ route('admin.scoreCalculation.get') }}">Score Calculation &amp; Report Data</a></li>
                        <li><a href="{{ route('admin.reportOrder.get') }}">Report Order</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['HappiSELF']))
                <li><a><i class="fa fa-leaf"></i> HappiSELF <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        {!! $productUsersLink('happiself', 'HappiSELF') !!}
                        <li><a href="{{ route('admin.happiselfCoursesList') }}">Courses</a></li>
                        <li><a href="{{ route('admin.addHappiselfCourses') }}">Add Course</a></li>
                        <li><a href="{{ route('admin.importHappiself') }}">Import Courses (Excel)</a></li>
                        <li><a href="{{ route('admin.happiselfLibraryList') }}">Library</a></li>
                        <li><a href="{{ route('admin.addHappiselflibrary') }}">Add Library Item</a></li>
                        <li><a href="{{ route('admin.importHappiselfLibraryContent') }}">Import Library (Excel)</a></li>
                        <li><a href="{{ route('admin.uploadSelfMedia') }}">Upload Media</a></li>
                        <li><a href="{{ route('admin.dateForExportSelfdata') }}">Export User Data</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['HappiTALK']))
                <li><a><i class="fa fa-comments"></i> HappiTALK <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        {!! $productUsersLink('happitalk', 'HappiTALK') !!}
                        <li><a href="{{ route('admin.happitalkUpcomingSessions') }}">Upcoming Sessions</a></li>
                        <li><a href="{{ route('admin.happitalkBookingList') }}">Booking List</a></li>
                        <li><a href="{{ route('admin.happitalkBookingListByUsername') }}">Booking List by Username</a></li>
                        <li><a href="{{ route('admin.happitalkPsychologistList') }}">HappiTALK Psychologists</a></li>
                        <li><a href="{{ route('admin.allPsychologistListForTalk') }}">All Psychologists</a></li>
                        <li><a href="{{ route('admin.allOrgListForHappitalk') }}">Organizations</a></li>
                        <li><a href="{{ route('admin.usersCredit') }}">User Credits</a></li>
                        <li><a href="{{ route('admin.penaltyClause') }}">Penalty Clause</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['HappiGUIDE']))
                <li><a><i class="fa fa-compass"></i> HappiGUIDE <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        {!! $productUsersLink('happiguide', 'HappiGUIDE') !!}
                        <li><a href="{{ route('admin.happiguideSessionList') }}">Session List</a></li>
                        <li><a href="{{ route('admin.happiguideSessionListByUsername') }}">Session List by Username</a></li>
                        <li><a href="{{ route('admin.happiguidePsychologistList') }}">HappiGUIDE Psychologists</a></li>
                        <li><a href="{{ route('admin.allPsychologistListForGuide') }}">All Psychologists</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['HappiBUDDY']))
                <li><a><i class="fa fa-life-ring"></i> HappiBUDDY <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        {!! $productUsersLink('happibuddy', 'HappiBUDDY') !!}
                        <li><a href="{{ route('admin.userListToWhomPsyAssigned.get') }}">Buddy User List</a></li>
                        <li><a href="{{ route('admin.userListToWhomPsyAssignedByUsername.get') }}">Buddy User List by Username</a></li>
                        <li><a href="{{ route('admin.buddyPsyList') }}">HappiBUDDY Psychologists</a></li>
                        <li><a href="{{ route('admin.allPsychologistListForBuddy') }}">All Psychologists</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['HappiLEARN']))
                <li><a><i class="fa fa-graduation-cap"></i> HappiLEARN <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.happilearnUserList') }}">HappiLEARN Users <span class="badge">{{ number_format((int)($productCounts['happilearn'] ?? 0)) }}</span></a></li>
                        <li><a href="{{ route('admin.happilearn-content-list') }}">Content List</a></li>
                        <li><a href="{{ route('admin.import-happilearn-content') }}">Import Content (Excel)</a></li>
                        <li><a href="{{ route('admin.uploadLearnMedia') }}">Import Media</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['HappiVOICE']))
                <li><a><i class="fa fa-microphone"></i> HappiVOICE <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        {!! $productUsersLink('happivoice', 'HappiVOICE') !!}
                        <li><a href="{{ route('admin.score.all') }}">Scores</a></li>
                        <li><a href="{{ route('admin.prompt.show') }}">Prompts</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['psychologists']))
                <li><a><i class="fa fa-user-md"></i> Psychologists <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.psychologist.all.get') }}">All Psychologists</a></li>
                        <li><a href="{{ route('admin.psychologist.allAppointments.get') }}">Appointments</a></li>
                        <li><a href="{{ route('admin.languages.get') }}">Languages</a></li>
                        <li><a href="{{ route('admin.city.get') }}">Cities</a></li>
                        <li><a href="{{ route('admin.specializations.get') }}">Specializations</a></li>
                        <li><a href="{{ route('admin.expertLevels.get') }}">Expert Levels</a></li>
                        <li><a href="{{ route('admin.addExpertLevel.get') }}">Add Expert Level</a></li>
                    </ul>
                </li>
            @endif

            @if($can(['ChatBot']))
                <li><a><i class="fa fa-commenting"></i> ChatBot <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.chat-bot.discussion-topics.index') }}">Discussion Topics</a></li>
                        <li><a href="{{ route('admin.chat-bot.suicidal-thoughts') }}">Suicidal Thoughts</a></li>
                        @if($can(['recommendations']))
                            <li><a href="{{ route('admin.chat-bot.recommendation-categories.index') }}">Recommendation Categories</a></li>
                            <li><a href="{{ route('admin.chat-bot.recommendations.index') }}">Recommendations</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if($can(['assessments']))
                <li><a><i class="fa fa-list-alt"></i> ChatBot Assessment <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.chat-bot.assessments.index') }}">Assessments</a></li>
                        <li><a href="{{ route('admin.chat-bot.categories.index') }}">Categories</a></li>
                        <li><a href="{{ route('admin.chat-bot.categories.import') }}">Import Categories</a></li>
                        <li><a href="{{ route('admin.chat-bot.questions.index') }}">Questions</a></li>
                        <li><a href="{{ route('admin.chat-bot.questions.import') }}">Import Questions</a></li>
                        <li><a href="{{ route('admin.chat-bot.report-characteristics.index') }}">Report Characteristics</a></li>
                    </ul>
                </li>
            @endif
          </ul>
        </div>

        {{-- ================= BUSINESS ================= --}}
        @if($can(['plans', 'coupen', 'happimynd-code', 'organizations', 'campaigns', 'contacts']))
        <div class="menu_section">
          <h3>Business</h3>
          <ul class="nav side-menu">
            @if($can(['plans']))
            <li><a><i class="fa fa-credit-card"></i> Plans &amp; Payments <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.bundleDetail') }}">Manage Plans &amp; Bundles</a></li>
                    <li><a href="{{ route('admin.createBundle') }}">Create Bundle</a></li>
                    <li><a href="{{ route('admin.paymentDetail') }}">Payments</a></li>
                    <li><a href="{{ route('admin.paymentDetailIos') }}">iOS Purchases</a></li>
                    <li><a href="{{ route('admin.usersPlans.get') }}">User Plans</a></li>
                    <li><a href="{{ route('admin.usersPlans.additional.get') }}">HappiBUDDY / HappiTALK Plans</a></li>
                    <li><a href="{{ route('admin.addAllAvailableDates.get') }}">Add Available Dates</a></li>
                    <li><a href="{{ route('admin.allBookedDate.get') }}">Booked Dates</a></li>
                    <li><a>Services <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('admin.otherServices.get') }}">Other Services</a></li>
                            <li><a href="{{ route('admin.educationalServices.get') }}">Educational Services</a></li>
                            <li><a href="{{ route('admin.purchasedServices.get') }}">Services Purchased</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif

            @if($can(['coupen']))
            <li><a><i class="fa fa-tag"></i> Coupons <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <li><a href="{{ route('admin.coupon.show') }}">Coupons</a></li>
                  <li><a href="{{ route('admin.coupon.coupon-user') }}">Coupon Users</a></li>
                  <li><a href="{{ route('admin.codeExpirySettings') }}">Code Expiry Settings</a></li>
                </ul>
            </li>
            @endif

            @if($can(['happimynd-code']))
            <li><a><i class="fa fa-key"></i> HappiMynd Codes <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.tokenListView') }}">Token List</a></li>
                    <li><a href="{{ route('admin.generateTokenView') }}">Generate Tokens</a></li>
                    <li><a href="{{ route('admin.thriveCodeListView') }}">HappiApp Codes</a></li>
                    <li><a href="{{ route('admin.codeExpirySettings') }}">Code Expiry Settings</a></li>
                </ul>
            </li>
            @endif

            @if($can(['organizations']))
            <li><a><i class="fa fa-building"></i> Organizations <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.OrganizationView') }}">Organization List</a></li>
                    <li><a href="{{ route('admin.OrganizationDetail') }}">Organizations</a></li>
                </ul>
            </li>
            @endif

            @if($can(['campaigns']))
            <li><a href="{{ route('admin.campaigns.get') }}"><i class="fa fa-bullseye"></i> Campaigns</a></li>
            @endif

            @if($can(['contacts']))
            <li><a href="{{ route('admin.contacts') }}"><i class="fa fa-envelope-o"></i> Contact Leads</a></li>
            @endif
          </ul>
        </div>
        @endif

        {{-- ================= CONTENT ================= --}}
        @if($can(['static-data']))
        <div class="menu_section">
          <h3>Website Content</h3>
          <ul class="nav side-menu">
            <li><a><i class="fa fa-desktop"></i> Landing Page <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section1']) }}">Section 1</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section3']) }}">Section 3</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section4']) }}">Section 4</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section5']) }}">Section 5</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section6']) }}">Section 6 · Header</a></li>
                    <li><a href="{{ route('admin.staticData.showCarouselContent', ['carousel' => 'feelings_carousel']) }}">Section 6 · Feelings Carousel</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section7']) }}">Section 7 · Header</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageSection', ['section' => 'section8']) }}">Section 8 · Header</a></li>
                    <li><a href="{{ route('admin.staticData.showCarouselContent', ['carousel' => 'people_carousel']) }}">Sections 7 &amp; 8 · People Carousel</a></li>
                    <li><a href="{{ route('admin.staticData.showCarouselContent', ['carousel' => 'achievement_carousel']) }}">Section 9 · Achievement Carousel</a></li>
                    <li><a href="{{ route('admin.staticData.editLandingButtons') }}">Buttons</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageVideoUploadView') }}">Landing Page Video</a></li>
                    <li><a href="{{ route('admin.staticData.landingPageBitrixFormView') }}">Bitrix Form</a></li>
                </ul>
            </li>
            <li><a><i class="fa fa-pencil-square-o"></i> Pages &amp; Content <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.staticData.blogFormView') }}">Blog</a></li>
                    <li><a href="{{ route('admin.staticData.landingFaqView') }}">FAQ · General</a></li>
                    <li><a href="{{ route('admin.staticData.faqOrganizationView') }}">FAQ · Organisation</a></li>
                    <li><a href="{{ route('admin.staticData.ourteam') }}">Our Team</a></li>
                    <li><a href="{{ route('admin.staticData.ourClientsGet') }}">Our Clients</a></li>
                    <li><a href="{{ route('admin.staticData.quotes') }}">Quotes</a></li>
                    <li><a href="{{ route('admin.staticData.exploreServices') }}">Explore Services</a></li>
                    <li><a href="{{ route('admin.staticData.organization') }}">Organization</a></li>
                    <li><a href="{{ route('admin.staticData.termServices') }}">Privacy</a></li>
                    <li><a href="{{ route('admin.staticData.terms') }}">Terms and Services</a></li>
                </ul>
            </li>
            <li><a><i class="fa fa-mobile"></i> App &amp; Email <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ route('admin.staticData.offerScreen') }}">Offer Screen</a></li>
                    <li><a href="{{ route('admin.staticData.uploadDashboardCoverPic.get') }}">Dashboard Cover Image</a></li>
                    <li><a href="{{ route('admin.staticData.dashboardAppDownloadView') }}">App Download Link</a></li>
                    <li><a href="{{ route('admin.staticData.editOrientationEmail.get') }}">Orientation Email</a></li>
                    <li><a href="{{ route('admin.organizationDetailsWithLogo') }}">White Labelling</a></li>
                </ul>
            </li>
          </ul>
        </div>
        @endif

        {{-- ================= EXTERNAL APPS ================= --}}
        @if($admin->hasAnyRole(['admin','super-admin']))
        <div class="menu_section">
          <h3>Other Apps</h3>
          <ul class="nav side-menu">
            <li><a href="https://quizzard.happimynd.com/" target="_blank" rel="noopener noreferrer"><i class="fa fa-question-circle"></i> Quizzard <span class="fa fa-external-link pull-right"></span></a></li>
            <li><a href="https://happipause.happimynd.com/" target="_blank" rel="noopener noreferrer"><i class="fa fa-pause-circle"></i> HappiPAUSE <span class="fa fa-external-link pull-right"></span></a></li>
            <li><a href="https://psychologist-dashboard.vercel.app/login" target="_blank" rel="noopener noreferrer" title="Psychologist Dashboard"><i class="fa fa-stethoscope"></i> Psychologist Dashboard <span class="fa fa-external-link pull-right"></span></a></li>
          </ul>
        </div>
        @endif
      </div>
      <!-- /sidebar menu -->

      <div class="sidebar-footer hidden-small">
        <a data-toggle="tooltip" data-placement="top" title="Log out" href="{{ route('admin.logout') }}">
          <span class="fa fa-sign-out" aria-hidden="true"></span> <span class="hidden-xs">Log out</span>
        </a>
      </div>
    </div>
  </div>
