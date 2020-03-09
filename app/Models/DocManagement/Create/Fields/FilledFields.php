<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class FilledFields extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_filled_fields_values';
    public $timestamps = false;
}
