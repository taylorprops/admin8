<?php

namespace App\Models\DocManagement\Transactions\EditFiles;

use Illuminate\Database\Eloquent\Model;

class UserFieldsValues extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transaction_fields_inputs_values';
    public $timestamps = false;
}