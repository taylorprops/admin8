<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Upload extends Model
{
    public $table = 'docs_uploads';
    protected $primaryKey = 'file_id';

}
