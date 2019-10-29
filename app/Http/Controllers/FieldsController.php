<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CommonFields;
use App\Fields;
use App\Upload;

class FieldsController extends Controller
{
    public function get_common_fields(Request $request) {
        $fields = CommonFields::select('field_name') -> orderBy('field_name', 'ASC') -> get();
        return (string) $fields;
    }

    public function add_fields(Request $request) {

        $files = Upload::whereFileId($request -> file_id) -> orderBy('page_number') -> get() -> toArray();

        // orderBy('id') is added so last field is selected on add_fields.js line 39 $('.group_' + group_id).find('.field-textline-addline-container').last().show();
        $fields = Fields::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();

        $common_fields = CommonFields::select('field_name') -> orderBy('field_name', 'ASC') -> get();

        return view('/doc_management/fields/add_fields', compact('files', 'fields', 'common_fields'));
    }

    public function save_fields(Request $request) {

        $data = json_decode($request['data'], true);
        // delete all fields for this document
        $file_id = $data[0]['file_id'];
        $delete_docs = Fields::where('file_id', $file_id) -> delete();
        // add new fields
        foreach($data as $field) {
            $fields = new Fields;
            foreach($field as $key => $val) {
                $fields -> $key = $val;
            }
            $fields -> save();
        }

    }
}

