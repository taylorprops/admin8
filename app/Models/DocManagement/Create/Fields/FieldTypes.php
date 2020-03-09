<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class FieldTypes extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_field_types';
    public $timestamps = false;
}
