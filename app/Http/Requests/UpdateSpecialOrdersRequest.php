<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StatusSpecialOrder;

class UpdateSpecialOrdersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'new_status'   => 'required'
        ];
    }

    public function prepareForValidation()
    {
        $data = $this->request->all();

        $this->merge([
            'created_at' => date("Y-m-d H:i:s"),
            'status'     => StatusSpecialOrder::select("id", "value", "name")->findOrFail($data["new_status"])->toArray()
        ]);
    }
}
