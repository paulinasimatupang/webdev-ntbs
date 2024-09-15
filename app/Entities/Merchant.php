<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Merchants.
 *
 * @package namespace App\Entities;
 */
class Merchant extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;

    protected $fillable = [
        'id',
        'name',
        'no',
        'address',
        'phone',
        'email',
        'status',
        'user_id',
        'terminal_id',
        'city',
        'mid',
        'status_agen',
        'active_at',
        'resign_at',
        'branchid',
        'no_cif',
        'file_ktp',
        'foto_lokasi_usaha',
        'file_npwp',
        'pin',
        'pekerjaan',
        'jenis_kelamin',
        'jenis_agen',
        'kode_pos',
        'lokasi',
        'kecamatan',
        'kelurahan',
        'provinsi',
        'no_perjanjian_kerjasama',
        'tgl_perjanjian',
        'tgl_pelaksanaan',
        'no_ktp',
        'no_npwp',
        'no_telp'
    ];

    protected $table = 'merchants';
    protected $connection = 'pgsql';

    public function terminal()
    {
        return $this->hasMany(Terminal::class,'merchant_id','mid');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'kode_agen', 'mid');
    }
}
