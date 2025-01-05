<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportHappiselfLibraryContent implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        return new data([
            'library_id'        => $row['library_id'],
            'content_type'      => $row['content_type'],
            'content'           => $row['content'],
        ]);
    }
}
