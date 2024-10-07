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
class CompOption extends Model implements Transformable
{
    use TransformableTrait;
    protected $connection = 'pgsql_billiton';
    protected $table = 'comp_option';
    protected $primaryKey = null; 
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'opt_id',
        'comp_id',
        'seq',
        'opt_label'
    ];

    public function comp_option()
    {
        return $this->belongsTo(Component::class,'opt_id','opt_id');
    }

    public function option_value()
    {
        return $this->belongsTo(OptionValue::class,'meta_id','meta_id');
    }
}
