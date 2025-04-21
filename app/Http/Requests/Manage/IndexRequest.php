<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest{

    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'per_page' => 'required|integer',
            'page' => 'required|integer',
        ];
    }
    public function messages()
    {
        return [
            'per_page' => '分页',
            'page' => '每页数',
        ];
    }
}
