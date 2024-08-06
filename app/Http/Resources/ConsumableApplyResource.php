<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumableApplyResource extends JsonResource
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
            "serial_number" => $this->serial_number,
            "platform_id"=> $this->platform_id,
            "department"=> $this->department,
            "consumable"=> $this->consumable,
            "model"=> $this->model,
            "price"=> $this->price,
            "apply_date"=> $this->apply_date,
            "count_year"=> $this->count_year,
            "registration_num" => $this->registration_num,
            "company"=> $this->company,
            "manufacturer"=> $this->manufacturer,
            "category_zj"=> $this->category_zj,
            "parent_directory"=> $this->parent_directory,
            "child_directory"=> $this->child_directory,
            "apply_type"=> $this->apply_type,
            "pre_assessment"=> $this->pre_assessment,
            "final"=> $this->final,
            "apply_file"=> $this->apply_file,
            "in_drugstore"=> $this->in_drugstore,
            "status"=> $this->status,   
        ];
    }
}
