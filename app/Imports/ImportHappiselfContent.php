<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportHappiselfContent implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    
    public function model(array $row)
    {
        // return new data([
        //     'course_id'             => $row['course_id'],
        //     'sub_course_id'         => $row['sub_course_id'],
        //     'content_type'          => $row['content_type'],
        //     'content'               => $row['content'],
        //     'media'                 => $row['media'],
        //     'is_media_downloadable' => $row['is_media_downloadable'],
        //     'correct_answer'        => $row['correct_answer'],
        //     'option1'               => $row['option1'],
        //     'option2'               => $row['option2'],
        //     'option3'               => $row['option3'],
        //     'option4'               => $row['option4'], 
        //     'option5'               => $row['option5'], 
        //     'option6'               => $row['option6'], 
        //     'option7'               => $row['option7'], 
        // ]);
    }

}
