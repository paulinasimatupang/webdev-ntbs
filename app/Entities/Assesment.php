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
class Assesment extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;
    
    public $timestamps = false;

    protected $fillable = [
        'pertanyaan',
        'poin'
    ];

    protected $table = 'assesments';
    protected $connection = 'pgsql_billiton';
}
