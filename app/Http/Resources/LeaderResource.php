<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $departments = $this->departments()->get();
        $departmentString = '';
        foreach ($departments as $department) {
            $departmentString .= $department->label .'&';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'departments' => $departmentString,
        ];
    }
}
