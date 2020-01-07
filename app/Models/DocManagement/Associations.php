<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\DocManagement\Upload;

class Associations extends Model
{
    public $table = 'docs_associations';
    public $timestamps = false;

    public function scopeGetAssociations() {
        $associations = Associations::orderBy('association') -> get();
        return $associations;
    }

    public function getCountAssociationForms($id) {
        $uploads = Upload::where('form_group_id', $id) -> get() -> count();
        return $uploads;
    }
}
