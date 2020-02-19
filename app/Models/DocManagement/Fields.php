<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_fields';
    public $timestamps = false;
}
