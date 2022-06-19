<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as Trait_CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Models\Reseller;
use App\Models\Holes;
use DB;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements CanResetPassword
{
    use HasRoles;
    use SoftDeletes;
    use Notifiable;
    use Trait_CanResetPassword;

    public $connection = "dashboard_server";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'type', 'name', 'user_name', 'pricing_role_id', 'email', 'password', 'provider', 'provider_id', 'wpp_phone', 'wpp_notification','id_loja',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'reseller',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['code', 'address', 'phone', 'doc'];

    public function getCodeAttribute()
    {
        return \Hashids::encode($this->id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhoneAttribute()
    {
        return $this->reseller->phone;
    }

    public function reseller()
    {
        return $this->hasOne(Reseller::class)->where("user_id", $this->id);
    }

    public function getAddressAttribute()
    {
        $address = $this->reseller->full_address;

        return array_merge($address, ['zip_code' => $this->reseller->zip_code]);
    }

    public function getDocAttribute()
    {
        return $this->reseller->doc;
    }

    public function attachRole($role)
    {
        $role = DB::connection("dashboard_server")->table("roles")->where("name", $role)->first();
        if (!$role) {
            return null;
        }
        if (Holes::where("model_type", User::class)->where("role_id", $role->id)->where("model_id", $this->id)->count() <= 0) {
            return Holes::insert(["model_type" => User::class, "role_id" => $role->id, "model_id" => $this->id]);
        }
        return null;
    }

    public function routeNotificationForSlack($notification)
    {
        return config('systemcolor.slack_ticket_created_hook');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
