<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class UsersBilliton extends Model
{
    //
    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'user_uid';
    //
    protected $fillable = [
        'user_uid',
        'user_status_uid',
        'user_type_uid',
        'username',
        'app_ver',
        'need_approval',
        'account_name',
        'card_no',
        'imei',
        'batch_no',
        'version',
        'brand',
        'model',
        'os_ver'
    ];

    
    protected $table = 'public.users';
}
