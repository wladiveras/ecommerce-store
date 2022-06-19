<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class CreateImage extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        Validator::extend('artfile', function ($attribute, $value) {
            $allowed_mimes = ["image/jpg", "image/jpeg", "image/png", "image/vnd.adobe.photoshop", "application/zip", "application/pdf", "application/octet-stream"];

            $mime = $value->getMimeType();
            $file_name = $value->getClientOriginalName();
            $extension = str_replace(".", "", substr($file_name, strlen($file_name) - 3, strlen($file_name)));

            if (in_array($mime, $allowed_mimes) || strstr($mime, 'cdr') || strstr($mime, 'eps') || ($mime == "application/zip" && strstr($file_name, 'cdr'))) {
                return true;
            } else {
                header("Content-Type: application/json");
                die(json_encode(['error' => true, 'message' => "Formato de arquivo inválido"]));
            }
        });

        return [
            'file' => ['artfile'],
        ];
    }

    public function messages()
    {
        return [
            'file.image' => 'Extensão de arquivo não permitida',
            'file.art-file' => 'Extensão de arquivo não permitida'
        ];
    }
}
