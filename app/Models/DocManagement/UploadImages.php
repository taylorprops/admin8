<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class UploadImages extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_uploads_images';
}
