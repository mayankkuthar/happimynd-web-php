<x-backend-layout>
    <x-slot name="title">
        Score Lists
    </x-slot>
    <x-slot name="css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .dt-buttons {
                display: none;
            }

            div#datatable-buttons_paginate,
            #datatable-buttons_info {
                display: none;
            }

            .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
                margin-bottom: 0px;
                margin-left: 15px;
            }

            div.dataTables_wrapper div.dataTables_length label {
                display: none;
            }
        </style>
    </x-slot>
    <x-slot name="content">
        <div class="right_col" role="main">
        <div class="row">
        <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>Scores</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive custom-change-table">

                            <div class="excel-button">
                                <form action="{{route('admin.downloadScoreXL')}}">
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" id="start_date" name="start_date">
                                    <label for="end_date">End Date:</label>
                                    <input type="date" id="end_date" name="end_date">

                                    <button type="submit" id="downloadbtn" class="btn  btn-rounded btn-primary" onclick="return validateDate(start_date,end_date);">
                                        <span id="buttonText">Download Excel</span>
                                        <span class="spinner-grow spinner-grow-sm loader" id="loader" role="status" style="display: none;" aria-hidden="true"></span><span class="loader" style="display: none"> Generating...</span>
                                    </button>
                                </form>
                            </div>

                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Nickname</th>
                                        <th>E-mail</th>
                                        <th>Mobile</th>
                                        <th>Organization</th>
                                        <th>Token</th>
                                        <th>Coupon Used</th>
                                        <th>Profile</th>
                                        <th>Result</th>
                                        <th>Score</th>
                                        <th>Smoothness</th>
                                        <th>Liveliness</th>
                                        <th>Control</th>
                                        <th>Energy Range</th>
                                        <th>Clarity</th>
                                        <th>Crispness</th>
                                        <th>Speech Rate</th>
                                        <th>Pause Duration</th>
                                        <th>Inferred At</th>
                                        {{--
                                        <th>Profession</th>
                                        <th>Account Status</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Date</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th style="text-align:center;">Action</th>
                                        --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scores as $score)
                                    <?php
                                        if($score->user->usersRating != null){
                                          $emoji_path = $score->user->usersRating->applicationRatingEmoji->image;
                                        }else{
                                          $emoji_path = '';
                                        }

                                        ?>
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $score->user->username ?? '-' }}</td>
                                        <td>{{ $score->user->nickname ?? '-' }}</td>
                                        <td>{{ $score->user->email ?? '-' }}</td>
                                        <td>{{ $score->user->mobile ?? '-' }}</td>
                                        {{-- Organization --}}
                                        <td>{{ ($score->user->isOrganizationUser())? $score->user->userToken->token->organization()->withTrashed()->first()->name : '-' }}</td>
                                        {{-- Token/code --}}
                                        <td>{{ $score->user->isOrganizationUser() ? $score->user->userToken->token->token: '-'}}</td>
                                        {{-- Used coupon code --}}
                                        <td>{{ $score->user->getUsedCouponCodes()??'-'}}</td>
                                        {{-- Profile --}}
                                        <td>{{ $score->user->profileType->name ?? '-' }}</td>
                                        {{-- Score fields --}}
                                        <td>{{ $score->result }}</td>
                                        <td>{{ $score->score }}</td>
                                        <td>{{ $score->smoothness }}</td>
                                        <td>{{ $score->liveliness }}</td>
                                        <td>{{ $score->control }}</td>
                                        <td>{{ $score->energy_range }}</td>
                                        <td>{{ $score->clarity }}</td>
                                        <td>{{ $score->crispness }}</td>
                                        <td>{{ $score->speech_rate }}</td>
                                        <td>{{ $score->pause_duration }}</td>
                                        <td>{{ $score->inferred_at }}</td>
                                    </tr>
                                    @endforeach
                        </div>
                        </tbody>
                        </table>
                        <x-pagination-dropdown :paginator="$scores" />
                    </div>
                </div>
            </div>
        </div>
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
