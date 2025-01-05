<?php

namespace App\Exports;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


use App\Models\HappitalkSession;


class HappitalkSessionExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $start;
    protected $end;
    public function __construct($data)
    {
        if (isset($data['start_date'])) {
            $this->start = $data['start_date'];
            $this->end = $data['end_date'];
        } 
    }


    public function collection()
    {

        $records = HappitalkSession::whereBetween('date', [$this->start, $this->end])
                                    ->with('psychologistDetail','userDetail' , 'userOpinion')
                                    ->get();

        $result = array();

        $i =0 ;

        foreach($records as $row){


            if($row->is_cancel == 1){
                $status = 'Session cancel '.$row->cancel_by;
            }
            elseif($row->is_req_accepted == 0){
                $status = 'Request pending by psychologist';
            }
            elseif($row->is_req_accepted == 2){
                $status = 'Request rejected by psychologist';
            }
            elseif($row->is_req_accepted == 1){
              if($row->is_cancel == 1){
                $status = 'Session cancel by '.$row->cancel_by;
              }
              elseif($row->is_cancel == 0 && $row->is_end == 0){
                $status = 'Request accepted by psychologist';
              }
              elseif($row->is_cancel == 0 && $row->is_end == 1){
                  $status = 'Session completed';
              } 
            }



            if($row->userDetail->isOrganizationUser()) {
                $type = 'B2B';
                $org_name = $row->userDetail->userToken->token->organization()->withTrashed()->first()->name;
            }else{
                $type = 'B2C';  
                $org_name = 'Individual';
            } 

          

           $result[] = array(
                'id'=> ++$i,
                'Psychologist Name' => $row->psychologistDetail->first_name. ' '. $row->psychologistDetail->last__name ,
                'User Name' => $row->userDetail->username,
                'B2B/B2C' => $type,
                'Organization' => $org_name,
                'Date'  =>  $row->date,
                'Time' =>  $row->time,
                'Status'=> $status, 

                'Psy joined Time' => $row->psy_joined_time ?? '-',
                'Psy leave Time'  => $row->psy_leave_time ?? '-',

                'Users Feedback Emoji'  => $row->userOpinion->Emoji->name ?? '-',
                'Users Feedback Reason'  => $row->userOpinion->reason ?? '-',

                'Recording Permission' => $row->user_recording_permission == "0" ? 'No' : 'Yes',

           );
        }



        return collect($result);

    }




    public function headings(): array
    {
       return [
            '#', 
            'Psychologist Name' ,
            'User Name' ,
            'B2B/B2C'  ,
            'Organization', 
            'Date'  ,
            'Time' ,
            'Status', 
            'Psy joined Time',
            'Psy leave Time' ,
            'Users Feedback Emoji' ,
            'Users Feedback Reason' ,
            'Recording Permission',
       ];
    }



}
