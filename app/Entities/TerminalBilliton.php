<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TerminalBilliton extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'terminal_id';
    public $timestamps = false;
    protected $fillable = [
        'terminal_id',
        'terminal_type',
        'terminal_imei',
        'terminal_ip',
        'terminal_name',
        'wk_ttl',
        'last_login',
        'merchant_id',
        'terminal_sim_number'
    ];
    
    protected $table = 'public.terminal';
}