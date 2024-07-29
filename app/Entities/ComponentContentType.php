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

class ComponentContentType extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;
    protected $connection = 'pgsql_billiton';
    protected $table = 'public.reff_comp_content_type';

    protected $primaryKey = 'comp_content_type'; 
    protected $fillable = [
        'comp_content_type', 
        'comp_content_type_name'
    ]; 
}

