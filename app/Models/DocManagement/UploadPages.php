<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class UploadPages extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_uploads_pages';
}
