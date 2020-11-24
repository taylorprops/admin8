<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    public $table = 'commission';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function agent() {
        return $this -> belongsTo('App\Models\Employees\Agents', 'Agent_ID');
    }

    public function other_checks() {
        return $this -> hasMany('App\Models\Commission\CommissionChecksIn', 'Commission_ID')/*  -> where('commission_checks_in.active', 'yes') */;
    }

}
