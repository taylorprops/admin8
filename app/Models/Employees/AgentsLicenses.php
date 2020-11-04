<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class AgentsLicenses extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_agents_licenses';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
