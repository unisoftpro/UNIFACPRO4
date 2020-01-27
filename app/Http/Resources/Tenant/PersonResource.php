<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'identity_document_type_id' => $this->identity_document_type_id,
            'number' => $this->number,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'country_id' => $this->country_id,
            'department_id' => $this->department_id,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'perception_agent' => (bool) $this->perception_agent,
            'percentage_perception' => $this->percentage_perception,
            'state' => $this->state,
            'condition' => $this->condition,
            'person_type_id' => $this->person_type_id,
            'comment' => $this->comment,

            // 'more_address' =>  $this->more_address,
        ];
    }
}