<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AssignPsyToPlan;
use App\Models\HappiguideSession;
use App\Models\Psychologist;
use App\Models\HappiguideNotesForUserByPsy;

use App\Models\HappiguideSessionOpinionPsychologist;
use App\Models\HappiguideSessionOpinionUser;


use App\Models\User;
use Http;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\HappiguideSessionExport;




class HappiguideController extends Controller
{

    public function allPsychologistListForGuide(Request $request){
        $psychologist = Psychologist::orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
        $already_mapped_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->pluck('psychologist_id')->toArray();
        return view('happiguide/all_psychologist_list_for_guide')->with('psychologist',$psychologist)->with('already_mapped_psychologist',$already_mapped_psychologist);
    }

    public function happiguidePsychologistList(Request $request){
        $guide_psy_ids = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->pluck('psychologist_id')->toArray();
        $psychologist_list = Psychologist::whereIn('id' , $guide_psy_ids)->orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
        return view('happiguide/guide_mapped_psy_list')->with('psychologist_list',$psychologist_list);
    }

    public function mapPsyWithGuide(Request $request , $psy_id){
        $data = [
          'plan_name' => 'HappiGuide',
          'psychologist_id' => $psy_id  
        ];
        AssignPsyToPlan::create($data);
        return back()->with('success' , 'Psychologist map successfully.');
    }

    public function unMapPsyWithGuide(Request $request , $psy_id){
        AssignPsyToPlan::where('psychologist_id',$psy_id)->where('plan_name' , 'HappiGuide')->delete();
        return back()->with('success' , 'Psychologist un-map successfully.');
    }

    public function happiguideSessionList(Request $request){

        $query = $request->get('query');

        if($query){
            $user_ids = User::where('username' , $query)->pluck('id');
            $guide_session = HappiguideSession::whereHas('userDetail')->whereIn('user_id' , $user_ids)->orderBy('id' , 'desc')
                                            ->with('userDetail','psychologistDetail' , 'userOpinion' , 'psychologistOpinion')
                                            ->paginate('10');
        }else{
            $guide_session = HappiguideSession::whereHas('userDetail')->orderBy('id' , 'desc')
                                            ->with('userDetail','psychologistDetail' , 'userOpinion' , 'psychologistOpinion')
                                            ->paginate('10');
        }

        
        
        return view('happiguide/session_list')->with('guide_session',$guide_session);
    }



    public function happiguideSessionListByUsername(Request $request)
    {
        if($request->isMethod('GET')){
            return view('happiguide/session_list_by_username');
        }
        if($request->isMethod('POST')){
            return redirect('admin/happiguide-session-list?query='.$request->username);
        }
    }



    public function changeGuideSessionPsyList(Request $request, $guide_session_id){
        if($request->isMethod('GET')){
            $guide_psy_ids = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->pluck('psychologist_id')->toArray();
            $psychologist = Psychologist::whereIn('id' , $guide_psy_ids)->orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
            return view('happiguide/change_psy')->with('guide_session_id',$guide_session_id)->with('psychologist',$psychologist);
        }
    }


    public function actionSwitchPsy(Request $request, $guide_session_id, $psy_id){
        $psy_details = Psychologist::where('id' , $psy_id)->first();
        $message = "Admin allocate a new HappiGuide session to you.";
        $device_token = $psy_details->device_token;
        if($device_token){
            $this->sendNotification($device_token,$message);
        }
        HappiguideSession::where('id' , $guide_session_id)->update(["psychologist_id" => $psy_id]);
        return redirect('admin/happiguide-session-list')->with('success' , 'Psychologist has been changed successfully.');
    }

    public function sendNotification($deviceToken , $message){
        $apiURL = 'https://exp.host/--/api/v2/push/send';
        $postInput = [
            'to' => $deviceToken,
            'title' => 'HappiMynd',
            'body' => $message,
        ];
        $headers = [
            'Content-Type: application/json'
        ];
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        // dd($responseBody);
        return $responseBody;
    }


    public function happiguideGetNotesDate(Request $request){
        return view('happiguide/dates_for_notes');
    }


    public function happiguideNotesBasedOnDates(Request $request){
        $start_date =  $request->start_date;
        $end_date =  $request->end_date;

        if($start_date== null ||  $end_date== null){
            return back()->with('error' , 'Please select start and end date.');
        }

        $guide_seeion_bt_these_dates = HappiguideSession::whereBetween('date' , [$start_date , $end_date])->pluck('id')->toArray();

        $guide_notes = HappiguideNotesForUserByPsy::whereIn('guide_session_id' , $guide_seeion_bt_these_dates )->With('guideSessionDetail')->get();

        return view('happiguide/guide_notes_list')->with('guide_notes' , $guide_notes);

    }



    public function guideOpinionDetail(Request $request , $id){
        $psy_opinion = HappiguideSessionOpinionPsychologist::where('happiguide_session_id' , $id)->get();
        $user_opinion = HappiguideSessionOpinionUser::where('happiguide_session_id' , $id)->with('Emoji')->get();

        return view('happiguide/session_notes_list')
        ->with('psy_opinion',$psy_opinion)
        ->with('user_opinion',$user_opinion);
    }




    public function downloadGuideListxl(Request $request)
    {
         
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        return Excel::download(new HappiguideSessionExport($data), 'HappiGUIDE Sessions ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');

    }



}












