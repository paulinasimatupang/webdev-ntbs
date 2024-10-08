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
class Pengaduan extends Model implements Transformable, Auditable
{
    use TransformableTrait;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = true;
    
    public $timestamps = false;

    protected $fillable = [
        'kategori',
        'judul',
        'deskripsi',
        'status',
        'request_time',
        'reply_time',
        'mid'
    ];

    protected $table = 'pengaduan';
    protected $connection = 'pgsql_billiton';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'mid', 'mid');
    }
}
