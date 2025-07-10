<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\Organization;
use App\Models\AssignPsyToOrgForTalk;
use App\Models\HappitalkBooking;
use App\Models\HappitalkSession;
use App\Models\AssignPsyToPlan;
use App\Models\HappitalkTax;
use App\Models\HappitalkPenaltyClause;
use App\Models\HappitalkSessionOpinionPsychologist;
use App\Models\HappitalkSessionOpinionUser;
use App\Models\HappitalkNotesForUserByPsy;
use App\Exports\HappitalkSessionExport;
use App\Models\User;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;



class HappitalkController extends Controller
{

    public function allPsychologistListForTalk(Request $request){
        $psychologist = Psychologist::orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
        $already_mapped_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiTalk')->pluck('psychologist_id')->toArray();
        return view('happitalk/all_psychologist_list_for_talk')->with('psychologist',$psychologist)->with('already_mapped_psychologist',$already_mapped_psychologist);
    }

    public function happitalkPsychologistList(Request $request){
        $talk_psy_ids = AssignPsyToPlan::where('plan_name' , 'HappiTalk')->pluck('psychologist_id')->toArray();
        $psychologist_list = Psychologist::whereIn('id' , $talk_psy_ids)->orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
        return view('happitalk/talk_mapped_psy_list')->with('psychologist_list',$psychologist_list);
    }
    
    public function mapPsyWithTalk(Request $request , $psy_id){
        $data = [
          'plan_name' => 'HappiTalk',
          'psychologist_id' => $psy_id  
        ];
        AssignPsyToPlan::create($data);
        return back()->with('success' , 'Psychologist map successfully.');
    }

    public function unMapPsyWithTalk(Request $request , $psy_id){
        AssignPsyToPlan::where('psychologist_id',$psy_id)->where('plan_name' , 'HappiTalk')->delete();
        return back()->with('success' , 'Psychologist un-map successfully.');
    }


    public function allOrgListForHappitalk(Request $request){
        $organization_list = Organization::orderBy('name' , 'asc')->get();
        return view('happitalk/all_org_list_talk')->with('organization_list',$organization_list);
    }

    public function assignPsyToOrg(Request $request , $org_id){
        if($request->isMethod('GET')){
            $org_details = Organization::where('id' , $org_id)->first();
            $psychologist_list = Psychologist::whereNotNull('slot1')->orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
            $maped_pay_list = AssignPsyToOrgForTalk::where('organization_id' , $org_id)->with('psychologist')->orderBy('id','desc')->get();
            $all_mapped_psy_ids_in_array = AssignPsyToOrgForTalk::where('organization_id' , $org_id)->pluck('psychologist_id')->toArray();
            return view('happitalk/assign-psy-to-org')
                    ->with('org_details',$org_details)
                    ->with('psychologist_list' , $psychologist_list)
                    ->with('all_mapped_psy_ids_in_array',$all_mapped_psy_ids_in_array)
                    ->with('maped_pay_list',$maped_pay_list);
        }

        if($request->isMethod('POST')){
            AssignPsyToOrgForTalk::where('organization_id',$org_id)->delete();
            $psy_ids_to_map_with_for_for_talk =  $request->psychologist_id;
            foreach($psy_ids_to_map_with_for_for_talk as $row){
                $data = [
                    'organization_id' => $org_id,
                    'psychologist_id' => $row,
                ];
                AssignPsyToOrgForTalk::create($data);
            }
            return back()->with('success' , 'Psychologist map successfully');
        }
    }

    
    public function unMapPsyToOrg(Request $request , $id){
        AssignPsyToOrgForTalk::where('id' , $id)->delete();
        return back()->with('success' , 'Psychologist un-map successfully');
    }


    public function happitalkBookingList(Request $request){
        // $booking = HappitalkBooking::orderBy('id','desc')->with('psychologist','user')->get();


        $query = $request->get('query');
        $perPage = $request->get('per_page', 10);

        if($query){
            $user_ids = User::where('username' , $query)->pluck('id');

            $booking = HappitalkSession::whereIn('user_id' , $user_ids)->orderBy('id','desc')
                                    ->with('psychologistDetail','userDetail' , 'userOpinion' , 'psychologistOpinion')
                                    ->paginate($perPage)
                                    ->appends($request->except('page'));
        }else{
            $booking = HappitalkSession::orderBy('id','desc')->whereHas('userDetail')
                                    ->with('psychologistDetail','userDetail' , 'userOpinion' , 'psychologistOpinion')
                                    ->paginate($perPage)
                                    ->appends($request->except('page'));
        }


        
        return view('happitalk/booking_list')->with('booking',$booking);
    }


    public function happitalkBookingListByUsername(Request $request)
    {
        if($request->isMethod('GET')){
            return view('happitalk/booking_list_by_username');
        }
        if($request->isMethod('POST')){
             
            return redirect('admin/happitalk-booking-list?query='.$request->username);
        }
    }


    public function sessionListBasedOnBookingId(Request $request , $booking_id){
        $sessions = HappitalkSession::where('happitalk_booking_id' , $booking_id)->get();
        return view('happitalk/session_list')->with('sessions',$sessions);
    }



    public function talkTds(Request $request){
        if($request->isMethod('GET')){
            $tds_Detail = HappitalkTax::first();
            return view('happitalk/talk_tds')->with('tds_Detail',$tds_Detail);
        }
        if($request->isMethod('POST')){
            $message = [
              'tds_percentage.required' => 'TDS percentage is required.',
              'tds_percentage.min' => 'TDS percentage must be greater than 1.',
              'tds_percentage.max' => 'TDS percentage must be less than 100.',
            ];
            $request->validate([
                  'tds_percentage' => 'required|gt:1|lt:100',
            ],$message);

          
            $details = HappitalkTax::first();
            $details->tds_percentage = $request->tds_percentage;
            $details->save();

            return back()->with('success' , 'TDS updated successfully.');
        }
    }

 


    public function penaltyClause(Request $request){
        if($request->isMethod('GET')){
            $penalty_details = HappitalkPenaltyClause::first();
            return view('happitalk/talk_penalty')->with('penalty_details',$penalty_details);
        }
        if($request->isMethod('POST')){
           

            $data = [
                'for_b2b_user_for_one_credit' => $request->for_b2b_user_for_one_credit,
                'for_b2b_user_for_half_credit' => $request->for_b2b_user_for_half_credit,

                'for_b2c_user_for_one_credit' => $request->for_b2c_user_for_one_credit,
                'for_b2c_user_for_half_credit' => $request->for_b2c_user_for_half_credit,

            ];

            $penalty_details = HappitalkPenaltyClause::first();
            if(!$penalty_details){
                $details = HappitalkPenaltyClause::create($data);
            }else{
                HappitalkPenaltyClause::where('id' , $penalty_details->id)->update($data);
            }

            return back()->with('success' , 'Penalty details update successfully.');
        }
    }


    public function talkNotesDetail(Request $request , $id)
    {
        $psy_opinion = HappitalkSessionOpinionPsychologist::where('happitalk_session_id' , $id)->get();
        $user_opinion = HappitalkSessionOpinionUser::where('happitalk_session_id' , $id)->with('Emoji')->get();
        $psy_notes_for_user = HappitalkNotesForUserByPsy::where('session_id' , $id)->get();

        return view('happitalk/session_notes_list')
        ->with('psy_opinion',$psy_opinion)
        ->with('user_opinion',$user_opinion)
        ->with('psy_notes_for_user',$psy_notes_for_user);

    }




    public function downloadtalkListxl(Request $request)
    {
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }
        // $users = User::latest()->with('profileType' , 'usersRating')->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        return Excel::download(new HappitalkSessionExport($data), 'HappiTALK Sessions ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');

    }



    public function usersCredit(Request $request){
        if($request->isMethod('GET')){

            $query = $request->get('query');

            if($query){
                $user_detail = User::where('username' , $query)->first();
                $list = HappitalkBooking::where('user_id' ,  $user_detail->id)->with('psychologist' , 'user')->get();
            }else{
                $list = [];
            }

            return view('happitalk/users_credit')->with('list' , $list);
        }
        if($request->isMethod('POST')){

             return redirect('admin/users-credit?query='.$request->username);

        }
    }



    public function editUsersCredit(Request $request , $bookng_id){
        

        if($request->isMethod('GET')){

            $booking_Details = HappitalkBooking::where('id' , $bookng_id)->first();

            return view('happitalk/edit_users_credit')->with('booking_Details' , $booking_Details);
        }

        if($request->isMethod('POST')){
            $data = [
                'remaining_session' => $request->remaining_session,
                'notes' => $request->notes ?? null,
            ];

            HappitalkBooking::where('id' , $bookng_id)->update($data);

            $booking_Details = HappitalkBooking::where('id' , $bookng_id)->with('user')->first();


            return redirect('admin/users-credit?query='.$booking_Details->user->username);

        }



    }




}





