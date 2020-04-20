<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionChecklistItemsDocs extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_item_docs';
    protected $primaryKey = 'id';

    public function ScopeGetDocs($query, $checklist_item_id) {
        $docs = $this -> where('checklist_item_id', $checklist_item_id) -> get();
    }
}
