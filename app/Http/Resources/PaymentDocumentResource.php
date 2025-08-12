<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class PaymentDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);
        return [
            "id"=> $this->id,
            "create_date" => $this->create_date,
            "user_name" => is_null($user) ? '': $user->name,
            "department" => $this->department,
            "status" => $this->status,
            "all_price" => $this->all_price,
            "excel_url" => $this->excel_url,
            "payment_document_file" => $this->payment_document_file,
            "reject_reason" => $this->reject_reason,
        ];
    }
}
