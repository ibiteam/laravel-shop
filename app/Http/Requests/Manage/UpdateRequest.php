<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest{

    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'id' => 'required|integer',
        ];
    }
    public function messages()
    {
        return [
            'id' => 'ID',
        ];
    }
}
