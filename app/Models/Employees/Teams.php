<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_teams';
    protected $guarded = [];
}
