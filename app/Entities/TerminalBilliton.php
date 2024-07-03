<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class TerminalBilliton extends Model
{

    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'terminal_id';
    //
    protected $fillable = [
        'terminal_id',
        'terminal_type',
        'terminal_imei',
        'terminal_name',
        'merchant_id',
        'terminal_sim_number'
    ];
    
    protected $table = 'public.terminal';
}