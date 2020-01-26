<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\DocManagement\Upload;

class ResourceItems extends Model
{
    public $table = 'docs_resource_items';
    protected $primaryKey = 'resource_id';
    public $timestamps = false;

    public function getCountFormGroup($id) {
        $uploads = Upload::where('form_group_id', $id) -> get() -> count();
        return $uploads;
    }

    public function scopeGetLocation($query, $id) {
        $location = $query -> where('resource_id', $id) -> first();
        return $location -> resource_name;
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
