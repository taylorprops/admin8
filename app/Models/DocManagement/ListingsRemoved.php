<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class ListingsRemoved extends Model
{
    protected $connection = 'mysql_taylorproperties';
    public $table = 'listings_removed';
}
