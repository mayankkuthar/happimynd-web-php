<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\HappiselfUsersLastVisitSubCourseAndContent;

class HappiSelfExport implements FromCollection, WithHeadings
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
        //
        
        $records = HappiselfUsersLastVisitSubCourseAndContent::with('courseDetails' , 'subCourseDetails' , 'userDetails')->whereBetween('created_at', [$this->start, $this->end])->get();

        $result = array();

        $i =0 ;

        foreach($records as $record){

            if($record->userDetails->isOrganizationUser()) {
                $type = 'B2B';
                $org_name = $record->userDetails->userToken->token->organization()->withTrashed()->first()->name;
            }else{
                $type = 'B2C';  
                $org_name = 'Individual';
            } 



           $result[] = array(
              'id'=> ++$i,
              'user_id' => $record->userDetails->id,
              'username' => $record->userDetails->username,
              'B2B/B2C' => $type,
              'Organization' => $org_name,
              'course_name' => $record->courseDetails->course_name,
              'sub_course_name' => $record->subCourseDetails->sub_course_name,
              'status' => $record->is_complete_happiself_sub_course ? 'compelte' : "Pending"// Custom data
           );
        }

        return collect($result);

    }

    public function headings(): array
    {
       return [
         '#',
         'User ID',
         'Username',
         'B2B/B2C'  ,
         'Organization', 
         'Course Name',
         'Sub Course Name.',
         'Is Sub Course Complete'
       ];
    }


}
