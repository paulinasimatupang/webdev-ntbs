<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ReportFee extends Model
{
    protected $connection = 'pgsql_report';
    protected $primaryKey = 'id';
    //
    protected $fillable = [
        'id',
        'tid',
        'mid',
        'total_amount_fee',
        'fee_agen',
        'fee_bjb',
        'fee_selada',
        'buffer',
        'total_amount_transaction',
        'total_transaction',
        'product_name',
        'created_at',
        'agent_name'
    ];
    
    protected $table = 'public.report_fee';
}
