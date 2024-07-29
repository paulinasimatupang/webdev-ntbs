<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Transactions.
 *
 * @package namespace App\Entities;
 */
class ServiceMeta extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;

    protected $fillable = [
        'meta_id',
        'service_id',
        'seq',
        'meta_type_id',
        'meta_default',
        'influx'
    ];

    protected $table = 'public.service_meta';
    protected $connection = 'pgsql_billiton';
    public $timestamps = false;

    
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
    
}
