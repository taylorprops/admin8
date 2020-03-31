<?php

namespace App\Models\DocManagement\Transactions\Listings\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingChecklistItemsDocs extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_listing_items_docs';
    protected $primaryKey = 'id';

    public function ScopeGetDocs($query, $checklist_item_id) {
        $docs = $this -> where('checklist_item_id', $checklist_item_id) -> get();
    }
}
