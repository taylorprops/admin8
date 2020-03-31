<?php

namespace App\Models\DocManagement\Transactions\Listings\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingDocuments extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_listings_docs';
    protected $primaryKey = 'id';
}
