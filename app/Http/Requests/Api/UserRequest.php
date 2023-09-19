<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        switch($this->method()){
            case 'POST':
                return [
                    'name' => 'required|unique:users,name',
                    'phone_number' => 'required|min:7',
                    'department' => 'required',
                ];
                break;
            case 'PATCH':
                return [
                    // 'password' => 'alpha_dash|min:6',
                    // 'phone_number' => 'min:7', 
                ];
                break;
        }

    }
}
