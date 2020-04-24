<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;

class TransactionChecklistItems extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_items';
    protected $primaryKey = 'id';

    public function ScopeGetStatus($query, $checklist_item_id) {
        // see if any docs have been added
        $accepted = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id) -> where('doc_status', 'accepted') -> get();
        $rejected = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id) -> where('doc_status', 'rejected') -> get();
        if(count($accepted) > 0) {
            $status = 'Complete';
            $classes = 'bg-success text-white';
            $fa = '<i class="fal fa-check fa-lg mr-2"></i>';
        } if(count($rejected) > 0 && count($accepted) == 0) {
            $status = 'Incomplete';
            $classes = 'bg-danger text-white';
            $fa = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i>';
        } else {
            $checklist_item = $this -> where('id', $checklist_item_id) -> first();
            if($checklist_item -> checklist_item_required == 'yes') {
                $status = 'Required';
                $classes = 'bg-orange text-white';
                $fa = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i>';
            } else {
                $status = 'If Applicable';
                $classes = 'bg-gray text-gray';
                $fa = '';
            }
        }
        $details = collect();
        $details -> status = $status;
        $details -> classes = $classes;
        $details -> fa = $fa;
        return $details;
    }
}
