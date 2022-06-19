<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardApi;
use App\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {      
        $this->sendRequestDashboard($request);
        $this->middleware('guest');
    }

    private function sendRequestDashboard($request) 
    {   
        if($request->email)
        {  
            $redID = $this->getUserByEmail($request->email);
            $dashboard = new DashboardApi();
            $dashboard->make("reset_password", ["ref_id" => $redID, "new_pass" => $request->password]);
            $dashboard->send();
        }
    }

    private function getUserByEmail($email) 
    {  
        $user = User::where("email", $email)->first();
        return $user->reseller->ref_id; 
    }
}
