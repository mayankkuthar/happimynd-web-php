<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignPsyToPlan;
use App\Models\HappiguideSession;
use App\Models\Psychologist;
use App\Models\HappiguideNotesForUserByPsy;

use App\Models\User;
use App\Exports\HappibuddyExport;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class HappibuddyController extends Controller
{
    public function allPsychologistListForBuddy(Request $request){
        $psychologist = Psychologist::orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
        $already_mapped_psychologist = AssignPsyToPlan::where('plan_name' , 'Happibuddy')->pluck('psychologist_id')->toArray();
        return view('happibuddy/all_psychologist_list_for_buddy')->with('psychologist',$psychologist)->with('already_mapped_psychologist',$already_mapped_psychologist);
    }



    public function buddyPsyList(Request $request){
        $buddy_psy_ids = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->pluck('psychologist_id')->toArray();
        $psychologist_list = Psychologist::whereIn('id' , $buddy_psy_ids)->orderBy('first_name' , 'asc')->where('deleted_at' , null)->get();
        return view('happibuddy/buddy_mapped_psy_list')->with('psychologist_list',$psychologist_list);
    }


    public function mapPsyWithBuddy(Request $request , $psy_id){
        $data = [
          'plan_name' => 'HappiBuddy',
          'psychologist_id' => $psy_id  
        ];
        AssignPsyToPlan::create($data);
        return back()->with('success' , 'Psychologist map successfully.');
    }


    public function unMapPsyWithBuddy(Request $request , $psy_id){
        AssignPsyToPlan::where('psychologist_id',$psy_id)->where('plan_name' , 'HappiBuddy')->delete();
        return back()->with('success' , 'Psychologist un-map successfully.');
    }


    public function downloadBuddyListxl(Request $request){
        
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        return Excel::download(new HappibuddyExport($data), 'HappiBUDDY ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');

    }



}
