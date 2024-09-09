<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PaymentDocument;

class PaymentProcessRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $payment_document = PaymentDocument::find($this->payment_document_id);
        $payment_document_file = $payment_document ? $payment_document->payment_document_file : '';
        return [
            'id' => $this->id,
            'contract_name' => $this->contract_name,
            'department' => $this->department,
            'company' => $this->company,
            'assessment' => $this->assessment,
            'payment_voucher_file' => $this->payment_voucher_file,
            'assessment_date' => $this->assessment_date,
            'payment_file' => $this->payment_file,
            'payment_process_id' => $this->payment_process_id,
            'payment_document_file' => $payment_document_file,
            'payment_document_id' => $this->payment_document_id,
            'created_at' => $this->created_at,
        ];
    }
}
