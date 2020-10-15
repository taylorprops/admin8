<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionCheckDeductions extends Model
{
    public $table = 'commission_check_deductions';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

}
