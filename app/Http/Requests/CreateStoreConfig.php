<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoreConfig extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'store_id' => 'required|numeric', 
            'key' => 'required', 
            'value'   => 'required|numeric',       
        ];  
    }
    
    public function messages()
    {
        // return [
        //     'name.required' => "O nome de exibição é obrigatório.",
        //     'formas_pagto' => 'É necessário ter ao menos uma forma de pagamento',
        // ];
    }
}
