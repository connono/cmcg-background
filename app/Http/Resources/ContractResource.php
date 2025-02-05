<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'id' => $this->id,
            'type' => $this->type,
            'contract_name' => $this->contract_name,
            'complement_code' => $this->complement_code,
            'purchase_type' => $this->purchase_type,
            'department_source' => $this->department_source,
            'isComplement' => $this->isComplement,
            'series_number' => $this->series_number,
            'contractor' => $this->contractor,
            'category' => $this->category,
            'source' => $this->source,
            'price' => $this->price,
            'status' => $this->status,
            'dean_type' => $this->dean_type,
            'law_advice' => $this->law_advice,
            'isImportant' => $this->isImportant,
            'comment' => $this->comment,
            'contract_docx' => $this->contract_docx,
            'contract_file' => $this->contract_file,
            'is_pay' => $this->is_pay,
            'equipment_apply_record_id' => $this->equipment_apply_record_id,
            'created_at' => $this->created_at,
        ];
    }
}
