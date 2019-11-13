<?php

namespace App\Http\Controllers\DocManagement\Fill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CommonFields;
use App\Fields;
use App\FieldTypes;
use App\FieldInputs;
use App\Upload;

class FieldsController extends Controller
{
    public function get_common_fields(Request $request) {
        return CommonFields::getCommonFields();
    }


    public function add_fields(Request $request) {

        $files = Upload::whereFileId($request -> file_id) -> orderBy('page_number') -> get() -> toArray();
        $fields = Fields::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();
        $common_fields = CommonFields::getCommonFields();
        $field_types = FieldTypes::select('field_type') -> get() -> toArray();
        $field_inputs = FieldInputs::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();
        return view('/doc_management/create/fields/add_fields', compact('files', 'fields', 'common_fields', 'field_types', 'field_inputs'));

    }

    public function save_fields(Request $request) {

        $data = json_decode($request['data'], true);

        $file_id = $data[0]['file_id'];


        // add new fields
        if(isset($file_id)) {

            // delete all fields for this document
            $delete_docs = Fields::where('file_id', $file_id) -> delete();
            $delete_inputs = FieldInputs::where('file_id', $file_id) -> delete();

            // add fields
            foreach($data as $field) {
                $fields = new Fields;
                foreach($field as $key => $val) {
                    if($key != 'field_data_input' && $key != 'field_data_input_helper_text') {
                        if($key == 'field_name') {
                            $fields -> field_name_display = $val;
                            $val = trim(preg_replace('/\s/', '', $val));
                            $fields -> $key = $val;
                        } else {
                            $fields -> $key = $val;
                        }
                    }
                }
                $fields -> save();
            }

            // add field inputs
            foreach($data as $field) {
                $field_id = $field['field_id'];
                $input_names = $field['field_data_input'];
                $input_names_helper_text = $field['field_data_input_helper_text'];

                for($i = 0; $i < count($input_names); $i++) {
                    $field_inputs = new FieldInputs;
                    $field_inputs -> input_name = $input_names[$i];
                    $field_inputs -> input_helper_text = $input_names_helper_text[$i];
                    $field_inputs -> file_id = $file_id;
                    $field_inputs -> field_id = $field_id;
                    $field_inputs -> save();
                }

            }
        }

    }

    public function fillable_files(Request $request) {

        $files = Upload::select('file_name_orig', 'file_id') -> groupBy('file_id', 'file_name_orig') -> get() -> toArray();
        return view('/doc_management/fill/fillable_files', ['files' => $files]);

    }

    public function fill_fields(Request $request) {

        $files = Upload::whereFileId($request -> file_id) -> orderBy('page_number') -> get() -> toArray();
        $fields = Fields::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();
        $field_inputs = FieldInputs::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();
        return view('/doc_management/fill/fill_fields', compact('files', 'fields', 'field_inputs'));

    }
}

