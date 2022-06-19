<?php

namespace App\Http\Controllers\PadraoColor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\FinishRegisterMail;
use GuzzleHttp\Client;
use App\Services\CRM\JoinUs\Request\{JoinUsRequest, FinishRequest};
use App\Services\CRM\JoinUs\Resource\{NewUserResource, RegisterResource};
use App\User;
use Auth;
use App\Http\Controllers\Auth\LoginController as loginController;
use Illuminate\Support\Facades\Log;


class JoinUsFormController extends Controller
{
    private $formCrmSubmitUrl = "https://crm.otimize.me/padrao/new-register";
    private $formFinishUrl = "https://crm.otimize.me/padrao/get-register";
    private $formRecoverUrl = "https://crm.otimize.me/formrecover/E0BpYNR";

    //Passa numa função para confirmar o env
    private $newUserPath = "resellers/";

    public function index(Request $request)
    {
        $data = $request->all();
        return view("pages.padraocolor.join-us.form", compact("data"));
    }

    private function sendRequest($url, $data, $method = "POST", $headers = [])
    {
        $client = new Client();
        $data = [
            "json" => $data,
            "headers" => $headers
        ];

        try {

            $response = $client->request($method, $url, $data);

            $result = json_decode($response->getBody(), true);

            return $result;
        } catch (\Exception $e) {
            return [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }
    }

    public function saveProgress(Request $request)
    {
        $data = [
            'origin' => "padraocolor_" . config('app.env'),
            'hash' => $request['id'],
            'doc' => $request['reseller']['doc'],
            'name' => $request['reseller']['name'],
            'email' => $request['reseller']['email'],
            'form_data' => $request->except('id')
        ];

        $this->sendRequest($this->formRecoverUrl, $data);
    }

    private function sendToCrm($request)
    {
        $url = $this->formCrmSubmitUrl;
        $params = RegisterResource::toArray($request->all());
        $method = "POST";

        $send = $this->sendRequest($url, $params, $method);
        \Log::debug("resposta do send", [$send]);
        $id = $send['id'];

        \Mail::to($request['reseller']['email'])->send(new FinishRegisterMail($request->all(), $id));
    }

    private function createUser($request)
    {
        $url = $this->getNewUserUrl();
        $data = $request->all();
        $params = NewUserResource::toArray($data);
        $method = "POST";
        $headers = [
            //"Authorization" => "Basic Z2FsYXhzdXBvcnQ6MTUzNDY4"
            "X-User-Email" => "contato@galaxsuport.com.br",
            "X-User-Token" => "z5QXHs9GkXsJ1DKRgTCy"
        ];
        \Log::debug("payload", [$params]);
        $response = $this->sendRequest($url, $params, $method, $headers);
        if (isset($response['success']) && $response['success'] == false) abort(400, "{$response['message']}");
        \Log::debug("New user", $response);
        //@$response[0]["provider"] = @$data["reseller"]["provider"] ? $data["reseller"]["provider"] : null;
        //@$response[0]["provider_id"] = @$data["reseller"]["provider_id"] ? $data["reseller"]["provider_id"] : null;
        return $this->sendUserToDashboard($response);
    }

    public function getNewUserUrl()
    {
        return config("systemcolor.url") . $this->newUserPath;
    }

    private function sendUserToDashboard($data)
    {
        $json = [
            "key" => "importacaowebhook",
            "command" => "user-update",
            "data" => $data
        ];

        $url = config("ecommerce.dashboard_url") . "/api/systemcolor";

        $response = $this->sendRequest($url, $json);

        \Log::debug("cata o json", [$json]);
        \Log::debug("cata o data", [$data]);

        \Log::debug("Attempt to send to dashboard", (array) $response);
        return $response;
    }

    private function socialLoginFish($data)
    {
        $provider_id = @$data["users"][0]["provider_id"];
        $username = @$data["users"][0]["user_name"];
        if ($provider_id) {
            if ($username) {
                if ($user = User::where("user_name", $username)->first()) {
                    Auth::loginUsingId($user->id, true);
                }
            }
        }
    }

    public function store(JoinUsRequest $request)
    {
        $data = [];
        $loginController = new loginController();
        $data = $loginController->registerUser($request);
        
        return [
            "success" => true,
            "data" => $data
        ];
    }

    /**
     * 
     * @return array
     * 
     */

    private function getFormData($code)
    {
        $request = $this->sendRequest($this->formFinishUrl . "/$code?finish", "", "GET");
        if (@$request['error']) abort(404, "Não encontrado");

        return $request;
    }

    public function finishView($id)
    {
        return view("pages.padraocolor.join-us.finish", $this->getFormData($id));
    }

    public function finishRequest($id, FinishRequest $request)
    {
        $newData = $request->only(['files', 'clients']);
        $form = $this->getFormData($id)['data'];
        $payload = array_merge($form, $newData);
        $payload = RegisterResource::toArray($payload, 1);

        \Log::debug("New data2", $newData);

        $url = $this->formCrmSubmitUrl;
        $method = "POST";
        $send = $this->sendRequest($url, $payload, $method);

        if (@$send['error']) 
            \Log::debug("resposta do send2", [$send]);
            abort(400, "Erro ao enviar o formulário. Tente novamente em alguns minutos. Se o erro persistir, por favor, entre em contato com a gente.");
            \Log::debug("New data3", $newData);
            

        return [
            "success" => true
        ];
    }

    public function edit($id)
    {

        $request = $this->sendRequest($this->formFinishUrl . "/$id?finish", "", "GET");

        if (@$request['error']) abort(404, "Não encontrado");

        return view("pages.padraocolor.join-us.form", $request);
    }
}
