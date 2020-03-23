<?php

namespace App\Models\DocManagement\Transactions\Members;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_members';
    protected $primaryKey = 'id';
}
