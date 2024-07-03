<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

/**
 * Class TransactionBJB.
 *
 * @package namespace App\Entities;
 */
class TransactionBJB extends Model
{
//    use TransformableTrait;
//    use SoftDeletes;

    protected $presenter = TransactionBJBPresenter::class;
//    public $incrementing = true;
    protected $connection = 'pgsql_report';

    // protected $fillable = [
    //     'id',
    //     'transaction_name',
    //     'transaction_code',
    //     'product_name',
    //     'nominal',
    //     'fee',
    //     'total'
    // ];

    protected $table = 'report_mini_banking_view';

    // protected $table = 'fee_view';
    //protected $table = 'reff_pmt_transactions';

    // public function productTransaction()
    // {
    //     return $this->hasOne(Transaction::class,'stan','stan');
    // }

}
