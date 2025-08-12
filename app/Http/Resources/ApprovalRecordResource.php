<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class ApprovalRecordResource extends JsonResource
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
            'user_id' => $user->id,
            'user_name' => $user->name,
            'approve_model_id' => $this->approve_model_id,
            'approve_date' => $this->approve_date,
            'approve_model' => $this->approve_model,
            'approve_status' => $this->approve_status,
            'reject_reason' => $this->reject_reason,
        ];
    }
}                  
