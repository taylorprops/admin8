<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class CommonFieldsSubGroups extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_common_fields_sub_groups';
    public $timestamps = false;
    protected $guarded = [];
}
