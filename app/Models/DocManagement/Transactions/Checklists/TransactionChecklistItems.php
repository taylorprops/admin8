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
        $doc = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id);
        $exists = $doc -> get();
        $accepted = $doc -> where('doc_status', 'complete') -> get();
        $rejected = $doc -> where('doc_status', 'rejected') -> get();

        if(count($accepted) > 0) {
            $status = 'Complete';
            $classes = 'bg-success text-white';
            $fa = '<i class="fal fa-check fa-lg mr-2"></i>';
            $helper_text = 'The requirements for this checklist item have been met';
        } if(count($rejected) > 0 && count($accepted) == 0) {
            $status = 'Incomplete';
            $classes = 'bg-danger text-white';
            $fa = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i>';
            $helper_text = 'Documents for this item have been rejected. They must be added again';
        } else {
            if(count($exists) > 0) {
                $status = 'Pending';
                $classes = 'bg-blue-light text-primary';
                $fa = '<i class="fal fa-stopwatch fa-lg mr-2"></i>';
                $helper_text = 'We have received your document for this item. It is in the review process';
            } else {
                $checklist_item = $this -> where('id', $checklist_item_id) -> first();
                if($checklist_item -> checklist_item_required == 'yes') {
                    $status = 'Required';
                    $classes = 'bg-danger text-white';
                    $fa = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i>';
                    $helper_text = 'This is a required item for this checklist';
                } else {
                    $status = 'If Applicable';
                    $classes = 'bg-default-light text-white';
                    $fa = '<i class="fal fa-minus-circle fa-lg mr-2"></i>';
                    $helper_text = 'Depending on the details of the transaction, this form might not be required';
                }
            }
        }
        $details = collect();
        $details -> status = $status;
        $details -> classes = $classes;
        $details -> fa = $fa;
        $details -> helper_text = $helper_text;
        return $details;
    }
}
