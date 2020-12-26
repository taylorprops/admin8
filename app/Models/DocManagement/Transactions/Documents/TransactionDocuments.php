<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDocuments extends Model
{
    use SoftDeletes;

    public $table = 'docs_transactions_docs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetDocInfo($query, $document_id)
    {
        $document = $this->where('id', $document_id)->first();
        $file_name = $document->file_name_display;
        $file_location_converted = $document->file_location_converted;

        return compact('file_name', 'file_location_converted');
    }
}
