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
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;

use App\Models\DocManagement\Transactions\Listings\Listings;

use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;
use Illuminate\Filesystem\Filesystem;


class TransactionsEditFilesController extends Controller
{

    public function file_view(Request $request) {

        $document_id = $request -> document_id;
        $document = TransactionDocuments::whereId($document_id) -> first();
        $file_type = $document -> file_type;
        $file_id = $document -> file_id;
        $Listing_ID = $document -> Listing_ID ?? 0;
        $Contract_ID = $document -> Contract_ID ?? 0;
        $Referral_ID = $document -> Referral_ID ?? 0;
        $transaction_type = $document -> transaction_type;
        $Agent_ID = $document -> Agent_ID;

        /* if($transaction_type == 'listing') {
            $property = Listings::find($Listing_ID);
        } else if($transaction_type == 'contract') {
            $property = Contracts::find($Contract_ID);
        } else if($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        } */
        $common_fields = new CommonFields();

        $file = TransactionUpload::where('file_id', $file_id) -> first();
        $file_name = $file -> file_name_display;
        $images = TransactionUploadImages::where('file_id', $file_id) -> orderBy('page_number') -> get();

        $fields_user = UserFields::where('file_id', $file_id) -> orderBy('id') -> get();
        $fields_user_inputs = UserFieldsInputs::where('file_id', $file_id) -> orderBy('id') -> get();
        $field_values = UserFieldsValues::where('file_id', $file_id) -> get();

        return view('/agents/doc_management/transactions/edit_files/file', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'Agent_ID', 'file', 'file_name', 'images', 'fields_user', 'fields_user_inputs', 'file_id', 'document_id', 'field_values', 'file_type', 'common_fields'));

    }

    public function save_field_input_values(Request $request) {

        // delete all field input values for this file
        $file_id = $request[0]['file_id'];
        $file_type = $request[0]['file_type'];
        $Listing_ID = $request[0]['Listing_ID'] ?? 0;
        $Contract_ID = $request[0]['Contract_ID'] ?? 0;
        $Referral_ID = $request[0]['Referral_ID'] ?? 0;
        $transaction_type = $request[0]['transaction_type'];
        $Agent_ID = $request[0]['Agent_ID'];

        if($transaction_type == 'listing') {
            $column = 'Listing_ID';
            $id = $Listing_ID;
        } else if($transaction_type == 'contract') {
            $column = 'Contract_ID';
            $id = $Contract_ID;
        } else if($transaction_type == 'referral') {
            $column = 'Referral_ID';
            $id = $Referral_ID;
        }

        $delete_filled_fields = UserFieldsValues::where('Agent_ID', $Agent_ID)
            -> where('file_id', $file_id)
            -> where($column, $id)
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
            $filled_fields -> Referral_ID = $Referral_ID;
            $filled_fields -> transaction_type = $transaction_type;
            $filled_fields -> input_id = $field['input_id'];
            $filled_fields -> input_value = $field['input_value'];
            $filled_fields -> save();

            if($field['common_name'] != '') {
                // update all common fields
                $common_fields = UserFieldsValues::where('Agent_ID', $Agent_ID)
                -> where($column, $id)
                -> where('common_name', $field['common_name']) -> first();

                $common_fields -> input_value = $field['input_value'];
                $common_fields -> save();
            }
        }

    }

    public function convert_to_pdf(Request $request) {

        $time = [];

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $file_id = $request -> file_id;
        $file_type = $request -> file_type;

        $path = [
            'listing' => 'listings/'.$Listing_ID,
            'contract' => 'contracts/'.$Contract_ID,
            'referral' => 'referrals/'.$Referral_ID
        ][$transaction_type];

        $upload_dir = 'doc_management/transactions/' . $path . '/' . $file_id . '_'.$file_type;

        Storage::disk('public') -> makeDirectory($upload_dir . '/combined/');
        Storage::disk('public') -> makeDirectory($upload_dir . '/layers/');
        $full_path_dir = Storage::disk('public') -> path($upload_dir);
        $pdf_output_dir = Storage::disk('public') -> path($upload_dir . '/combined/');

        // get file name to use for the final converted file
        $file = glob($full_path_dir.'/converted/*pdf');
        $filename = basename($file[0]);

        // create or clear out directories if they already exist
        $clean_dir = new Filesystem;
        $clean_dir -> cleanDirectory('storage/' . $upload_dir . '/layers');
        $clean_dir -> cleanDirectory('storage/' . $upload_dir . '/combined');
        $clean_dir -> cleanDirectory('storage/' . $upload_dir . '/converted');

        $options = array(
            'binary' => '/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf',
            'no-outline',
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'page-size' => 'Letter',
            'encoding' => 'UTF-8',
            'dpi' => 96,
            'disable-smart-shrinking',
            'tmpDir' => '/var/www/tmp'
        );

        for ($c = 1; $c <= $request['page_count']; $c++) {

            $html = preg_replace('/\>[\s]+\</', '><', $request['page_' . $c]);

            $pdf = new Pdf($options);
            $pdf -> addPage($html);

            if (!$pdf -> saveAs($full_path_dir . '/layers/layer_' . $c . '.pdf')) {
                $error = $pdf -> getError();
            }

            // merge layers from pages folder and layers folder and dump in combined folder
            $page_number = $c;

            if (strlen($c) == 1) {
                $page_number = '0' . $c;
            }

            $layer1 = $full_path_dir . '/pages/page_' . $page_number . '.pdf';
            $layer2 = $full_path_dir . '/layers/layer_' . $c . '.pdf';

            // remove background from both layers
            exec('convert -quality 100 -density 300 ' . $layer2 . ' -transparent white -background none ' . $layer2);

            // merge layers
            exec('pdftk ' . $layer1 . ' background ' . $layer2 . ' output ' . $pdf_output_dir . '/' . date('YmdHis') . '_combined_' . $c . '.pdf');
            //exec('pdftk ' . $layer2 . ' background ' . $layer1 . ' output ' . $pdf_output_dir . '/' . date('YmdHis') . '_combined_' . $c . '.pdf');

        }

        // merge all from combined and add final to converted - named $filename
        exec('pdftk '.$full_path_dir.'/combined/*pdf cat output '.$full_path_dir.'/converted/'.$filename);

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $image_filename = str_replace('.pdf', '.jpg', $filename);
        $source = $full_path_dir.'/converted/'.$filename;
        $destination = $full_path_dir.'/converted_images';
        $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $file_id);


    }

    public function rotate_document(Request $request) {
        $file_id = $request -> file_id;
        $file_type = $request -> file_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $path = [
            'listing' => 'listings/'.$Listing_ID,
            'contract' => 'contracts/'.$Contract_ID,
            'referral' => 'referrals/'.$Referral_ID
        ][$transaction_type];

        $folder = 'public/doc_management/transactions/' . $path . '/' . $file_id.'_'.$file_type.'/';
        $files = Storage::allFiles($folder);

        foreach($files as $file) {
            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            $file = str_replace('public', $doc_root.'/storage', $file);
            exec('mogrify -rotate "90" /'.$file.' 2>&1', $output);
        }

    }

    public function get_user_fields(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $Agent_ID = $request -> Agent_ID;
        $file_id = $request -> file_id;
        $file_type = $request -> file_type;

        if($transaction_type == 'listing') {
            $field = 'Listing_ID';
            $id = $Listing_ID;
        } else if($transaction_type == 'contract') {
            $field = 'Contract_ID';
            $id = $Contract_ID;
        } else if($transaction_type == 'referral') {
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }

        $user_fields = UserFields::where($field, $id) -> where('Agent_ID', $Agent_ID) -> where('file_id', $file_id) -> get();

        return response() -> json($user_fields);

    }

    public function save_edit_options(Request $request) {

        $data = json_decode($request['data'], true);

        $file_type = $data[0]['file_type'];
        $file_id = $data[0]['file_id'];

        if(isset($file_id)) {

            // delete all fields for this document - user fields only
            $delete_fields = UserFields::where('file_id', $file_id) -> where('field_inputs', 'no') -> delete();

            // add fields
            foreach($data as $field) {
                $new_fields = new UserFields;
                foreach($field as $key => $val) {
                    $new_fields -> $key = $val;
                }
                $new_fields -> file_type = 'user';
                $new_fields -> field_inputs = 'no';
                $new_fields -> save();
            }

        }
    }

}
