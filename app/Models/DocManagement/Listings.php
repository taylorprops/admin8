<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class Listings extends Model
{
    protected $connection = 'mysql_taylorproperties';
    public $table = 'listings';
}
