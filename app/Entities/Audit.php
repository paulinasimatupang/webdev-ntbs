<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model 
{
    public $incrementing = true;

    protected $table = 'audits';
    protected $connection = 'pgsql_billiton';
}
