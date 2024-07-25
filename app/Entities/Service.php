<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Services.
 *
 * @package namespace App\Entities;
 */
class Service extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    public $incrementing = true;

    protected $fillable = [
        'service_id',
        'service_name',
        'is_transaction',
        'screen_respon',
        'screen_start',
        'is_to_core',
        'service_class',
        'param_1',
        'param_2',
        'trx_tbl',
        'service_url',
        'system_markup'
    ];
    protected $table = 'public.service';
    protected $connection = 'pgsql_billiton';

}
