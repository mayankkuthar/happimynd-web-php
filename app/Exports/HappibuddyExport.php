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

use App\Models\GroupChat;
use DB;

class HappibuddyExport implements FromCollection, WithHeadings, ShouldAutoSize
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

        $records = GroupChat::whereBetween('created_at', [$this->start, $this->end])
                ->select('user_id', DB::raw('MAX(id) as id') )
                ->groupBy('user_id')
                ->orderBy('id' , 'desc')
                ->with('user')
                ->get();

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
                'User Name' => $row->user->username,
                'Email' => $row->user->email,
                'Gender' => ucfirst($row->user->gender  ?? ''),
                'B2B/B2C' => $type,
                'Organization' => $org_name,
                 
           );


        }

        return collect($result);

    }



    public function headings(): array
    {
       return [
            '#', 
            "Username",
            "Email",
            "Gender",
            "B2B/B2C",
            "Organization"
       ];
    }





}






