<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_agents';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function scopeAgentDetails($query, $id) {
        $agent_details = $query -> find($id);
        return $agent_details;
    }
}
