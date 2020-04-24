<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\EditFiles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Create\Fields\CommonFields;
use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Create\Fields\FieldTypes;
use App\Models\DocManagement\Create\Fields\FieldInputs;

use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Create\Upload\UploadImages;

use App\Models\DocManagement\Transactions\EditFiles\UserFields;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;

use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;

use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;

use App\Models\DocManagement\Transactions\Listings\Listings;

use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;
use Illuminate\Filesystem\Filesystem;

class EditFilesController extends Controller
{

    public function file_view(Request $request) {

        $document_id = $request -> document_id;
        $document = TransactionDocuments::whereId($document_id) -> first();
        $file_type = $document -> file_type;
        $file_id = $document -> file_id;
        $Listing_ID = $document -> Listing_ID;
        $Agent_ID = $document -> Agent_ID;

        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $common_fields = new CommonFields();

        if($file_type == 'system') {
            $file = Upload::where('file_id', $file_id) -> first();
            $images = UploadImages::where('file_id', $file_id) -> get();
            $fields = Fields::where('file_id', $file_id) -> orderBy('id') -> get();

        } else if($file_type == 'user') {
            $file = TransactionUpload::where('file_id', $file_id) -> first();
            $images = TransactionUploadImages::where('file_id', $file_id) -> get();
            $fields = UserFields::where('file_id', $file_id) -> orderBy('id') -> get();

        }
        $field_inputs_system = FieldInputs::where('file_id', $file_id) -> orderBy('id') -> get();
        $field_inputs_user = UserFieldsInputs::where('file_id', $file_id) -> orderBy('id') -> get();
        $field_values = UserFieldsValues::where('file_id', $file_id) -> where('file_type', $file_type) -> get();

        return view('/agents/doc_management/transactions/edit_files/file', compact('listing', 'Listing_ID', 'Agent_ID', 'file', 'images', 'fields', 'field_inputs_system', 'field_inputs_user', 'file_id', 'document_id', 'field_values', 'file_type', 'common_fields'));

    }

    public function save_field_input_values(Request $request) {

        // delete all field input values for this file
        $file_id = $request[0]['file_id'];
        $file_type = $request[0]['file_type'];
        $Listing_ID = '0';
        $Contract_ID = '0';

        if($request[0]['Listing_ID']) {
            $Listing_ID = $request[0]['Listing_ID'];
        }
        if(isset($request[0]['Contract_ID'])) {
            $Contract_ID = $request[0]['Contract_ID'];
        }


        $Agent_ID = $request[0]['Agent_ID'];

        $delete_filled_fields = UserFieldsValues::where('Agent_ID', $Agent_ID) -> where('file_id', $file_id) -> where('file_type', $file_type)
            -> where(function ($query) use ($Listing_ID, $Contract_ID) {
                if($Listing_ID > 0) {
                    $query -> where('Listing_ID', $Listing_ID);
                } else if($Contract_ID > 0) {
                    $query -> where('Contract_ID', $Contract_ID);
                }
            })
            -> delete();

        $fields = json_decode($request -> getContent(), true);

        foreach($fields as $field) {
            $filled_fields = new UserFieldsValues();
            $filled_fields -> file_id = $file_id;
            $filled_fields -> file_type = $file_type;
            $filled_fields -> common_name = $field['common_name'];
            $filled_fields -> Agent_ID = $Agent_ID;
            $filled_fields -> Listing_ID = $Listing_ID;
            $filled_fields -> Contract_ID = $Contract_ID;
            $filled_fields -> input_id = $field['input_id'];
            $filled_fields -> input_value = $field['input_value'];
            $filled_fields -> save();

            if($field['common_name'] != '') {
                // update all common fields for this listing or contract
                $common_fields = UserFieldsValues::where('Agent_ID', $Agent_ID)
                -> where(function ($query) use ($Listing_ID, $Contract_ID) {
                    if($Listing_ID > 0) {
                        $query -> where('Listing_ID', $Listing_ID);
                    } else if($Contract_ID > 0) {
                        $query -> where('Contract_ID', $Contract_ID);
                    }
                })
                -> where('common_name', $field['common_name']) -> first();

                $common_fields -> input_value = $field['input_value'];
                $common_fields -> save();
            }
        }

    }

    public function convert_to_pdf(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $file_id = $request -> file_id;

        $upload_dir = 'doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_system';

        $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $full_path_dir = $doc_root . 'storage/' . $upload_dir;
        $pdf_output_dir = $doc_root . 'storage/' . $upload_dir . '/combined/';

        // get file name to use for the final converted file
        $file = glob($full_path_dir.'/converted/*pdf');
        $filename = basename($file[0]);

        // create or clear out directories if they already exist
        $clean_dir = new Filesystem;
        $clean_dir -> cleanDirectory('storage/' . $upload_dir . '/layers');
        $clean_dir -> cleanDirectory('storage/' . $upload_dir . '/combined');
        $clean_dir -> cleanDirectory('storage/' . $upload_dir . '/converted');

        for ($c = 1; $c <= $request['page_count']; $c++) {
            $options = array(
                'binary' => '/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf',
                'no-outline',
                'margin-top' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
                'disable-smart-shrinking',
                'page-size' => 'A4',
                'encoding' => 'UTF-8',
                'dpi' => 96,
            );

            $pdf = new Pdf($options);
            $pdf -> addPage($request['page_' . $c]);

            if (!$pdf -> saveAs($full_path_dir . '/layers/layer_' . $c . '.pdf')) {
                $error = $pdf -> getError();
                dd($error);
            }

            // merge layers from pages folder and layers folder and dump in combined folder
            $page_number = $c;

            if (strlen($c) == 1) {
                $page_number = '0' . $c;
            }

            $layer1 = $full_path_dir . '/pages/page_' . $page_number . '.pdf';
            $layer2 = $full_path_dir . '/layers/layer_' . $c . '.pdf';
            exec('convert -quality 100 -density 300 ' . $layer2 . ' -transparent white -background none ' . $layer2.' 2>&1', $output);
            exec('pdftk ' . $layer2 . ' background ' . $layer1 . ' output ' . $pdf_output_dir . '/' . date('YmdHis') . '_combined_' . $c . '.pdf 2>&1', $output2);

        }

        // merge all from combined and add final to converted - named $filename
        exec('pdftk '.$full_path_dir.'/combined/*pdf cat output '.$full_path_dir.'/converted/'.$filename);
    }



    public function rotate_document(Request $request) {
        $file_id = $request -> file_id;
        $file_type = $request -> file_type;
        $Listing_ID = $request -> Listing_ID;
        $folder = 'public/doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id.'_'.$file_type.'/';
        $files = Storage::allFiles($folder);

        foreach($files as $file) {
            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            $file = str_replace('public', $doc_root.'/storage', $file);
            exec('mogrify -rotate "90" /'.$file.' 2>&1', $output);
        }

    }

    /* public function save_add_fields(Request $request) {

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

    } */
    /* public function save_pdf_client_side(Request $request) {
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
    }  */
}
