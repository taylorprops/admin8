<?php

namespace App\Models\DocManagement\Transactions\CancelRequests;

use Illuminate\Database\Eloquent\Model;

class CancelRequests extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_cancel_requests';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
