<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class TerminalUserBilliton extends Model
{
    //
    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'terminal_id';
    //
    protected $fillable = [
        'terminal_id',
        'user_uid'
    ];

    
    protected $table = 'public.terminal_user';
}
