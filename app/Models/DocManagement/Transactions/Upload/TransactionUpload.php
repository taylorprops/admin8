<?php

namespace App\Models\DocManagement\Transactions\Upload;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;

class TransactionUpload extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_uploads';
    protected $primaryKey = 'file_id';
    protected $guarded = [];

    public function scopeFormGroupFiles($query, $location_id, $Listing_ID, $type) {

        $forms_available = $this -> where('form_group_id', $location_id)
            -> where('published', 'yes')
            -> orderBy('file_name_display', 'ASC') -> get();

        $forms_in_use = null;

        if($type != '') {
            $field = 'Listing_ID';
            if($type == 'contract') {
                $field = 'Contract_ID';
            }
            if($id) {
                $trash_folder = TransactionDocumentsFolders::where($field, $id) -> where('folder_name', 'Trash') -> first();
                $forms_in_use = TransactionDocuments::select('orig_file_id')
                    -> where($field, $id)
                    -> where('orig_file_id', '>', '0')
                    -> where('folder', '!=', $trash_folder -> id)
                    -> pluck('orig_file_id');
            }
        }

        return compact('forms_available', 'forms_in_use');

    }


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
