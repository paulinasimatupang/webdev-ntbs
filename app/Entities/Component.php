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
class Component extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;

    protected $fillable = [
        'comp_id',
        'component_type_id',
        'comp_content_type',
        'visible',
        'comp_lbl',
        'comp_act',
        'min_length',
        'max_length',
        'comp_lbl_en'
    ];

    protected $connection = 'pgsql_billiton';
    protected $table = 'public.component';

    public function componentType()
    {
        return $this->belongsTo(ComponentType::class, 'component_type_id', 'component_type_id');
    }

    public function componentContentType()
    {
        return $this->belongsTo(ComponentContentType::class, 'comp_content_type', 'comp_content_type');
    }
}
