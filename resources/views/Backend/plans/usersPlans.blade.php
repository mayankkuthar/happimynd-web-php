<x-backend-layout>
  <x-slot name="title">
    Dashboard
  </x-slot>
  
  <x-slot name="content">
    <!-- Enhanced page content with better styling -->
    <div class="right_col" role="main">
      <div class="dashboard-container">
        <!-- Enhanced header section with better typography and spacing -->
        <div class="page-header">
          <div class="header-content">
            <div class="title-section">
              <h2 class="page-title">
                <i class="fa fa-users text-primary"></i>
                User Plan Analytics
              </h2>
              <p class="page-subtitle">Monitor and analyze user subscription patterns</p>
            </div>
          </div>
        </div>

        <!-- Separate filter section below header -->
        <div class="filter-container">
          <div class="card filter-card">
            <div class="card-body">
              <h6 class="filter-title">
                <i class="fa fa-download"></i> Export Data
              </h6>
              <form action="{{route('admin.downloadUserPlanXL')}}" method="GET" class="export-form">
                <div class="date-inputs">
                  <div class="input-group">
                    <label for="start_date" class="input-label">Start Date</label>
                    <div class="date-input-wrapper">
                      <i class="fa fa-calendar input-icon" onclick="openCalendar('start_date')"></i>
                      <input type="date" id="start_date" name="start_date" class="form-control date-input" required>
                    </div>
                  </div>
                  <div class="input-group">
                    <label for="end_date" class="input-label">End Date</label>
                    <div class="date-input-wrapper">
                      <i class="fa fa-calendar input-icon" onclick="openCalendar('end_date')"></i>
                      <input type="date" id="end_date" name="end_date" class="form-control date-input" required>
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary export-btn" onclick="return validateDate(document.getElementById('start_date'), document.getElementById('end_date'));">
                  <i class="fa fa-download"></i>
                  <span>Download Excel</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Enhanced statistics cards -->
        <div class="stats-section">
          <div class="row">
            <div class="col-md-3 col-sm-6">
              <div class="stat-card">
                <div class="stat-icon bg-primary">
                  <i class="fa fa-users"></i>
                </div>
                <div class="stat-content">
                  <h3 class="stat-number">{{ $users->total() }}</h3>
                  <p class="stat-label">Total Users</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="stat-card">
                <div class="stat-icon bg-success">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                  <h3 class="stat-number">{{ $users->sum(function($user) { return $user->bundleStatus->count(); }) }}</h3>
                  <p class="stat-label">Total Plans Sold</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="stat-card">
                <div class="stat-icon bg-info">
                  <i class="fa fa-building"></i>
                </div>
                <div class="stat-content">
                  <h3 class="stat-number">{{ $users->filter(function($user) { return $user->isOrganizationUser(); })->count() }}</h3>
                  <p class="stat-label">B2B Users</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="stat-card">
                <div class="stat-icon bg-warning">
                  <i class="fa fa-user"></i>
                </div>
                <div class="stat-content">
                  <h3 class="stat-number">{{ $users->filter(function($user) { return !$user->isOrganizationUser(); })->count() }}</h3>
                  <p class="stat-label">B2C Users</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced table section -->
        <div class="table-section">
          <div class="card table-card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fa fa-table"></i>
                User Plans Details
              </h5>
              <div class="card-tools">
                <div class="table-info">
                  Showing {{ $users->count() }} of {{ $users->total() }} entries
                </div>
              </div>
            </div>
            
            <div class="card-body">
              <div class="table-responsive">
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

                <table id="datatable-buttons" class="table table-hover enhanced-table">
                  <thead class="table-header">
                    <tr>
                      <th class="text-center">#</th>
                      <th><i class="fa fa-user"></i> User Details</th>
                      <th><i class="fa fa-envelope"></i> Email</th>
                      <th><i class="fa fa-tag"></i> Type</th>
                      <th><i class="fa fa-building"></i> Organization</th>
                      <th class="text-center"><i class="fa fa-shopping-bag"></i> Plans Count</th>
                      <th><i class="fa fa-calendar"></i> Purchase Date</th>
                      @for($i=0 ; $i < $plan_col_limit ; $i++)
                        <th><i class="fa fa-package"></i> Plan {{ $i + 1 }}</th>
                      @endfor
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                      <tr class="table-row">
                        <td class="text-center">
                          <span class="row-number">{{ $loop->iteration }}</span>
                        </td>
                        <td class="user-cell">
                          <div class="user-info">
                            <div class="user-avatar">
                              <i class="fa fa-user-circle"></i>
                            </div>
                            <div class="user-details">
                              <div class="username">{{ $user->username }}</div>
                              <small class="user-id">ID: {{ $user->id }}</small>
                            </div>
                          </div>
                        </td>
                        <td class="email-cell">
                          <a href="mailto:{{ $user->email }}" class="email-link">
                            {{ $user->email }}
                          </a>
                        </td>
                        <td>
                          @if($user->isOrganizationUser()) 
                            <span class="badge badge-business">B2B</span>
                          @else 
                            <span class="badge badge-consumer">B2C</span>
                          @endif
                        </td>
                        <td class="org-cell">
                          @if($user->isOrganizationUser())
                            <span class="org-name">
                              <i class="fa fa-building text-muted"></i>
                              {{ $user->userToken->token->organization()->withTrashed()->first()->name }}
                            </span>
                          @else
                            <span class="individual-user">
                              <i class="fa fa-user text-muted"></i>
                              Individual
                            </span>
                          @endif
                        </td>
                        <td class="text-center">
                          <span class="plans-count">{{ $user->bundleStatus->count() }}</span>
                        </td>
                        <td class="date-cell">
                          @if($user->bundleStatus->count() > 0)
                            <span class="purchase-date">
                              <i class="fa fa-calendar-check text-success"></i>
                              {{ $user->bundleStatus->sortByDesc('created_at')->first()->created_at->format('M d, Y') }}
                            </span>
                          @else
                            <span class="no-purchase">
                              <i class="fa fa-minus text-muted"></i>
                              No purchases
                            </span>
                          @endif
                        </td>

                        <?php 
                          $array = $user->bundleStatus->pluck('plans.package.name')->toArray();
                          $search = 'HappiLIFE Summary Reading';
                          $replace = 'HappiLearn';
                          $td_count = count($array);
                          $diff = $plan_col_limit - $td_count;
                        ?>
                        
                        @foreach ($array as $key => $value) 
                          @if(!$value)
                            <td class="plan-cell">
                              <span class="no-plan">-</span>
                            </td>
                          @elseif ($value == $search) 
                            <td class="plan-cell">
                              <span class="plan-badge plan-happilearn">HappiLEARN</span>
                            </td>
                          @else
                            <td class="plan-cell">
                              <span class="plan-badge plan-default">{{$value}}</span>
                            </td>
                          @endif
                        @endforeach
                        
                        @for($i=0 ; $i < $diff ; $i++)
                          <td class="plan-cell">
                            <span class="no-plan">-</span>
                          </td>
                        @endfor 
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Enhanced pagination -->
            <div class="card-footer">
              <x-pagination-dropdown :paginator="$users" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced styles -->
    <style>
      .dashboard-container {
        padding: 20px;
        background-color: #f8f9fa;
        min-height: 100vh;
        max-width: 100%;
        overflow-x: hidden;
      }

      /* Header Styles */
      .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      }

      .header-content {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
      }

      /* Filter Container Styles */
      .filter-container {
        margin-bottom: 30px;
        display: flex;
        justify-content: flex-start;
      }

      .page-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
      }

      .page-subtitle {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
      }

      /* Filter Card Styles */
      .filter-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        max-width: 450px;
        width: 100%;
      }
      
      .filter-card .card-body {
        padding: 25px;
      }

      .filter-title {
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.1rem;
      }

      .export-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .date-inputs {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
      }

      .input-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 180px;
      }

      .input-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
      }

      .date-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
      }

      .input-icon {
        position: absolute;
        left: 12px;
        color: #667eea;
        z-index: 2;
        font-size: 16px;
        cursor: pointer;
        transition: color 0.3s ease;
      }

      .input-icon:hover {
        color: #764ba2;
      }

      .date-input {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 12px 12px 40px;
        transition: all 0.3s ease;
        font-size: 14px;
        width: 100%;
        background: white;
        cursor: pointer;
      }

      .date-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
      }

      .date-input::-webkit-calendar-picker-indicator {
        opacity: 0;
        position: absolute;
        right: 10px;
        width: 20px;
        height: 20px;
        cursor: pointer;
      }

      .export-btn {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: center;
        color: white;
        text-decoration: none;
        font-size: 14px;
      }

      .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
      }

      /* Statistics Cards */
      .stats-section {
        margin-bottom: 30px;
      }

      .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
      }

      .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      }

      .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
      }

      .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: #333;
      }

      .stat-label {
        margin: 5px 0 0 0;
        color: #666;
        font-weight: 500;
      }

      .bg-primary { background: linear-gradient(45deg, #667eea, #764ba2); }
      .bg-success { background: linear-gradient(45deg, #56ab2f, #a8e6cf); }
      .bg-info { background: linear-gradient(45deg, #3498db, #85c1e9); }
      .bg-warning { background: linear-gradient(45deg, #f39c12, #f7dc6f); }

      /* Table Styles */
      .table-card {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        overflow: hidden;
      }

      .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .card-title {
        margin: 0;
        color: #333;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .table-info {
        color: #666;
        font-size: 0.9rem;
      }

      .enhanced-table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
      }

      .table-header th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        padding: 15px 12px;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
      }

      .table-header th:first-child {
        border-top-left-radius: 0;
      }

      .table-header th:last-child {
        border-top-right-radius: 0;
      }

      .table-row {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
      }

      .table-row:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }

      .table-row td {
        padding: 15px 12px;
        vertical-align: middle;
        border: none;
      }

      /* User Cell Styles */
      .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .user-avatar {
        font-size: 32px;
        color: #667eea;
      }

      .username {
        font-weight: 600;
        color: #333;
      }

      .user-id {
        color: #666;
        font-size: 0.85rem;
      }

      .email-link {
        color: #667eea;
        text-decoration: none;
        transition: color 0.3s ease;
      }

      .email-link:hover {
        color: #764ba2;
        text-decoration: underline;
      }

      /* Badge Styles */
      .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .badge-business {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
      }

      .badge-consumer {
        background: linear-gradient(45deg, #56ab2f, #a8e6cf);
        color: white;
      }

      /* Plan Badge Styles */
      .plan-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
      }

      .plan-default {
        background: linear-gradient(45deg, #3498db, #85c1e9);
        color: white;
      }

      .plan-happilearn {
        background: linear-gradient(45deg, #e74c3c, #f1948a);
        color: white;
      }

      .no-plan {
        color: #999;
        font-style: italic;
      }

      .row-number {
        display: inline-block;
        width: 30px;
        height: 30px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border-radius: 50%;
        line-height: 30px;
        text-align: center;
        font-weight: 600;
        font-size: 0.85rem;
      }

      .plans-count {
        display: inline-block;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 20px;
        font-weight: 600;
        color: #333;
        border: 2px solid #e9ecef;
      }

      .purchase-date, .no-purchase {
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .org-name, .individual-user {
        display: flex;
        align-items: center;
        gap: 8px;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .dashboard-container {
          padding: 15px;
        }
        
        .page-header {
          padding: 20px;
          margin-bottom: 15px;
        }
        
        .filter-container {
          justify-content: center;
          margin-bottom: 20px;
        }
        
        .filter-card {
          max-width: 100%;
        }

        .date-inputs {
          flex-direction: column;
          gap: 15px;
        }
        
        .input-group {
          min-width: auto;
        }

        .page-title {
          font-size: 1.8rem;
        }

        .stat-card {
          flex-direction: column;
          text-align: center;
        }

        .table-responsive {
          font-size: 0.85rem;
        }

        .user-info {
          flex-direction: column;
          gap: 5px;
        }
      }
      
      @media (min-width: 769px) and (max-width: 1024px) {
        .filter-container {
          justify-content: flex-start;
        }
        
        .filter-card {
          max-width: 400px;
        }
      }
      
      @media (min-width: 1025px) {
        .filter-container {
          justify-content: flex-start;
        }
      }

      /* Animation for loading */
      .table-row {
        animation: fadeInUp 0.5s ease-out;
      }

      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  </x-slot>

  <x-slot name="js">
    <script>
      // Function to open calendar when icon is clicked
      function openCalendar(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
          // First try the modern showPicker method
          if (input.showPicker) {
            try {
              input.showPicker();
            } catch (error) {
              // Fallback for browsers that don't support showPicker
              input.focus();
              input.click();
            }
          } else {
            // Fallback for older browsers
            input.focus();
            input.click();
          }
        }
      }

      function validateDate(start, end) {
        if (!start.value || !end.value) {
          Swal.fire({
            title: 'Missing Dates',
            text: 'Please select both start and end dates.',
            icon: 'warning',
            confirmButtonColor: '#667eea'
          });
          return false;
        }
        
        const _MS_PER_DAY = 1000 * 60 * 60 * 24;
        const start_date = new Date(start.value);
        const end_date = new Date(end.value);
        const utc1 = Date.UTC(start_date.getFullYear(), start_date.getMonth(), start_date.getDate());
        const utc2 = Date.UTC(end_date.getFullYear(), end_date.getMonth(), end_date.getDate());
        
        const diffDays = Math.floor((utc2 - utc1) / _MS_PER_DAY);
        
        if (diffDays < 0) {
          Swal.fire({
            title: 'Invalid Date Range',
            text: 'End date must be after start date.',
            icon: 'error',
            confirmButtonColor: '#667eea'
          });
          return false;
        }
        
        if (diffDays > 31) {
          Swal.fire({
            title: 'Date Range Too Large',
            text: 'End date must be at most 31 days from start date.',
            icon: 'warning',
            confirmButtonColor: '#667eea'
          });
          return false;
        }
        
        // Show loading animation
        Swal.fire({
          title: 'Preparing Export...',
          text: 'Please wait while we generate your Excel file.',
          icon: 'info',
          showConfirmButton: false,
          allowOutsideClick: false,
          timer: 2000
        });
        
        return true;
      }

      // Add smooth scrolling and animations
      document.addEventListener('DOMContentLoaded', function() {
        // Make date inputs clickable to open calendar
        const dateInputs = document.querySelectorAll('.date-input');
        
        dateInputs.forEach(input => {
          // Make the input itself clickable to open calendar
          input.addEventListener('click', function() {
            if (this.showPicker) {
              try {
                this.showPicker();
              } catch (error) {
                // Fallback already handled by default browser behavior
              }
            }
          });
        });

        // Animate stat cards on scroll
        const observerOptions = {
          threshold: 0.1,
          rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateY(0)';
            }
          });
        }, observerOptions);

        // Observe stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
          card.style.opacity = '0';
          card.style.transform = 'translateY(20px)';
          card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
          observer.observe(card);
        });

        // Add table row hover effects
        const tableRows = document.querySelectorAll('.table-row');
        tableRows.forEach((row, index) => {
          row.style.animationDelay = `${index * 0.05}s`;
        });
      });
    </script>
  </x-slot>
</x-backend-layout>