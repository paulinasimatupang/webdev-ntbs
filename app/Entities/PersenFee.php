<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class PersenFee.
 *
 * @package namespace App\Entities;
 */
class PersenFee extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'pgsql_billiton';
    protected $table = 'persen_fee';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'penerima',
        'persentase'
    ];
}
