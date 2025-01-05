<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->resource->load('category', 'batchCategory.batch.userProfile'));
        if (env('ASSESSMENT_DEBUG')) {
            $this->resource->load('category', 'batchCategory.batch.userProfile');
            return [
                'id' => $this->id,
                'question' => $this->question,
                'debugData' => "Q.No =>" . $this->id . ", Category: " . $this->resource->category->name . " => " . $this->resource->category->acronymn . "(" . $this->resource->category->id . ") , Batch: " . $this->resource->category->batchCategory->batch->name . ", Profile: " . $this->resource->category->batchCategory->batch->name,
                'options' => OptionResource::collection($this->option)
            ];
        }
        return [
            'id' => $this->id,
            'question' => $this->question,
            'category' => $this->resource->load('category')->category->name . ' => ' . $this->resource->category->acronymn,
            'options' => OptionResource::collection($this->option)
        ];
    }
}
