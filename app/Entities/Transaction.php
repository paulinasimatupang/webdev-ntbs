<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Transactions.
 *
 * @package namespace App\Entities;
 */
class Transaction extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;

    protected $fillable = [
       'transacton_id',
        'service_id',
        'event_uid',
        'transaction_status_id',
        'transaction_time',
        'amount',
        'fee',
        'account_number',
        'no_resi',
        'user_uid',
        'card_no',
        'trace_no',
        'batch_no',
        'appr_code',
    ];

    protected $table = 'public.transaction';
    protected $connection = 'pgsql_billiton';

    public function event()
    {
        return $this->belongsTo(Event::class,'event_uid','user_uid');
    }

    public function service()
    {
        return $this->belongsTo(Service::class,'service_id','user_uid');
    }

    public function transactionStatus()
    {
        return $this->hasMany(TransactionStatus::class,'transaction_status_id','user_uid');
    }

    public function user()
    {
        return $this->hasMany(UsersBilliton::class,'user_uid','user_uid');
    }
}
