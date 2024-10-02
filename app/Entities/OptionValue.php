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
class OptionValue extends Model implements Transformable
{
    use TransformableTrait;
    protected $connection = 'pgsql_billiton';
    protected $table = 'option_value';
    protected $primaryKey = null;
    public $incrementing = false; 
    public $timestamps = false;

    protected $fillable = [
        'opt_id',
        'meta_id',
        'default_value'
    ];
    public function comp_option()
    {
        return $this->belongsTo(CompOption::class,'opt_id','opt_id');
    }

}
