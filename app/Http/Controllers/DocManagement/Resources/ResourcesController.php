<?php

namespace App\Http\Controllers\DocManagement\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Zips;
use App\Models\DocManagement\ResourceItems;


class ResourcesController extends Controller
{

    public function resources() {
        $states = Zips::States();
        $resources = ResourceItems::groupBy('resource_type') -> get();
        $resources_items = ResourceItems::orderBy('resource_order') -> get();
        return view('/doc_management/resources/resources', compact('resources', 'resources_items', 'states'));
    }

    public function resources_reorder(Request $request) {

        $data = json_decode($request['data'], true);
        $data = $data['resource'];

        foreach($data as $item) {
            $resource_id = $item['resource_id'];
            $resource_order = $item['resource_index'];
            $reorder = ResourceItems::whereResourceId($resource_id) -> first();
            $reorder -> resource_order = $resource_order;
            $reorder -> save();
        }

    }

    public function resources_add(Request $request) {

        $resource_type = $request -> resource_type;
        $resource_type_title = ucwords(str_replace('_', ' ', $resource_type));
        $resource_name = $request -> resource_name;
        $resource_state = $request -> resource_state;
        $resource_color = $request -> resource_color;
        $resource_association = $request -> resource_association;
        $resource_addendums = $request -> resource_addendums;
        $resource_county_abbr = $request -> resource_county_abbr;

        // get default values from existing
        // $defaults = ResourceItems::whereResourceType($resource_type) -> first();
        // $resource_type_title = $defaults -> resource_type_title;

        $resource_item = new ResourceItems();
        $resource_item -> resource_type = $resource_type;
        $resource_item -> resource_type_title = $resource_type_title;
        $resource_item -> resource_name = $resource_name;
        $resource_item -> resource_state = $resource_state;
        $resource_item -> resource_color = $resource_color;
        $resource_item -> resource_association = $resource_association;
        $resource_item -> resource_addendums = $resource_addendums;
        $resource_item -> resource_county_abbr = $resource_county_abbr;
        $resource_item -> resource_order = 0;
        $resource_item -> save();
        return $resource_item -> resource_id;

    }

    public function resources_edit(Request $request) {

        $resource_item = ResourceItems::whereResourceId($request -> resource_id) -> first();
        $resource_item -> resource_name = $request -> resource_name;
        $resource_item -> resource_state = $request -> resource_state;
        $resource_item -> resource_color = $request -> resource_color;
        $resource_item -> resource_association = $request -> resource_association;
        $resource_item -> resource_addendums = $request -> resource_addendums;
        $resource_item -> resource_county_abbr = $request -> resource_county_abbr;
        $resource_item -> save();

    }

    public function resources_delete(Request $request) {

        $resource_item = ResourceItems::whereResourceId($request -> resource_id) -> delete();

    }

}
