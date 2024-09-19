<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Merchants.
 *
 * @package namespace App\Entities;
 */
class AssesmentResult extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'total_poin',
        'catatan'
    ];

    protected $table = 'assesments_result';
    protected $connection = 'pgsql_billiton';
}
