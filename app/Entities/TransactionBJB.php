<?php

namespace App\Entities;

use Auth;
use Illuminate\Database\Eloquent\Model;
use App\Presenters\TransactionBJBPresenter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

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
