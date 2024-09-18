<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Imei extends Model
{
    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'tid',
        'imei',
        'mid'
    ];

    protected $table = 'public.request_imei';
}
