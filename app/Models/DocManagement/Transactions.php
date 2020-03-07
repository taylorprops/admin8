<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions';
    public $timestamps = false;
}
