<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDocuments extends Model {
    use SoftDeletes;

    public $table = 'docs_transactions_docs';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';



}
