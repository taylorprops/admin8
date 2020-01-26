<?php

namespace App\Http\Controllers\DocManagement\Checklists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\ResourceItems;
use App\Models\DocManagement\Upload;

class ChecklistsController extends Controller
{
    public function get_checklist_item_details(Request $request) {
        $details = ChecklistsItems::where('checklist_form_id', $request -> form_id) -> first();
        return $details ?? null;
    }

    public function get_checklist_items(Request $request) {
        $checklist_id = $request -> checklist_id;
        $checklist = Checklists::whereId($checklist_id) -> first();
        $checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> get();
        $form_groups = ResourceItems::where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();
        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> orderBy('resource_order') -> get();
        $files = new Upload();
        $resource_items = new ResourceItems();
        return view('/doc_management/checklists/get_add_checklist_items_html', compact('checklist', 'checklist_items', 'form_groups', 'files', 'resource_items', 'checklist_groups'));
    }

    public function checklists() {

        $property_types = ResourceItems::where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = ResourceItems::where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();
        $locations = ResourceItems::where('resource_type', 'checklist_locations') -> orderBy('resource_order') -> get();


        return view('/doc_management/checklists/checklists', compact('property_types', 'property_sub_types', 'locations'));

    }

    public function get_checklists(Request $request) {

        $checklist_location_id = $request -> checklist_location_id;
        $checklist_type = $request -> checklist_type;
        // $checklist_type = $request -> checklist_type;
        $checklists = Checklists::where('checklist_location_id', $checklist_location_id) -> orderBy('checklist_order') -> get();
        $checklists_count = count($checklists);

        $checklist_property_types = $checklists -> mapToGroups(function ($item, $key) {
            return [
                $item['checklist_property_type_id'] => [
                    'checklist_id' => $item['id'],
                    'checklist_type' => $item['checklist_type'],
                    'checklist_represent' => $item['checklist_represent'],
                    'checklist_property_type_id' => $item['checklist_property_type_id'],
                    'checklist_property_sub_type_id' => $item['checklist_property_sub_type_id'],
                    'checklist_sale_rent' => $item['checklist_sale_rent'],
                    'checklist_location_id' => $item['checklist_location_id'],
                    'checklist_state' => $item['checklist_state'],
                    'checklist_order' => $item['checklist_order'],
                    'checklist_count' => $item['checklist_count']
                ]
            ];
        });

        $property_types = ResourceItems::where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();

        $checklist_property_types_items = [];
        foreach($property_types as $property_type) {
            $type = $property_type -> resource_id;
            if($checklist_property_types -> get($type)) {
                $checklist_property_types_items[] = $checklist_property_types -> get($type) -> all();
            }/*  else {
                $checklist_property_types_items[][0]['checklist_property_type_id'] = $type;
            } */
        }
        $resource_items = new ResourceItems();

        return view('/doc_management/checklists/get_checklists_html', compact('checklist_property_types_items', 'checklists_count', 'resource_items', 'checklist_type'));

    }

    public function add_checklist_items(Request $request) {

        $checklist_id = $request -> checklist_id;
        // delete current checklist items
        $delete_checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> delete();

        $checklist_array = json_decode($request -> checklist_items);

        foreach($checklist_array as $checklist_items) {

            $add_checklist_items = new ChecklistsItems();

            $add_checklist_items -> checklist_id = $checklist_id;
            $add_checklist_items -> checklist_form_id = $checklist_items -> checklist_form_id ?? null;
            $add_checklist_items -> checklist_item_required = $checklist_items -> checklist_item_required;
            $add_checklist_items -> checklist_item_group_id = $checklist_items -> checklist_item_group_id;
            $add_checklist_items -> checklist_item_order = $checklist_items -> checklist_item_order;

            $add_checklist_items -> save();

        }

        $checklist_item_count = count($checklist_array);
        $update_count = Checklists::where('id', $checklist_id) -> first();
        $update_count -> checklist_count = $checklist_item_count;
        $update_count -> save();

    }

    public function add_checklist(Request $request) {

        $checklist_property_sub_type_id = 0;
        if($request -> checklist_property_sub_type_id) {
            $checklist_property_sub_type_id = $request -> checklist_property_sub_type_id;
        }
        $checklist = new Checklists();
        $checklist -> checklist_location_id = $request -> checklist_location_id;
        $checklist -> checklist_represent = $request -> checklist_represent;
        $checklist -> checklist_type = $request -> checklist_type;
        $checklist -> checklist_sale_rent = $request -> checklist_sale_rent;
        $checklist -> checklist_property_type_id = $request -> checklist_property_type_id;
        $checklist -> checklist_property_sub_type_id = $checklist_property_sub_type_id;
        $checklist -> checklist_state = $request -> checklist_state;
        $checklist -> checklist_order = 0;
        $checklist -> save();
    }

    public function edit_checklist(Request $request) {
        $checklist = Checklists::where('id', $request -> checklist_id) -> first();
        $checklist -> checklist_location_id = $request -> checklist_location_id;
        $checklist -> checklist_represent = $request -> checklist_represent;
        $checklist -> checklist_type = $request -> checklist_type;
        $checklist -> checklist_sale_rent = $request -> checklist_sale_rent;
        $checklist -> checklist_property_type_id = $request -> checklist_property_type_id;
        $checklist -> checklist_property_sub_type_id = $request -> checklist_property_sub_type_id;
        $checklist -> checklist_state = $request -> checklist_state;
        $checklist -> save();
    }

    public function delete_checklist(Request $request) {
        $checklist_id = $request -> checklist_id;
        if($checklist_id) {
            $checklist = Checklists::where('id', $checklist_id) -> delete();
            $checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> delete();
        }
    }

    public function reorder_checklists(Request $request) {

        $data = json_decode($request['data'], true);
        $data = $data['checklist'];

        foreach($data as $item) {
            $checklist_id = $item['checklist_id'];
            $checklist_order = $item['checklist_index'];
            $reorder = Checklists::whereId($checklist_id) -> first();
            $reorder -> checklist_order = $checklist_order;
            $reorder -> save();
        }

    }
}
