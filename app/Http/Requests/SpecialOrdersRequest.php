<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StatusSpecialOrder;
use Auth;

class SpecialOrdersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'        => 'required',
            'description'  => 'required',
            'target_date'  => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $data = $this->request->all();

        $this->merge([
            'description' => $data["description"],
            'user_id'     => Auth::user()->id,
            'status_id'   => @$data["status_id"] ? $data["status_id"] : StatusSpecialOrder::where("value", "opened")->firstOrFail()->id
        ]);
    }
}
