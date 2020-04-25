<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionChecklistItemsNotes extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_item_notes';
    protected $primaryKey = 'id';

    public function ScopeGetNotes($query, $checklist_item_id) {

        $notes = $this -> where('checklist_item_id', $checklist_item_id) -> get();

        return $notes;
    }
}