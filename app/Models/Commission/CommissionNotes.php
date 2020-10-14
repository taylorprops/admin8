<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionNotes extends Model
{
    public $table = 'commission_notes';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];
}
