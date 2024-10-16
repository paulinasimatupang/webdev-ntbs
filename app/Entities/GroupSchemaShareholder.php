<?php

namespace App\Entities;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Presenters\GroupSchemaShareholderPresenter;

/**
 * Class GroupSchemaShareholder.
 *
 * @package namespace App\Entities;
 */
class GroupSchemaShareholder extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    protected $presenter = GroupSchemaShareholderPresenter::class;
    public $incrementing = true;

    protected $fillable = [
        'id',
        'group_schema_id',
        'shareholder_id',
        'share'
    ];

    protected $table = 'group_schema_shareholders';

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class,'shareholder_id','id');
    }

}
