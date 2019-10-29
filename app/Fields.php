<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fields extends Model
{
    public $table = 'docs_fields';
    //use SoftDeletes;
}
