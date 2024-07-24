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
        'transaction_stats_desc',
    ];

    protected $table = 'reff_transactions_status';
    protected $connection = 'pgsql_billiton';
}
