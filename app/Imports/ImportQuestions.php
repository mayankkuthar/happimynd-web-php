<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// use Maatwebsite\Excel\Concerns\ToCollection;
use App\Model\Question;

class ImportQuestions implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Question([
            'language'      => $row['language'],
            'question'      => $row['question'],
            'batch_id'      => $row['batch_id'],
            'category_id'   => $row['category_id'],
            'option1'       => $row['option1'],
            'score1'        => $row['score1'],
            'option2'       => $row['option2'],
            'score2'        => $row['score2'],
            'option3'       => $row['option3'],
            'score3'        => $row['score3'],
            'option4'       => $row['option4'],
            'score4'        => $row['score4'],
            'option5'       => $row['option5'],
            'score5'        => $row['score5'],
            
        ]);
    }
}
