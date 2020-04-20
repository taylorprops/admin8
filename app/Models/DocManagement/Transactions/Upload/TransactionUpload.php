<?php

namespace App\Models\DocManagement\Transactions\Upload;

use Illuminate\Database\Eloquent\Model;

class TransactionUpload extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_uploads';
    protected $primaryKey = 'file_id';


    public function scopeGetFormName($query, $form_id) {
        if($form_id) {
            $form_name = $query -> where('file_id', $form_id) -> first();
            return $form_name -> file_name_display;
        }
        return  true;
    }

    public function scopeGetFormLocation($query, $form_id) {
        $form_name = $query -> where('file_id', $form_id) -> first();
        if($form_name -> file_location) {
            return $form_name -> file_location;
        }
        return '';
    }
}
