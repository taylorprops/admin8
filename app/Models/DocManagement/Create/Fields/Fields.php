<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_fields';
    public $timestamps = false;
    protected $guarded = [];
}
