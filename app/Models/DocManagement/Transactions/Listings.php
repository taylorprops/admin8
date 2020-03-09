<?php

namespace App\Models\DocManagement\Transactions;

use Illuminate\Database\Eloquent\Model;

class Listings extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_listings';
    public $timestamps = false;
}
