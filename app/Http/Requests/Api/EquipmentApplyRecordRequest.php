<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentApplyRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // switch($this->method()){
        //     case 'POST':
        //         return [
        //             'serial_number' => 'required|unique:equipment_apply_records',
        //             'equipment' => 'required',
        //             'department' => 'required',
        //             'count' =>  'required|integer',
        //             'budget' => 'required|integer',
        //             'apply_type' => 'required|in:0,1,2',

        //         ];
        //         break;
        //     case 'PATCH':
        //         return [
        //             'survey_date' => 'date',
        //             'approve_date' => 'date',
        //             'execute_date' => 'date',
        //             'purchase_date' => 'date',
        //             'arrive_date' => 'date',
        //             'price' => 'integer',
        //         ];
        //         break;
        // }
        $rules = [
        ];
        return $rules;
    }
}
