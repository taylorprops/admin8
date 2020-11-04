<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Model;

class OldAgentsTeams extends Model
{
    protected $connection = 'mysql_company';
    public $table = 'tbl_agent_teams';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
