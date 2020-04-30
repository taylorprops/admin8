<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;

class TransactionDocuments extends Model {
    use SoftDeletes;

    public $table = 'docs_transactions_docs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';

    public function ScopeGetDocInfo($query, $document_id) {
        $document = $this -> where('id', $document_id) -> first();
        $file_id = $document -> file_id;
        $file_type = $document -> file_type;
        if($file_type == 'system') {
            $document = Upload::where('file_id', $file_id) -> first();
        } else {
            $document = TransactionUpload::where('file_id', $file_id) -> first();
        }
        $file_name = $document -> file_name_display;
        $file_location = $document -> file_location;

        return compact('file_name', 'file_location');
    }


}
