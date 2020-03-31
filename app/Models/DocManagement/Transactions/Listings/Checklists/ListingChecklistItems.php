<?php

namespace App\Models\DocManagement\Transactions\Listings\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingChecklistItems extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_listing_items';
    protected $primaryKey = 'id';
}
