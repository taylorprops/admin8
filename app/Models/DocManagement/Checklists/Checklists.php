<?php

namespace App\Models\DocManagement\Checklists;

use Illuminate\Database\Eloquent\Model;

class Checklists extends Model
{
    public $table = 'docs_checklists';

    public function scopeGetChecklistsByPropertyType($query, $checklist_property_type_id, $checklist_type) {
        $checklists = $query -> where('checklist_property_type_id', $checklist_property_type_id) -> where('checklist_type', $checklist_type)
        -> orderBy('checklist_represent', 'DESC')
        -> orderBy('checklist_sale_rent', 'DESC')
        -> orderBy('checklist_property_type_id', 'ASC') -> get();
        return $checklists;
    }

}
