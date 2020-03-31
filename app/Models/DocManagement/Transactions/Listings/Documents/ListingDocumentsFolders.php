<?php

namespace App\Models\DocManagement\Transactions\Listings\Documents;

use Illuminate\Database\Eloquent\Model;

class ListingDocumentsFolders extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_listings_docs_folders';
    protected $primaryKey = 'id';
}
