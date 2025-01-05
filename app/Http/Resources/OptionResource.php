<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (env('ASSESSMENT_DEBUG')) {
            return [
                'option' => $this->option,
                'id' => $this->pivot['id'],
                'score' => $this->pivot['weightage'],
                'option_id' => $this->id,
                'debugData' => 'optionId =>' . $this->id . ', Score => ' . $this->pivot['weightage'],
            ];
        }
        return [
            'option' => $this->option,
            'id' => $this->pivot['id'],
            'score' => $this->pivot['weightage'],
            'option_id' => $this->id,
        ];
    }
}
