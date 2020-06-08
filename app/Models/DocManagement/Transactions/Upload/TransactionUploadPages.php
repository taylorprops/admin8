<?php

namespace App\Models\DocManagement\Transactions\Upload;

use Illuminate\Database\Eloquent\Model;

class TransactionUploadPages extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_uploads_pages';
    protected $guarded = [];
}
