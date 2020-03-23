<?php

namespace App\Http\Controllers\DocManagement\Fill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Create\Fields\CommonFields;
use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Create\Fields\FieldTypes;
use App\Models\DocManagement\Create\Fields\FieldInputs;
use App\Models\DocManagement\Create\Fields\FilledFields;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;
use Illuminate\Filesystem\Filesystem;

class FieldsController extends Controller
{
    public function get_common_fields(Request $request) {
        return CommonFields::getCommonFields();
    }


    public function add_fields(Request $request) {

        $file = Upload::whereFileId($request -> file_id) -> get();
        $images = UploadImages::whereFileId($request -> file_id) -> get() -> toArray();
        $fields = Fields::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();
        $common_fields = CommonFields::getCommonFields();
        $field_types = FieldTypes::select('field_type') -> get() -> toArray();
        $field_inputs = FieldInputs::where('file_id', $request -> file_id) -> orderBy('id') -> get() -> toArray();
        return view('/doc_management/create/fields/add_fields', compact('file', 'images', 'fields', 'common_fields', 'field_types', 'field_inputs'));

    }

    public function save_add_fields(Request $request) {

        $data = json_decode($request['data'], true);

        $file_id = $data[0]['file_id'];

        // add new fields
        if(isset($file_id)) {

            // delete all fields for this document
            $delete_docs = Fields::where('file_id', $file_id) -> delete();
            $delete_inputs = FieldInputs::where('file_id', $file_id) -> delete();

            if(!empty($data[0]['field_id'])) {

                // remove input fields, they are added next
                $ignore_fields = ['field_data_input', 'field_data_input_helper_text', 'field_data_input_id'];
                // add fields
                foreach($data as $field) {
                    $fields = new Fields;
                    foreach($field as $key => $val) {
                        if(!in_array($key, $ignore_fields)) {
                            // keep field name in readable format and as name/id
                            if($key == 'field_name') {
                                $fields -> field_name_display = $val;
                                $val = trim(preg_replace('/\s/', '', $val));
                            }
                            $fields -> $key = $val;
                        }
                    }
                    $fields -> save();
                }

                // add field inputs
                foreach($data as $field) {
                    $field_id = $field['field_id'];
                    $input_ids = $field['field_data_input_id'];
                    $input_names = $field['field_data_input'];
                    $input_names_helper_text = $field['field_data_input_helper_text'];

                    for($i = 0; $i < count($input_names); $i++) {
                        $field_inputs = new FieldInputs;
                        $field_inputs -> input_id = $input_ids[$i];
                        $field_inputs -> input_name = $input_names[$i];
                        $field_inputs -> input_helper_text = $input_names_helper_text[$i];
                        $field_inputs -> file_id = $file_id;
                        $field_inputs -> field_id = $field_id;
                        $field_inputs -> save();
                    }

                }

            }

        }

    }

    public function fillable_files(Request $request) {

        $files = Upload::select('file_name_orig', 'file_id') -> groupBy('file_id', 'file_name_orig') -> get() -> toArray();
        return view('/doc_management/fill/fillable_files', ['files' => $files]);

    }

    public function fill_fields(Request $request) {

        $file_id = $request -> file_id;
        $file = Upload::whereFileId($file_id) -> get();
        $images = UploadImages::whereFileId($request -> file_id) -> get() -> toArray();
        $fields = Fields::where('file_id', $file_id) -> orderBy('id') -> get() -> toArray();
        $field_inputs = FieldInputs::where('file_id', $file_id) -> orderBy('id') -> get() -> toArray();
        $field_values = FilledFields::where('file_id', $file_id) -> get() -> toArray();
        // $states = LocationData::ActiveStates();
        //$file_id = $file_id;
        return view('/doc_management/fill/fill_fields', compact('file', 'images', 'fields', 'field_inputs', 'file_id', 'field_values'));

    }

    public function save_fill_fields(Request $request) {
        // delete all field input values for this file
        $file_id = $request[0]['file_id'];
        $delete_filled_fields = FilledFields::where('file_id', $file_id) -> delete();

        $fields = json_decode($request -> getContent(), true);
        foreach($fields as $field) {
            $filled_fields = new FilledFields();
            $filled_fields -> file_id = $file_id;
            $filled_fields -> input_id = $field['input_id'];
            $filled_fields -> input_value = $field['input_value'];
            $filled_fields -> save();
        }


    }

    public function save_pdf_client_side(Request $request) {
        if($request) {

            $file_id = $request['file_id'];

            $upload_dir = 'doc_management/uploads/' . $file_id;
            // create or clear out directories if they already exist
            $clean_dir = new Filesystem;
            if (!Storage::disk('public') -> exists($upload_dir . '/layers')) {
                Storage::disk('public') -> makeDirectory($upload_dir . '/layers');
            } else {
                $clean_dir -> cleanDirectory('storage/'.$upload_dir . '/layers');
            }
            if (!Storage::disk('public') -> exists($upload_dir . '/combined')) {
                Storage::disk('public') -> makeDirectory($upload_dir . '/combined');
            } else {
                $clean_dir -> cleanDirectory('storage/'.$upload_dir . '/combined');
            }

            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            $full_path_dir = $doc_root . 'storage/'.$upload_dir;

            $pdf_output_dir = $doc_root . 'storage/'.$upload_dir.'/combined/';

            for($c = 1; $c <= $request['page_count']; $c++) {
                $options = array(
                    'binary' => '/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf',
                    'no-outline',
                    'margin-top'    => 0,
                    'margin-right'  => 0,
                    'margin-bottom' => 0,
                    'margin-left'   => 0,
                    //'disable-smart-shrinking',
                    'page-size' => 'Letter',
                    'encoding' => 'UTF-8',
                    'dpi' => 96,
                );

                $pdf = new Pdf($options);
                $pdf -> addPage($request['page_'.$c]);
                if (!$pdf -> saveAs($full_path_dir.'/layers/layer_'.$c.'.pdf')) {
                    $error = $pdf -> getError();
                    dd($error);
                }

                // merge layers from pages folder and layers folder and dump in combined folder
                $page_number = $c;
                if(strlen($c) == 1) {
                    $page_number = '0'.$c;
                }
                $layer1 = $full_path_dir . '/pages/page_'.$page_number.'.pdf';
                $layer2 = $full_path_dir . '/layers/layer_'.$c.'.pdf';
                exec('convert -quality 100 -density 300 '.$layer2.' -transparent white -background none '.$layer2);
                exec('pdftk '.$layer2.' background '.$layer1.' output '.$pdf_output_dir.'/'.date('YmdHis').'_combined_'.$c.'.pdf');

            }

        }
    }




}

