<?php

namespace App\Models\DocManagement\Transactions\Contracts;

use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_contracts';
    protected $primaryKey = 'Contract_ID';
    public $timestamps = false;
}
