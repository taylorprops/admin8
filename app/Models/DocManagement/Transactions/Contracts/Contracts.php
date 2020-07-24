<?php

namespace App\Models\DocManagement\Transactions\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Transactions\Listings\Listings;
use Schema;

class Contracts extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_contracts';
    protected $primaryKey = 'Contract_ID';
    public $timestamps = false;
    protected $guarded = [];

    public function ScopeContractColumnsNotInListings() {
        $listing_columns = Schema::getColumnListing('docs_transactions_listings');
        $contract_columns = Schema::getColumnListing('docs_transactions_contracts');
        return array_diff($contract_columns, $listing_columns);
    }
}
