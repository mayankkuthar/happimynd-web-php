<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PsychoLogistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $t = $this->getPsychologistPlans();
        foreach ($t as $plan) {
            $plan->cost_price = $plan->getCostPrice();
            $plan->session_selling_price = $plan->getPerSessionSellingPrice();
            $plan->print_duration = $plan->printDuration();
        }
        return [
            'id' => $this->id,
            'city' => $this->city,
            'custom_price' => $this->custom_price,
            'expert_level' => $this->expertLevel,
            'expert_level_id' => $this->expert_level_id,
            'languages' => $this->printLanguages(),
            'plans' => $t,
            'full_name' => $this->full_name,
            'profile_picture_url' => $this->s3ImageUrl,
            'slot1' => $this->slot1,
            'slot2' => $this->slot2,
            'specialization' => $this->printSpecializations(),
            'summary' => $this->summary,
            'minimum_session_price' => $this->getMinimumSessionPrice(),

        ];
    }
}
