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


use App\Models\HappiguideSession;

class HappiguideSessionExport implements FromCollection, WithHeadings, ShouldAutoSize
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

        $records = HappiguideSession::whereBetween('date', [$this->start, $this->end])
                                    ->with('psychologistDetail','userDetail' , 'userOpinion' , 'psychologistOpinion')
                                    ->get();

        $result = array();

        $i =0 ;

        foreach($records as $row){


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
                'Status'=> $row->is_end ? 'Completed' : 'Pending', 

                'Users Feedback Emoji'  => $row->userOpinion->Emoji->name ?? '-',
                'Users Feedback Reason'  => $row->userOpinion->reason ?? '-',

                'Plan to next session / Remarks'  => $row->psychologistOpinion->plan_for_next_session ?? '-',


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
            'Users Feedback Emoji' ,
            'Users Feedback Reason' ,

            'Plan to next session / Remarks' ,

            
       ];
    }



}
