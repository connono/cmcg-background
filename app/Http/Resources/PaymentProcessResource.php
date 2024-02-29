<?php

namespace App\Http\Resources;

use App\Models\Contract;
use App\Models\EquipmentApplyRecord;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentProcessResource extends JsonResource
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
            "contract_name"=> $this->contract_name,
            "department"=> $this->department,
            "company"=> $this->company,
            "payment_file"=> $this->payment_file,
            "next_date"=> $this->next_date,
            "contract_date"=> $this->contract_date,
            "assessment"=> $this->assessment,
            "target_amount"=> $this->target_amount,
            "is_pay"=> $this->is_pay,
            "category"=> $this->category,
            "records_count"=> $this->records_count,
            "assessments_count"=> $this->assessments_count,
            "status"=> $this->status,
            "current_payment_record_id"=> $this->current_payment_record_id,
            "contract_id"=> $this->contract_id,
            'warehousing_date' => EquipmentApplyRecord::find(Contract::find($this->contract_id)->equipment_apply_record_id)->warehousing_date,
            'install_picture' => EquipmentApplyRecord::find(Contract::find($this->contract_id)->equipment_apply_record_id)->install_picture,
        ];
    }
}
