<?php

namespace App\Models\DocManagement\Transactions\Listings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listings extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_listings';
    protected $primaryKey = 'Listing_ID';
    public $timestamps = false;
    protected $guarded = [];
}
