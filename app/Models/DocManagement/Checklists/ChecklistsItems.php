<?php

namespace App\Models\DocManagement\Checklists;

use Illuminate\Database\Eloquent\Model;

class ChecklistsItems extends Model
{
    public $table = 'docs_checklists_items';

    public function ScopeInChecklist($query, $form_id) {
        $found = $query -> where('checklist_form_id', $form_id) -> get();
        if($found) {
            return count($found);
        }
        return 0;
    }

    public function scopeGetChecklistItems($query, $checklist_id) {
        $checklist_items = $query -> where('checklist_id', $checklist_id) -> orderBy('checklist_item_order', 'ASC') -> get();
        return $checklist_items;
    }


}
