<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class CommonFields extends Model
{
    public $table = 'docs_common_fields';
    public $timestamps = false;

    public function scopeGetCommonFields() {
        $common_fields = CommonFields::select('field_name', 'field_type') -> orderBy('field_order', 'ASC') -> get();
        $common_field_types = $common_fields -> mapToGroups(function ($item, $key) {
            return [ $item['field_type'] => [ $item['field_name'] ] ];
        });
        $common_field_types -> toArray();

        $field_types = FieldTypes::select('field_type') -> get() -> toArray();

        $common = array();
        foreach($field_types as $type) {

            if($common_field_types -> get($type['field_type'])) {
                $common[$type['field_type']] = $common_field_types -> get($type['field_type']) -> all();
            }
        }
        return $common;
    }
}
