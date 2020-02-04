<?php

namespace App\Models\DocManagement\Checklists;

use Illuminate\Database\Eloquent\Model;

class ChecklistsItems extends Model
{
    public $table = 'docs_checklists_items';

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('checklist_item_active', 'yes');
        });
    }

    public function ScopeCountInChecklist($query, $form_id) {
        $found = $query -> where('checklist_form_id', $form_id) -> get();
        if($found) {
            return count($found);
        }
        return 0;
    }

    public function ScopeIfFormInChecklist($query, $checklist_id, $form_id) {
        $found = ChecklistsItems::where('checklist_id', $checklist_id) -> where('checklist_form_id', $form_id) -> first();
        if($found) {
            return true;
        }
        return false;
    }

    public function scopeGetChecklistItemsCount($query, $checklist_id) {
        if($checklist_id) {
            $checklist_items = $query -> where('checklist_id', $checklist_id) -> count();
            return $checklist_items;
        }
        return '0';
    }

    public function updateChecklistItemsOrder($checklist_id) {
        // reset order to start from 0
        $checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> orderBy('checklist_item_order', 'ASC') -> get();
        foreach($checklist_items as $index => $checklist_item) {
            $item = ChecklistsItems::where('id', $checklist_item -> id) -> first();
            $item -> checklist_item_order = $index;
            $item -> save();
        }
    }


}
