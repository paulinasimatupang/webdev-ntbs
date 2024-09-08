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
class Cabang extends Model implements Transformable
{
    use TransformableTrait;
    protected $connection = 'pgsql_billiton';
    protected $table = 'cabang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'created_at'
    ];
}
