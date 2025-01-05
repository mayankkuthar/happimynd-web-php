<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportHappiLearnContent implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Question([
            'language'      => $row['language'],
            'type'          => $row['type'],
            'title'         => $row['title'],
            'profile'       => $row['profile'],
            'parameters'    => $row['parameters'],
            'keywords'      => $row['keywords'],
            'summary'       => $row['summary'],
            'link'          => $row['link'],
            'credit'        => $row['credit'],
            'thumbnail'     => $row['thumbnail'],
        ]);
    }
    
}


