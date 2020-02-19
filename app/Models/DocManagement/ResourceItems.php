<?php

namespace App\Models\DocManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocManagement\Upload;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;


class ResourceItems extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_resource_items';
    protected $primaryKey = 'resource_id';
    public $timestamps = false;

    // allow only active records on all queries
    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('resource_active', 'yes');
        });
    }

    public function scopeIsResourceInUse($query, $resource_id) {

        $checklist_property_type_ids = Checklists::select('checklist_property_type_id') -> get() -> pluck('checklist_property_type_id') -> toArray();
        $checklist_property_sub_type_ids = Checklists::select('checklist_property_sub_type_id') -> get() -> pluck('checklist_property_sub_type_id') -> toArray();
        $checklist_location_ids = Checklists::select('checklist_location_id') -> get() -> pluck('checklist_location_id') -> toArray();
        $checklist_item_group_ids = ChecklistsItems::select('checklist_item_group_id') -> get() -> pluck('checklist_item_group_id') -> toArray();
        $form_group_ids = Upload::select('form_group_id') -> get() -> pluck('form_group_id') -> toArray();
        $sale_type_list = Upload::select('sale_type') -> get() -> pluck('sale_type') -> toArray();
        $sale_type_ids = [];
        foreach($sale_type_list as $sale_type) {
            $sale_type_array = explode(',', $sale_type);
            foreach($sale_type_array as $sale_type_item) {
                $sale_type_ids[] = $sale_type_item;
            }
        }
        $all_ids = array_merge($checklist_property_type_ids, $checklist_property_sub_type_ids, $checklist_location_ids, $checklist_item_group_ids, $form_group_ids, $sale_type_ids);
        $resource_ids_in_use = array_unique($all_ids);
        if(in_array($resource_id, $resource_ids_in_use)) {
            return true;
        }
        return false;

    }


    public function getCountFormGroup($id) {
        $uploads = Upload::where('form_group_id', $id) -> get() -> count();
        return $uploads;
    }

    public function scopeGetLocation($query, $id) {
        $location = $query -> where('resource_id', $id) -> first();
        return $location -> resource_name;
    }

    public function scopeGetState($query, $resource_id) {
        $location = $query -> where('resource_id', $resource_id) -> first();
        return $location -> resource_state;
    }

    public function scopeGetTagName($query, $id) {
        if($id) {
            $tags = $query -> where('resource_id', $id) -> first();
            return $tags -> resource_name;
        }
        return false;
    }

    public function scopeGetTagColor($query, $id) {
        $tags = $query -> where('resource_id', $id) -> first();
        return $tags -> resource_color;
    }


}
