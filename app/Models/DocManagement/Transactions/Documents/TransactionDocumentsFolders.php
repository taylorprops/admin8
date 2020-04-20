<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;

class TransactionDocumentsFolders extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_docs_folders';
    protected $primaryKey = 'id';
}
