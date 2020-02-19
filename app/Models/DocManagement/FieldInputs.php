<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class FieldInputs extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_fields_inputs';
    public $timestamps = false;
}
