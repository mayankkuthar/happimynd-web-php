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

use App\Models\Feedback;

class FeedbackExport implements FromCollection, WithHeadings, ShouldAutoSize
{
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
        //
        
        $records = Feedback::orderBy('id' , 'desc')->with('user' , 'applicationRateEmoji')->whereBetween('created_at', [$this->start, $this->end])->get();

        $result = array();

        $i =0 ;

        foreach($records as $row){

            if($row->user->isOrganizationUser()) {
                $type = 'B2B';
                $org_name = $row->user->userToken->token->organization()->withTrashed()->first()->name;
            }else{
                $type = 'B2C';  
                $org_name = 'Individual';
            } 

           $result[] = array(
              'id'=> ++$i,
              'username' => $row->user->username ?? '-' ,
              'username' => $row->user->username ?? '-' ,
              'b2b/b2c' => $type,
              'organization' => $org_name,
              'emoji_name' => $row->applicationRateEmoji->name ?? '-',
              'feedback_message' => $row->feedback_message ?? '-',
              'date' => $row->created_at ?? '-',

           );
        }



        return collect($result);

    }

    public function headings(): array
    {
       return [
         '#',
         'Username',
         'B2B/B2C',
         'Organization',
         'Emoji Name',
         'Additional Message',
         'Date',
       ];
    }

}
