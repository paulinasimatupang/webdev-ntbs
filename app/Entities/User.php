<?php

namespace App\Entities;

use Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DateTimeZone;
use DateTime;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    // HasFactory;

    // HasApiTokens,

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'role_id',
        'fullname',
        'username',
        'email',
        'password',
        'branchid',
        'status'
    ];
    protected $table = 'users';
    protected $casts = [
        'id' => 'string'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $keyType = 'string'; // Tipe UUID

    // public function hasRole($role)
    // {
    //     return in_array($role, $this->roles);
    // }

    public function hasRole($role)
    {
        return $this->roles && $this->roles->name === $role;
    }


    public function user_group()
    {
        return $this->hasMany(UserGroup::class, 'user_id', 'id');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'user_id', 'id');
    }
    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate(4);
        });
    }


    public function can_access($routeName)
    {
        $role = $this->roles; // Ambil role user
        $permissions = $role->permissions->pluck('name')->toArray(); // Ambil semua permissions dari role

        return in_array($routeName, $permissions);
    }



    public function has_action($menu, $action)
    {
        $role_id = Auth::user()->role_id;
        $role_privilege = Privilege::where('role_id', $role_id)
            ->where('name', '=', $menu)
            ->where('key', '=', $action)
            ->select('key', 'value')
            ->first();
        return $role_privilege->value;
    }

    public function getRole()
    {
        $role_id = Auth::user()->role_id;
        $role = Role::where('id', $role_id)
            ->first();

        if ($role) {
            $result = $role->name;
        } else {
            $result = 'no role';
        }

        return $result;
    }
}
