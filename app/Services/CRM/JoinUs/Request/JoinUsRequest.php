<?php

namespace App\Services\CRM\JoinUs\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class JoinUsRequest extends FormRequest
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
            "reseller" => "bail|required|array",
            "reseller.name" => "bail|required",
            "reseller.email" => "bail|required",
            "reseller.business_name" => "bail",
            "reseller.doc" => "bail|required",
            "reseller.phone" => "bail|required",
            "reseller.whatsapp" => "bail|required",
            "reseller.password" => "bail|required|min:6|max:240",
            "reseller.type" => ["bail", "required", "integer", Rule::in(['0', '1'])],

            "address" => "bail|required|array",
            "address.zipcode" => "bail|required",
            "address.state" => "bail|required",
            "address.city" => "bail|required",
            "address.district" => "bail|required",
            "address.street" => "bail|required",
            "address.number" => "bail|required|integer",

            "company.ie" => "bail|required_if:reseller.type,1",
            "company.im" => ["bail", function (...$a) {
                return $this->validateCompanyIM(...$a);
            }],
            "company.no_im" => "bail|required_if:reseller.type,1",

            "person.identity" => "bail"
        ];
    }

    protected function validateCompanyIM($attribute, $value, $fail)
    {
        if (request()['reseller']['type'] == 1 && (!request()['company']['no_im'] && !request()['company']['im']))
            $fail("Por favor preencha a Inscrição Municipal ou marque a isenção da mesma se for o caso.");
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
            "reseller.name" => $this->getFieldName(),
            "reseller.business_name" => "Nome da Revenda",
            "reseller.doc" => $this->getFieldDocName(),
            "reseller.phone" => "Telefone",
            "reseller.whatsapp" => "Celular Whatsapp",
            "reseller.password" => "Senha",

            "company.ie" => "Inscrição Estadual",
            "company.im" => "Inscrição Municipal",
            "company.no_im" => "Isenção da inscrição municipal",

            "person.identity" => "Identidade (RG)",

            "address.zipcode" => "CEP",
            "address.state" => "Estado",
            "address.city" => "Cidade",
            "address.district" => "Bairro",
            "address.street" => "Rua",
            "address.number" => "Número do endereço",
            "address.complement" => "Complemento",
            "address.reference" => "Referência para o local"
        ];
    }

    public function messages()
    {
        return [
            "*.required_if" => "O campo :attribute é obrigatório"
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
