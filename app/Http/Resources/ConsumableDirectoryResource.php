<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ConsumableApplyTable;

class ConsumableDirectoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $consumable_apply_record = ConsumableApplyTable::where('serial_number', $this->consumable_apply_id)->first();
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
            "in_drugstore"=> $this->in_drugstore,
            "status"=> $this->status,   
            "stop_reason"=> $this->stop_reason,  
            "stop_date"=> $this->stop_date,
            "apply_file" => $consumable_apply_record->apply_file,
        ];
    }
}
