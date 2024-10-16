<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class TerminalType.
 *
 * @package namespace App\Entities;
 */
class Branch extends Model implements Transformable, Auditable
{
    use TransformableTrait;
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'pgsql_billiton';
    protected $table = 'branch';
    protected $primaryKey = 'branch_id';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'branch_code',
        'branch_name'
    ];
}
