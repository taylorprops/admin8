<?php

namespace App\Models\DocManagement\Transactions\Listings\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Listings\Checklists\ListingChecklistItems;
use App\Models\DocManagement\Checklists\ChecklistsItems;

class ListingChecklists extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_listing';
    protected $primaryKey = 'id';

    public function ScopeCreateListingChecklist($request, $checklist_id, $Listing_ID, $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id) {



        $where = [
            ['checklist_represent', $checklist_represent],
            ['checklist_type', $checklist_type],
            ['checklist_property_type_id', $checklist_property_type_id],
            ['checklist_property_sub_type_id', $checklist_property_sub_type_id],
            ['checklist_sale_rent', $checklist_sale_rent],
            ['checklist_state', $checklist_state],
            ['checklist_location_id', $checklist_location_id]
        ];

        $checklist = Checklists::where($where) -> first();
        $checklist_items = new ChecklistsItems();
        $items = $checklist_items -> where('checklist_id', $checklist -> id) -> orderBy('checklist_item_order') -> get();

        $remove_ids = [];
        $keep_form_ids = [];

        if($checklist_id) {

            $existing_checklist = ListingChecklists::find($checklist_id);
            $existing_checklist -> checklist_id = $checklist -> id;
            $existing_checklist -> Listing_ID = $Listing_ID;
            $existing_checklist -> Agent_ID = $Agent_ID;
            $existing_checklist -> save();

            $existing_items = ListingChecklistItems::where('checklist_id', $checklist_id) -> get();

            // remove items from checklist no longer needed
            $new_form_ids = [];
            foreach($items as $item) {
                $new_form_ids[] = $item -> checklist_form_id;
            }

            foreach($existing_items as $existing_item) {
                if(in_array($existing_item -> checklist_form_id, $new_form_ids)) {
                    $keep_form_ids[] = $existing_item -> checklist_form_id;
                } else {
                    $remove_ids[] = $existing_item -> id;
                }
            }

            if(count($remove_ids) > 0) {
                $remove_items = ListingChecklistItems::whereIn('id', $remove_ids) -> delete();
            }

            // TODO: remove items docs too

        } else {

            $add_checklist = new ListingChecklists();
            $add_checklist -> checklist_id = $checklist -> id;
            $add_checklist -> Listing_ID = $Listing_ID;
            $add_checklist -> Agent_ID = $Agent_ID;
            $add_checklist -> save();
            $checklist_id = $add_checklist -> id;

        }

        foreach($items as $item) {

            if(!in_array($item -> checklist_form_id, $keep_form_ids)) {
                $add_checklist_items = new ListingChecklistItems();
                $add_checklist_items -> checklist_id = $checklist_id;
                $add_checklist_items -> Listing_ID = $Listing_ID;
                $add_checklist_items -> Agent_ID = $Agent_ID;
                $add_checklist_items -> checklist_form_id = $item -> checklist_form_id;
                $add_checklist_items -> checklist_item_required = $item -> checklist_item_required;
                $add_checklist_items -> checklist_item_group_id = $item -> checklist_item_group_id;
                $add_checklist_items -> checklist_item_order = $item -> checklist_item_order;

                $add_checklist_items -> save();
            }

        }
    }
}
