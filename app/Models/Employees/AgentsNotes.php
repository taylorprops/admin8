<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class AgentsNotes extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_agents_notes';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
