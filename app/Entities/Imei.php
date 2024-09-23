<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Imei extends Model
{
    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'tid',
        'imei',
        'mid',
        'status',
        'id_pengaduan'
    ];

    protected $table = 'public.request_imei';
    
    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'tid', 'tid');
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id');
    }
}
