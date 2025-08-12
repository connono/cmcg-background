<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'platform_id' => $this->platform_id,
            'department' => $this->department,
            'consumable' => $this->consumable,
            'model' => $this->model,
            'price' => $this->price,
            'manufacturer' => $this->manufacturer,
            'start_date' => $this->start_date,
            'exp_date' => $this->exp_date,
            'in_drugstore' => $this->in_drugstore,
            'apply_type' => $this->apply_type,
            'need_selection' => $this->need_selection,
            'sunshine_purchase_file' => $this->sunshine_purchase_file,
            'bid_purchase_file' => $this->bid_purchase_file,
            'registration_num' => $this->registration_num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company' => $this->company,
            'category_zj' => $this->category_zj,
            'parent_directory' => $this->parent_directory,
            'child_directory' => $this->child_directory,
            'apply_date' => $this->apply_date,
            'count_year' => $this->count_year,
            'medical_approval_file' => $this->medical_approval_file,
            'current_state' => new ConsumableStateResource($this->currentState),
            'state_history' => ConsumableStateResource::collection($this->stateEvents)
        ];
    }
} 