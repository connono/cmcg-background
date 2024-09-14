<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumableTrendsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "consumable_apply_id" => $this->consumable_apply_id,
            "platform_id"=> $this->platform_id,
            "department"=> $this->department,
            "consumable"=> $this->consumable,
            "model"=> $this->model,
            "price"=> $this->price,
            "start_date"=> $this->start_date,
            "exp_date"=> $this->exp_date,
            "registration_num" => $this->registration_num,
            "company"=> $this->company,
            "manufacturer"=> $this->manufacturer,
            "category_zj"=> $this->category_zj,
            "parent_directory"=> $this->parent_directory,
            "child_directory"=> $this->child_directory,
            "apply_type"=> $this->apply_type,
            "contract_file"=> $this->contract_file,
            "is_need"=> $this->is_need,   
            "reason"=> $this->reason,  
        ];
    }
}
