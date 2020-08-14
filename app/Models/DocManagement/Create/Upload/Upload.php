<?php

namespace App\Models\DocManagement\Create\Upload;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;

class Upload extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_uploads';
    protected $primaryKey = 'file_id';
    protected $guarded = [];


    public function scopeGetFormCount($query, $location_id) {

        $form_count = $this -> where('form_group_id', $location_id) -> count();
        return compact('form_count');

    }

    public function scopeFormGroupFiles($query, $location_id, $Listing_ID, $Contract_ID, $type) {

        $forms_available = $this -> where('form_group_id', $location_id)
            -> where('published', 'yes')
            -> orderBy('file_name_display', 'ASC') -> get();

        //$forms_in_use = null;

        //if($type != '') {
            /* $field = 'Listing_ID';
            if($type == 'contract') {
                $field = 'Contract_ID';
            } */
            /* if($Contract_ID > 0 || $Listing_ID > 0) {
                $trash_folder = TransactionDocumentsFolders::where(function($query) use ($Contract_ID, $Listing_ID) {
                    $query -> where(function($q) use ($Listing_ID) {
                        $q -> where('Listing_ID', $Listing_ID) -> where('Listing_ID', '>', '0');
                    })
                    -> orWhere(function($q) use ($Contract_ID) {
                        $q -> where('Contract_ID', $Contract_ID) -> where('Contract_ID', '>', '0');
                    });
                })
                -> where('folder_name', 'Trash') -> first(); */


                /* $forms_in_use = TransactionDocuments::select('orig_file_id')
                    -> where($field, $id)
                    -> where('orig_file_id', '>', '0')
                    -> where('folder', '!=', $trash_folder -> id)
                    -> pluck('orig_file_id'); */
            //}
        //}

        return compact('forms_available');

    }


    public function scopeGetFormName($query, $form_id) {
        if($form_id) {
            $form_name = $query -> where('file_id', $form_id) -> first();
            if($form_name) {
                return $form_name -> file_name_display;
            }
            return true;
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
