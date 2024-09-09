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
class Branch extends Model implements Transformable
{
    use TransformableTrait;
    protected $connection = 'pgsql_billiton';
    protected $table = 'branch';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'branch_code',
        'branch_name',
        'address',
        'city',
        'office_type'
    ];
}
