<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ResponseLog extends Model
{
    protected $connection = 'pgsql_billiton';
    
    protected $quotes = '"';

    protected $fillable = [
        'message_id',
        'name',
        'value'

    ];

    protected $table = "responselog";
}
