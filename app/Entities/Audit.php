<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Audits.
 *
 * @package namespace App\Entities;
 */
class Audit extends Model
{

    public $incrementing = true;

    protected $fillable = [
        'user_type',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
        'created_at',
        'updated_at',
        'user_id'
    ];

    protected $table = 'audits';
    protected $connection = 'pgsql_billiton';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }
}
