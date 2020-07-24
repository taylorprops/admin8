<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionChecklistItems extends Model {

    public $table = 'docs_transactions_checklist_items';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetStatus($query, $checklist_item_id) {

        // see if any docs have been added
        $checklist_item = $this -> where('id', $checklist_item_id) -> first();
        $checklist_item_docs = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id);
        $docs_count = $checklist_item_docs -> count();
        // if doc_status is null for any added doc for this item than a form has been added but has not been reviewed
        $pending = $checklist_item_docs -> where(function($query) {
            $query -> where('doc_status', 'pending');
        }) -> get();

        $show_mark_required = false;
        $show_mark_not_required = false;
        if ($checklist_item -> checklist_item_required == 'yes') {
            $show_mark_not_required = true;
        } else {
            $show_mark_required = true;
        }

        // Pending, If Applicable, Incomplete, Complete
        if (count($pending) > 0) {

            $status = 'Pending';
            $classes = 'bg-blue-light text-primary';
            $fa = '<i class="fal fa-stopwatch fa-lg mr-2"></i>';
            $helper_text = 'We have received your document for this item. It is in the review process';
            $badge_class = 'badge-blue-light text-primary';

        } else {

            // if no docs have been added to checklist item the the only options are Required or If Applicable
            if ($docs_count == 0 && $checklist_item -> checklist_item_status != 'rejected') {

                if ($checklist_item -> checklist_item_required == 'yes') {
                    $status = 'Required';
                    $classes = 'bg-orange text-white';
                    $fa = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i>';
                    $helper_text = 'This is a required item for this checklist';
                    $badge_class = 'badge-orange text-white';
                } else {
                    $status = 'If Applicable';
                    $classes = 'bg-default-light text-white';
                    $fa = '<i class="fal fa-minus-circle fa-lg mr-2"></i>';
                    $helper_text = 'Depending on the details of the transaction, this form might not be required';
                    $badge_class = 'badge-primary text-white';
                }

            // if docs HAVE been added to checklist item the the only options are Complete or Rejected
            } else {

                if ($checklist_item -> checklist_item_status == 'accepted') {
                    $status = 'Complete';
                    $classes = 'bg-success text-white';
                    $fa = '<i class="fal fa-check fa-lg mr-2"></i>';
                    $helper_text = 'The requirements for this checklist item have been met';
                    $badge_class = 'badge-success text-white';
                } else if ($checklist_item -> checklist_item_status == 'rejected') {
                    $status = 'Rejected';
                    $classes = 'bg-danger text-white';
                    $fa = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i>';
                    $helper_text = 'Documents for this item have been rejected. They must be added again';
                    $badge_class = 'badge-danger text-white';
                }

            }

        }


        $details = collect();
        $details -> status = $status;
        $details -> classes = $classes;
        $details -> fa = $fa;
        $details -> helper_text = $helper_text;
        $details -> show_mark_required = $show_mark_required;
        $details -> show_mark_not_required = $show_mark_not_required;
        $details -> badge_class = $badge_class;
        return $details;
    }

}
