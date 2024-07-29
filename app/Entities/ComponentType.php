<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TerminalType.
 *
 * @package namespace App\Entities;
 */
class ComponentType extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;
    protected $primaryKey = 'component_type_id';

    protected $fillable = [
        'component_type_id',
        'component_type_naem'
    ];

    protected $connection = 'pgsql_billiton';
    protected $table = 'public.reff_component_type';
}
