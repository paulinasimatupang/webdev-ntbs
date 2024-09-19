<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Merchants.
 *
 * @package namespace App\Entities;
 */
class AssesmentResultDetail extends Model implements Transformable
{
    use TransformableTrait;

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'assesment_id',
        'pertanyaan_id',
        'poin'
    ];

    protected $table = 'assesments_result_detail';
    protected $connection = 'pgsql_billiton';

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'pertanyaan_id', 'id');
    }
    public function assesmentResult()
    {
        return $this->belongsTo(AssesmentResult::class, 'assesment_id', 'id');
    }
}
