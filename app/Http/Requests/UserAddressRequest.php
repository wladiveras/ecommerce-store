<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAddressRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
          'city' => 'bail|required',
          //'complement' => 'bail|nullable|required',
          'district' => 'bail',
          'name' => 'bail|required',
          'number' => 'bail|required',
          'client_doc' => [function($attr,$doc,$fail){
              $user = \Auth::user();
              if($user->reseller->doc == $doc && request('type') == 'client'){
                  $fail("Você não pode cadastrar seu CPF/CNPJ para Envio para Cliente");
              }
          }],
          'state' => 'bail|required',
          'zip_code' => 'bail|required'
        ];
    }
}
