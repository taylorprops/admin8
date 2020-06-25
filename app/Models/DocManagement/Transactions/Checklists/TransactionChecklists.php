<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;

class TransactionChecklists extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklists';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeCreateTransactionChecklist($request, $checklist_id, $Listing_ID, $Contract_ID, $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built) {


        $where = [
            ['checklist_represent', $checklist_represent],
            ['checklist_type', $checklist_type],
            ['checklist_property_type_id', $checklist_property_type_id],
            ['checklist_property_sub_type_id', $checklist_property_sub_type_id],
            ['checklist_sale_rent', $checklist_sale_rent],
            ['checklist_state', $checklist_state],
            ['checklist_location_id', $checklist_location_id]
        ];

        // get checklist
        $checklist = Checklists::where($where) -> first();

        // get checklist items
        $checklist_items = new ChecklistsItems();
        $items = $checklist_items -> where('checklist_id', $checklist -> id) -> orderBy('checklist_item_order') -> get();

        // some items and docs from old checklist will be kept and added to the new
        $remove_ids = [];
        $keep_form_ids = [];

        // if existing checklist then keep items/forms from old checklist
        // that will be on the new so the user doesn't have to add docs and notes to checklist
        if($checklist_id) {

            $existing_checklist = TransactionChecklists::find($checklist_id);
            $existing_checklist -> checklist_id = $checklist -> id;
            $existing_checklist -> Listing_ID = $Listing_ID;
            $existing_checklist -> Contract_ID = $Contract_ID;
            $existing_checklist -> Agent_ID = $Agent_ID;
            $existing_checklist -> save();

            $existing_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> get();

            // get all ids for new checklist items
            $new_form_ids = [];
            foreach($items as $item) {
                $new_form_ids[] = $item -> checklist_form_id;
            }
            // group items that are going to be kept and removed
            foreach($existing_items as $existing_item) {
                if(in_array($existing_item -> checklist_form_id, $new_form_ids)) {
                    $keep_form_ids[] = $existing_item -> checklist_form_id;
                } else {
                    $remove_ids[] = $existing_item -> id;
                }
            }

            // if there are items no longer needed, remove them
            if(count($remove_ids) > 0) {
                $remove_items = TransactionChecklistItems::whereIn('id', $remove_ids) -> delete();
                $remove_item_docs = TransactionChecklistItemsDocs::whereIn('checklist_item_id', $remove_ids) -> delete();
                $remove_item_notes = TransactionChecklistItemsNotes::whereIn('checklist_item_id', $remove_ids) -> delete();
                $remove_docs = TransactionDocuments::whereIn('checklist_item_id', $remove_ids) -> update(['assigned' => 'no', 'checklist_item_id' => '0']);
            }

        } else {

            // if no existing checklist create a new one
            $add_checklist = new TransactionChecklists();
            $add_checklist -> checklist_id = $checklist -> id;
            $add_checklist -> Listing_ID = $Listing_ID;
            $add_checklist -> Contract_ID = $Contract_ID;
            $add_checklist -> Agent_ID = $Agent_ID;
            $add_checklist -> hoa_condo = $checklist_hoa_condo;
            $add_checklist -> year_built = $checklist_year_built;
            $add_checklist -> sale_rent = $checklist_sale_rent;
            $add_checklist -> save();
            $checklist_id = $add_checklist -> id;

        }

        // if not an item from old checklist that is transferred to new, add to new checklist`
        foreach($items as $item) {

            if(!in_array($item -> checklist_form_id, $keep_form_ids)) {

                $add_checklist_items = new TransactionChecklistItems();
                $add_checklist_items -> checklist_id = $checklist_id;
                $add_checklist_items -> Listing_ID = $Listing_ID;
                $add_checklist_items -> Contract_ID = $Contract_ID;
                $add_checklist_items -> Agent_ID = $Agent_ID;
                $add_checklist_items -> checklist_form_id = $item -> checklist_form_id;
                $add_checklist_items -> checklist_item_required = $item -> checklist_item_required;
                $add_checklist_items -> checklist_item_group_id = $item -> checklist_item_group_id;
                $add_checklist_items -> checklist_item_order = $item -> checklist_item_order;
                if($item -> checklist_item_required == 'yes') {
                    $add_checklist_items -> checklist_item_status = 'Required';
                }

                $add_checklist_items -> save();

            }

        }
    }
}
