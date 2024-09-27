<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use OwenIt\Auditing\Contracts\Auditable;
/**
 * Class Merchants.
 *
 * @package namespace App\Entities;
 */
class Assesment extends Model implements Auditable
{
    use TransformableTrait;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = true;
    
    public $timestamps = false;

    protected $fillable = [
        'pertanyaan',
        'poin'
    ];

    protected $table = 'assesments';
    protected $connection = 'pgsql_billiton';
}
