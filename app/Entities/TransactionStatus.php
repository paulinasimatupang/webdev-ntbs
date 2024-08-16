<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TransactionStatus.
 *
 * @package namespace App\Entities;
 */
class TransactionStatus extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;

    protected $fillable = [
        'transaction_status_id',
<<<<<<< Updated upstream
        'transaction_stats_desc',
    ];

    protected $table = 'reff_transactions_status';
    protected $connection = 'pgsql_billiton';
=======
        'transaction_status_desc'
    ];

    protected $table = 'reff_transaction_status';

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_status_id');
    }
>>>>>>> Stashed changes
}

// 'id',
// 'transaction_id',
// 'status',
// 'description'