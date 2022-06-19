<?php

namespace App\Services\CRM\JoinUs\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class FinishRequest extends FormRequest
{
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
            "clients.*.name" => "bail|required",
            "clients.*.lastName" => "bail|required",
            "clients.*.phone" => "bail|required",

            "files.doc" => "bail|required",

            "files.ie" => "bail|required_if:reseller.type,1",
            "files.im" => ["bail", function (...$a) {
                return $this->validateCompanyIM(...$a);
            }],

            "files.identity" => "bail|required_if:reseller.type,0"
        ];
    }

    protected function validateCompanyIM($attribute, $value, $fail)
    {
        if (!request()['company']['no_im'] && !request()['company']['im'])
            $fail("Por favor, envie a Inscrição Municipal");
    }

    protected function failedValidation(Validator $validator)
    {
        $messages = $validator->getMessageBag()->getMessages();

        $message = array_flatten($messages)[0];
        abort(400, $message);
    }

    public function attributes()
    {
        return [
            "files.doc" => "Arquivo de ".$this->getFieldDocName(),
            "files.im" => "Arquivo da Inscrição Municipal",
            "files.ie" => "Arquivo da Inscrição Estadual",
            "files.identity" => "Arquivo da Identidade",
            "clients.*.name" => "Nome do cliente",
            "clients.*.lastName" => "Sobrenome do cliente",
            "clients.*.phone" => "Telefone do cliente",
        ];
    }

    public function messages()
    {
        return [
            "*.required_if" => "O campo :attribute é obrigatório",
            "*.required_if" => "O :attribute é obrigatório"
        ];
    }


    private function getFieldDocName()
    {
        return request()['reseller']['type'] ? "CNPJ" : "CPF";
    }


    private function getFieldName()
    {
        return request()['reseller']['type'] ? "Razão Social" : "Nome completo";
    }
}
