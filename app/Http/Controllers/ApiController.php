<?php

namespace App\Http\Controllers;

use App;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\AdditionalConfig;
use Illuminate\Http\Request;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use marcusvbda\uploader\Controllers\UploaderController as Uploader;



class ApiController extends Controller
{
    public function upload_image(Request $request)
    {
        $data = $request->all();
        $file = Uploader::upload($data['file'], $data['file']->getClientOriginalName(), @$data['_alt'], null, \Auth::user()->id);
        $file->setPublic();
        return response()->json(['success' => true, 'message' => null, 'data' => $file]);
    }

    public function product_department()
    {
        try {
            $department = [
                'impressao-digital' => Product::where('department', 'impressao digital')->orderBy('name', 'asc')->get(),
                'impressao-offset' => Product::where('department', 'impressao offset')->orderBy('name', 'asc')->get(),
                'comunicacao-visual' => Product::where('department', 'comunicacao visual')->orderBy('name', 'asc')->get(),
            ];

            return response()->json(['success' => true, 'message' => null, 'data' => $department]);
        } catch (\Exception $e) {
            @\DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function product_config(Request $request)
    {
        try {
            $data = $request->all();
            $data = AdditionalConfig::where('type', $data['type'])->with('attr')->get();

            return response()->json(['success' => true, 'message' => null, 'data' => $data]);
        } catch (\Exception $e) {
            @\DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function get_testimonies()
    {
        try {
            $data = Testimonial::with("files")->get();
            return response()->json(['success' => true, 'message' => null, 'data' => $data]);
        } catch (\Exception $e) {
            @\DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function set_wpp_phone(Request $request)
    {
        try {
            $data = $request->all();
            $userId = Auth::user()->getId();
            Auth::user()->update($data);
            if ($data['wpp_phone'] != null) {
                $this->set_wpp_welcome_queue($data, $userId);
            }
            return response()->json(['success' => true, 'message' => null, 'data' => $data]);
        } catch (\Exception $e) {
            @\DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    private function set_wpp_welcome_queue($data, $userId)
    {
        $client = new Client();
        $data = [
            "json" => [
                "wpp_phone" => $data['wpp_phone'],
                "userId" => $userId
            ]
        ];
        $guzzleReturn = $client->request("POST", env('DASHBOARD_ROUTE') . "/api/wpp/queue/welcome/store", $data);
        debug_log('Whatsapp/Register', "Teste de envio", [$data, $guzzleReturn->getBody()]);
        $status_code = $guzzleReturn->getStatusCode();
    }

    public function get_company_categories()
    {
        $data = config("product_config.company_categories");
        return response()->json(['success' => true, 'message' => null, 'data' => $data]);
    }

    public function resellerRequest(Request $request)
    {
        $data = $request->all();
        if ($request['type'] == 0) {
            $data = array_except(
                $data,
                [
                    "details_ie",
                    "details_im",
                    "details_files_sc",
                    "details_files_ie",
                    "details_files_im"
                ]
            );
        }
        foreach ($data as $key => &$value) {
            if (strstr($key, "details_files") && $value) {
                $file = Storage::disk('public')->putFile('reseller-request', $value);
                $value = app('url')->to('/') . Storage::url($file);
            }
        }

        try {

            $client = new Client();
            $response = $client->request("POST", "https://crm.otimize.me/padrao/new-register", ['form_params' => $data]);
            return [
                'redirect' => true
            ];
        } catch (\Exception $e) {
            report($e);
            return [
                'redirect' => true
            ];
        }
    }

    public function registerFinished()  
    {
        return view('auth.thank-you');
    }
}
