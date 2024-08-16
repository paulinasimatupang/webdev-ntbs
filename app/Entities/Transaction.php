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
<<<<<<< Updated upstream
        'transaction_id',
        'service_id',
        'event_uid',
        'transaction_status_id',
        'transaction_time',
        'amount',
        'fee',
=======
        'transaction_id', 
        'event_uid',
        'service_id', 
        'transaction_status_id', 
        'transaction_time', 
        'amount',
        'fee', 
>>>>>>> Stashed changes
        'account_number',
        'no_resi',
        'user_uid',
        'card_no',
        'trace_no',
<<<<<<< Updated upstream
        'batch_no',
        'appr_code',
    ];

    protected $primaryKey = 'transaction_id';
    protected $table = 'public.transaction';
    protected $connection = 'pgsql_billiton';
    public $timestamps = false;

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_uid', 'user_uid');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'user_uid');
    }

    public function transactionStatus()
    {
        return $this->hasMany(TransactionStatus::class, 'transaction_status_id', 'user_uid');
    }

    public function user()
    {
        return $this->belongsTo(UsersBilliton::class, 'user_uid', 'user_uid');
    }
=======
        'batching',
        'appr_code'
    ];

    protected $table = 'transaction';

     // Relasi belongsTo untuk EventBilliton
     public function event()
     {
         return $this->belongsTo(EventBilliton::class, 'event_uid');
     }
 
     // Relasi belongsTo untuk ServiceBilliton
     public function service()
     {
         return $this->belongsTo(ServiceBilliton::class, 'service_id');
     }
 
     // Relasi belongsTo untuk TransactionStatus
     public function transactionStatus()
     {
         return $this->belongsTo(TransactionStatus::class, 'transaction_status_id');
     }
 
     // Relasi belongsTo untuk FeeBilliton
 
     // Relasi belongsTo untuk UserBilliton
     public function user()
     {
         return $this->belongsTo(UsersBilliton::class, 'user_uid');
     }
 
     // Relasi belongsTo untuk UserCardBilliton
>>>>>>> Stashed changes
}

// 'id',
// 'service_id',
// 'code',
// 'merchant_id',
// 'merchant_no',
// 'price',
// 'vendor_price',
// 'note',
// 'status',
// 'payment_status',
// 'is_suspect'