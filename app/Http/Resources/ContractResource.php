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
            'contract_name' => $this->contract_name,
            'isComplement' => $this->isComplement,
            'series_number' => $this->series_number,
            'contractor' => $this->contractor,
            'category' => $this->category,
            'source' => $this->source,
            'price' => $this->price,
            'isImportant' => $this->isImportant,
            'comment' => $this->comment,
            'contract_docx' => $this->contract_docx,
            'contract_file' => $this->contract_file,
        ];
    }
}
