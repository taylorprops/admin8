<?php

namespace App\Models\DocManagement\Transactions\EditFiles;

use Illuminate\Database\Eloquent\Model;

class UserFields extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transaction_fields';
    public $timestamps = false;
}
