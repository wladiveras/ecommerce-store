<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RequestConfigProduct extends FormRequest
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
    public function rules(Request $request)
    {
        $data = $request->all();
        return [
            'sizes' => [
                function ($attribute, $value, $fail) use ($data) {
                    $this->checkSizes($fail, $data);
                }
            ],
        ];
    }

    private function checkSizes($fail, $data)
    {
        $attributes = $data["skus"][0]["sku"]["attributes"];
        if(in_array("M²",$attributes))
        {
            $measures = $data["measures"];
            if(($measures["height"]<0.001)||($measures["width"]<0.001))
            {
                $fail('A altura ou largura do produto não pode ser inferior a 0.001m.');
            }
        }
    }
}
