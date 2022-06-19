<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Reseller;
use App\Models\PricingRole;
use App\Models\ResellerRoute;
use App\Http\Controllers\SystemColorController as SystemColor;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Socialite;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $loginInfo = request()->user;
        $password = request()->password;
        if (!is_null($loginInfo) && !is_null($password)) {
            $user = User::where('email', $loginInfo)
                ->orWhere('user_name', $loginInfo)
                ->first();

            if (!isset($user->id)) {
                Log::debug("__construct");
                //$user = $this->registerApiUser();
            }

            $this->middleware('guest')->except('logout');
        }
    }

    public function login(Request $request)
    {
        Log::debug("request".json_encode($request));
        $credentials = [
            'user_name' => $request['user'],
            'password' => $request['password'],
        ];
        unset($credentials["user"]);
        if (Auth::attempt($credentials)) {
            Log::debug("credentials - IF".json_encode($credentials));
            $user = User::where("user_name", $credentials["user_name"])->first();
            if (!$user->hasRole("revendedor")) {
                Auth::logout();
                return $this->sendFailedLoginResponse($request);
            }
        } else {
            return $this->sendFailedLoginResponse($request);
        }
        return redirect(@$request["redirect"] ? $request["redirect"] : $this->redirectTo);
    }

    public function username()
    {
        return 'user';
    }

    public function credentials(Request $request)
    {

        $login = $request['user'];
        $password = $request['password'];
        $credentials = ['password' => $password];
        if (strstr($login, '@')) {
            $credentials['email'] = $login;
        } else {
            $credentials['user_name'] = $login;
        }
        return $credentials;
    }

    public function getUserType($type)
    {
        if (strtolower($type) == "transportadora") {
            return 'frete';
        } else {
            return 'rota';
        }
    }

    private function registerApiUser()
    {
        Log::debug("registerApiUser");
        $auth['user_name'] = request()->user;
        $auth['password'] = request()->password;

        $systemColor = new SystemColor();
        $response = $systemColor->import('reseller', $auth);

        $data = $response['data'][0];
        $this->registerUser($data);
    }

    public function startRegister()
    {
        $this->faker = app(Faker::class);
        $this->loadResellerRoutes();
        $this->loadPricingRoles();
    }
    private function loadPricingRoles()
    {
        $pricing_role = PricingRole::all()->pluck('id', 'ref_id');
        $this->pricing_roles = $pricing_role;
    }
    private function loadResellerRoutes()
    {
        $reseller_routes = ResellerRoute::all()->pluck('id', 'ref_id');
        $this->routes = $reseller_routes;
    }

    public function registerUser($userInfo)
    {
        Log::debug("registerUser");
        $this->startRegister();
        
        //$data = $this->sanitizeRequest($userInfo);
        
        $this->findModels($userInfo);
        
        $this->makeUser($userInfo);

        $this->makeReseller($userInfo);

        return $this->user;
    }

    private function findModels($data)
    {
        $this->reseller = Reseller::firstOrNew(['ref_id' => $data['codigo']]);
        $this->user = $this->reseller->user ? $this->reseller->user : new User;
    }

    //Utils
    private function sanitizeRequest($request)
    {
        $this->reseller_route = $request['zona'];
        $this->sanitizeArrays($request);
        $this->sanitizeStrings($request);
        return $request;
    }

    private function sanitizeArrays(&$request)
    {
        Log::debug("request=> ".json_encode($request['reseller']));
        $data[] = $request['reseller'];
        foreach ((object)$data as $key => &$v) {
            
            $v = explode(';', $v)[0];
            if ($key == 'email') {
                if ($v == "") {
                    $v = mt_rand(0, 9999) . $this->faker->unique()->safeEmail;
                } else {
                    $v = mt_rand(0, 9999) . $v;
                }
            }
        }
    }

    private $sanitizeStringItems = ['nome', 'descricao', 'zona', 'endereco', 'bairro', 'cidade', 'pais'];
    private $sanitizeStringFunctions = ['mb_strtolower', [Str::class, "title"], 'trim'];

    private function sanitizeStrings(&$request)
    {
        $sanitize = array_only($request, $this->sanitizeStringItems);
        foreach ($this->sanitizeStringFunctions as $function) {
            $sanitize = array_map($function, $sanitize);
        }
        $sanitize['endereco'] = explode(",", $sanitize['endereco'])[0];

        $request = array_merge($request, $sanitize);
    }

    //User Config

    private function makeUser($data)
    {
        $user = $this->user;
        
        $saveUser = (object)$data['reseller'];

        Log::debug("id_loja=> ".json_encode($saveUser->id_loja));
        Log::debug("email=> ".json_encode($saveUser->email));

        $user_email = User::where('email', $saveUser->email)
                ->where('id_loja', $saveUser->id_loja)
                ->first();
                Log::debug("user ".json_encode($user_email));        
        if($user_email != null){
            Log::debug("email existente");
            abort(400, "Email já cadastrado");
        }

        $userData["name"]  = $saveUser->name;
        $userData["user_name"]  = $saveUser->email;
        $userData["password"]  = bcrypt($saveUser->password);
        $userData["email"]  = $saveUser->email;
        $userData["wpp_phone"]  = $saveUser->whatsapp;
        $userData["id_loja"]  = $saveUser->id_loja;
        $userData["type"] = 1;
        Log::debug("userData",$userData);
        $user
            ->fill($userData)
            ->saveOrFail();
    }

    private function getUserShippingType($type)
    {
        if (strtolower($type) == "transportadora") {
            return 'frete';
        } else {
            return 'rota';
        }
    }

    // Reseller Config

    public function findReseller($ref)
    {
        return Reseller::firstOrNew(['ref_id' => $ref]);
    }

    private function makeReseller($data)
    {
        $user = $this->user;
        config(['database.default' => 'dashboard_server']);
        $user->assignRole('revendedor');

        $reseller = $this->reseller;

        $saveReseller = (object)$data['reseller'];
        $saveAddress = (object)$data['address'];

        $resellerData["user_id"]  = $user->id;
        $resellerData["pricing_role_id"]  = 1;
        $resellerData["ref_id"]  = 1;
        $resellerData["can_be_trusted"]  = 1;
        $resellerData["reseller_route_id"] = 12;
        $resellerData["name"]  = $saveReseller->name;
        $resellerData["full_name"]  = $saveReseller->name;
        $resellerData["doc"]  = $saveReseller->doc;
        $resellerData["full_address"]  =  $this->getAddresses($saveAddress);
        $resellerData["phone"]  = $saveReseller->phone;
        $resellerData["email"]  = $saveReseller->email;
        $resellerData["location"]  = "Brasil";
        $resellerData["account_manager_id"]  = 0;
        $resellerData["zip_code"]  = $saveAddress->zipcode;

        

        $reseller
            ->fill($resellerData)
            ->saveOrFail();
    }

    private function getAddresses($data)
    {
        $addresses["street"] = $data->street;
        $addresses["number"] = $data->number;
        $addresses["district"] = $data->district;
        $addresses["state"] = $data->state;
        $addresses["city"] = $data->city;
        $addresses["complement"] = $data->complement;
        $addresses["reference"] = $data->reference;

        return $addresses;
    }

    private function canBeTrusted($type)
    {
        return $type == "N" ? 1 : 0;
    }

    private function getResellerRoute()
    {
        return $this->routes[$this->reseller_route];
    }

    private function getPricingRole($refID)
    {
        return $this->pricing_roles[$refID];
    }

    public function redirectToProvider($provider)
    {
        $socialLogin = Socialite::driver($provider)->redirect();
        return $socialLogin;
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();
        if (!$user) abort(500);
        $_user = User::where("provider_id", $user->id)->where("provider", $provider);
        if ($_user->count() > 0) {
            $_user = $_user->firstOrFail();
            Auth::loginUsingId($_user->id, true);
            return redirect($this->redirectTo);
        } else {
            $data = [
                "message"     => "Dados cadastrais importados do $provider",
                "provider"    => $provider,
                "provider_id" => $user->id,
                "email"       => $user->email,
                "name"        => $user->name,
            ];
            return redirect(route('seja-um-revendedor', $data));
        }
    }


    public function recoverPass()
    {
    }
}
