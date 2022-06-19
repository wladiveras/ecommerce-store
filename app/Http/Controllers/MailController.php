<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CaptchaRequest;
use App\Mail\{
    ContactMail
};

class MailController extends Controller
{
    public function send(CaptchaRequest $request)
    {
        \Mail::to('ecommerce@padraocolor.com.br')->send(new ContactMail($request->all()));
        return "Seu email foi enviado com sucesso";
    }
}
