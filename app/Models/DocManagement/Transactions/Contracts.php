<?php

namespace App\Models\DocManagement\Transactions;

use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_contracts';
    public $timestamps = false;
}
