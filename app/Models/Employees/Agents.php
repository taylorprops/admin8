<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_agents';

    public function scopeAgentDetails($query, $id) {
        $agent_details = $query -> whereId($id) -> first();
        return $agent_details;
    }
}
