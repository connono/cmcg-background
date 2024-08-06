<?php

namespace App\Http\Resources;

use App\Models\ConsumableTemporaryApply;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsumableTemporaryApplyRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $consumableTemporary_apply_record = ConsumableTemporaryApply::find($this->id);
       // $contract = $equipment_apply_record ? $equipment_apply_record->contract()->first() : null;
       // $contract_id = $contract ? $contract->id : null;

        return [
            "id"=> $this->id,
            "serial_number" => $this->serial_number,
            "status"=> $this->status,
            "department"=> $this->department,
            "consumable"=> $this->consumable,
            "count"=> $this->count,
            "budget"=> $this->budget,
            "model"=> $this->model,
            "manufacturer"=> $this->manufacturer,
            "telephone"=> $this->telephone,
            "registration_num" => $this->registration_num,
            "reason"=> $this->reason,
            "apply_date"=> $this->apply_date,
            "apply_type"=> $this->apply_type,
            "apply_file"=> $this->apply_file,
            "product_id"=> $this->product_id,
            "arrive_date"=> $this->arrive_date,
            "arrive_price"=> $this->arrive_price,
            "company"=> $this->company,
            "telephone2"=> $this->telephone2,
            "accept_file"=> $this->accept_file,
            "stop_reason"=> $this->stop_reason,
           
        ];
    }
}
