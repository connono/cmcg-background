<?php

namespace App\Http\Resources;

use App\Models\EquipmentApplyRecord;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentApplyRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $contract = EquipmentApplyRecord::find($this->id)->contract()->first();
        $contract_id = $contract ? $contract->id : null;

        return [
            "id"=> $this->id,
            "serial_number" => $this->serial_number,
            "status"=> $this->status,
            "equipment"=> $this->equipment,
            "department"=> $this->department,
            "count"=> $this->count,
            "budget"=> $this->budget,
            "apply_type"=> $this->apply_type,
            "survey_record"=> $this->survey_record,
            "meeting_record"=> $this->meeting_record,
            "survey_date" => $this->survey_date,
            "purchase_type"=> $this->purchase_type,
            "survey_picture"=> $this->survey_picture,
            "approve_date"=> $this->approve_date,
            "execute_date"=> $this->execute_date,
            "approve_picture"=> $this->approve_picture,
            "tender_date"=> $this->tender_date,
            "tender_file"=> $this->tender_file,
            "tender_boardcast_file"=> $this->tender_boardcast_file,
            "tender_out_date"=> $this->tender_out_date,
            "bid_winning_file"=> $this->bid_winning_file,
            "send_tender_file"=> $this->send_tender_file,
            "purchase_date"=> $this->purchase_date,
            "arrive_date"=> $this->arrive_date,
            "price"=> $this->price,
            "purchase_picture"=> $this->purchase_picture,
            "install_date"=> $this->install_date,
            "install_picture"=> $this->install_picture,
            "isAdvance"=> $this->isAdvance,
            "advance_status"=> $this->advance_status,
            "advance_record_id"=> $this->advance_record_id,
            "warehousing_date"=> $this->warehousing_date,
            "contract_id"=> $contract_id,
        ];
    }
}
