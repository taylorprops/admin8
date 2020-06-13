<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Listings;

use File;
use Config;
use App\User;
use Illuminate\Http\Request;
use App\Models\CRM\CRMContacts;
use App\Models\Employees\Teams;

use App\Models\Employees\Agents;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Create\Fields\FieldInputs;
use App\Models\DocManagement\Create\Upload\UploadPages;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\EditFiles\UserFields;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;

use App\Mail\DocManagement\Emails\Documents;



class ListingDetailsController extends Controller {

    // TABS

    // Checklist Tab
    public function get_checklist(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $Agent_ID = $request -> Agent_ID;
        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_model = new TransactionChecklistItems();
        $transaction_checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $transaction_checklist_item_notes_model = new TransactionChecklistItemsNotes();
        $users_model = new User();

        $transaction_checklist = TransactionChecklists::where('Listing_ID', $Listing_ID) -> first();
        $transaction_checklist_id = $transaction_checklist -> id;
        $original_checklist_id = $transaction_checklist -> checklist_id;
        $transaction_checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $transaction_checklist_year_built = $transaction_checklist -> year_built;

        $checklist = Checklists::where('id', $original_checklist_id) -> first();

        $transaction_checklist_items = $transaction_checklist_items_model -> where('Listing_ID', $Listing_ID) -> where('checklist_id' , $transaction_checklist_id)  -> orderBy('checklist_item_order') -> get();

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', ['listing', 'both']) -> orderBy('resource_order') -> get();

        $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('folder_name', 'Trash') -> first();
        $documents_model = new TransactionDocuments();
        $documents_available = $documents_model -> where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('order') -> get();
        $documents_checklist = $documents_model -> where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('order') -> get();
        $folders = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> where('folder_name', '!=', 'Trash') -> orderBy('order') -> get();

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_checklist', compact('Listing_ID', 'checklist_items_model', 'transaction_checklist', 'transaction_checklist_id',  'transaction_checklist_items', 'transaction_checklist_item_docs_model', 'transaction_checklist_item_notes_model', 'transaction_checklist_items_model','checklist_groups', 'documents_model', 'users_model', 'documents_available', 'documents_checklist', 'folders', 'resource_items', 'property_types', 'property_sub_types', 'checklist', 'transaction_checklist_hoa_condo', 'transaction_checklist_year_built'));
    }

    public function add_document_to_checklist_item(Request $request) {
        $document_id = $request -> document_id;
        $checklist_id = $request -> checklist_id;
        $checklist_item_id = $request -> checklist_item_id;
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID;

        $add_checklist_item_doc = new TransactionChecklistItemsDocs();
        $add_checklist_item_doc -> document_id = $document_id;
        $add_checklist_item_doc -> checklist_id = $checklist_id;
        $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
        $add_checklist_item_doc -> Agent_ID = $Agent_ID;
        $add_checklist_item_doc -> Listing_ID = $Listing_ID;
        $add_checklist_item_doc -> save();

        $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
    }

    public function remove_document_from_checklist_item(Request $request) {
        $document_id = $request -> document_id;
        $checklist_item_doc = TransactionChecklistItemsDocs::where('document_id', $document_id) -> delete();
        $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'no']);
    }

    function add_notes_to_checklist_item(Request $request) {
        $add_notes = new TransactionChecklistItemsNotes();
        $add_notes -> checklist_id = $request -> checklist_id;
        $add_notes -> checklist_item_id = $request -> checklist_item_id;
        $add_notes -> checklist_item_doc_id = $request -> checklist_item_doc_id ?? null;
        $add_notes -> Listing_ID = $request -> Listing_ID;
        $add_notes -> Agent_ID = $request -> Agent_ID;
        $add_notes -> note_user_id = auth() -> user() -> id;
        $add_notes -> note_status = 'unread';
        $add_notes -> notes = $request -> notes;
        $add_notes -> save();
    }

    public function mark_note_read(Request $request) {
        $mark_read = TransactionChecklistItemsNotes::where('id', $request -> note_id) -> update(['note_status' => 'read']);
    }


    public function change_checklist(Request $request) {

        $checklist_id = $request -> checklist_id;
        $Listing_ID = $request -> Listing_ID;
        $Agent_ID = $request -> Agent_ID;

        $transaction_checklist = TransactionChecklists::where('id', $checklist_id) -> first();
        $original_checklist_id = $transaction_checklist -> checklist_id;

        $checklist = Checklists::where('id', $original_checklist_id) -> first();
        $checklist_state = $checklist -> checklist_state;
        $checklist_location_id = $checklist -> checklist_location_id;
        $checklist_sale_rent = $transaction_checklist -> sale_rent;
        $checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $checklist_year_built = $transaction_checklist -> year_built;

        $checklist_property_type_id = ResourceItems::GetResourceID($request -> property_type, 'checklist_property_types');
        $checklist_property_sub_type_id = ResourceItems::GetResourceID($request -> property_sub_type, 'checklist_property_sub_types');

        $checklist_represent = 'seller';
        $checklist_type = 'listing';

        TransactionChecklists::CreateListingChecklist($checklist_id, $Listing_ID, $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        return true;
    }

    // End Checklist Tab

    // Members Tab
    public function get_members(Request $request) {
        $listing = Listings::find($request -> Listing_ID);
        $members = Members::where('Listing_ID', $request -> Listing_ID) -> get();
        $resource_items = new ResourceItems();
        $contact_types = $resource_items -> where('resource_type', 'contact_type') -> whereIn('resource_form_group_type', ['listing', 'both']) -> get();
        $states = LocationData::AllStates();
        $contacts = CRMContacts::where('Agent_ID', $listing -> Agent_ID) -> get();
        return view('/agents/doc_management/transactions/listings/details/data/get_members', compact('members', 'contact_types', 'resource_items', 'states', 'contacts'));
    }

    public function add_member_html(Request $request) {
        $contact_types = ResourceItems::where('resource_type', 'contact_type') -> whereIn('resource_form_group_type', ['listing', 'both']) -> get();
        $states = LocationData::AllStates();
        return view('/agents/doc_management/transactions/listings/details/data/add_member_html', compact('contact_types', 'states'));
    }

    public function delete_member(Request $request) {

        if ($member = Members::find($request -> id) -> delete()) {

            $this -> update_sellers($request -> Listing_ID);

            return response() -> json([
                'status' => 'ok',
            ]);

        }

    }

    public function save_member(Request $request) {

        if ($request -> id && $request -> id != 'undefined') {
            $member = Members::find($request -> id);
        } else {
            $member = new Members();
        }

        $data = $request -> all();

        foreach ($data as $col => $val) {

            if ($col != 'id') {
                $member -> $col = $val ?? null;
            }

        }

        $member -> save();

        $this -> update_sellers($request -> Listing_ID);

        return response() -> json([
            'status' => 'ok',
        ]);
    }
    // End Members Tab

    // Details Tab
    public function get_details(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $agents = Agents::where('active', 'yes') -> orderBy('last_name') -> get();
        $teams = Teams::where('active', 'yes') -> orderBy('team_name') -> get();
        $street_suffixes = config('global.vars.street_suffixes');
        $street_dir_suffixes = config('global.vars.street_dir_suffixes');
        $states = config('global.vars.active_states');
        $listing_state = $listing -> StateOrProvince;
        $counties = LocationData::CountiesByState($listing_state);
        $trans_coords = TransactionCoordinators::where('active', 'yes') -> orderBy('last_name') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_details', compact('Listing_ID', 'listing', 'agents', 'teams', 'street_suffixes', 'street_dir_suffixes', 'states', 'counties', 'trans_coords'));
    }

    public function save_details(Request $request) {
        $listing = Listings::find($request -> Listing_ID);

        // mls needs to be verified. if not MLS_Verified needs to be set to no
        $listing -> MLS_Verified = 'no';

        if (bright_mls_search($request -> ListingId)) {
            $listing -> MLS_Verified = 'yes';
        }

        $data = $request -> all();

        $FullStreetAddress = $data['StreetNumber'].' '.$data['StreetName'].' '.$data['StreetSuffix'];

        if ($data['StreetDirSuffix']) {
            $FullStreetAddress .= ' '.$data['StreetDirSuffix'];
        }

        if ($data['UnitNumber']) {
            $FullStreetAddress .= ' '.$data['UnitNumber'];
        }

        $data['FullStreetAddress'] = $FullStreetAddress;

        foreach ($data as $col => $val) {

            if ($col != 'Listing_ID' && !stristr($col, '_submit')) {

                if ($col == 'ListPrice') {
                    $val = preg_replace('/[\$,]+/', '', $val);
                }

                $listing -> $col = $val;
            }

        }

        $listing -> save();
        return response() -> json([
            'status' => 'ok',
        ]);
    }
    // End Details Tab

    // Documents Tab
    public function get_documents(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $Agent_ID = $listing -> Agent_ID;

        $members = Members::where('Listing_ID', $Listing_ID) -> get();

        $documents = TransactionDocuments::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> orderBy('order') -> orderBy('created_at', 'DESC') -> get();
        $folders = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> orderBy('order') -> get();
        $checklist_items = TransactionChecklistItems::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> orderBy('checklist_item_order') -> get();
        $checklist_id = $checklist_items -> first() -> checklist_id;
        $checklist_form_ids = $checklist_items -> pluck('checklist_form_id') -> all();
        $checklist_forms = Upload::whereIn('file_id', $checklist_form_ids) -> get();

        $available_files = new Upload();

        $resource_items = new ResourceItems();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> where('resource_association', 'yes') -> orderBy('resource_order') -> get();
        $form_tags = $resource_items -> where('resource_type', 'form_tags') -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_documents', compact('listing', 'Agent_ID', 'Listing_ID', 'members', 'checklist_id', 'documents', 'folders', 'checklist_forms', 'available_files', 'resource_items', 'form_groups', 'form_tags'));
    }

    public function reorder_documents(Request $request) {
        $data = json_decode($request['data'], true);
        $data = $data['document'];

        foreach($data as $item) {
            $document_id = $item['document_id'];
            $document_order = $item['document_index'];
            $reorder = TransactionDocuments::where('id', $document_id) -> first();
            $reorder -> order = $document_order;
            $reorder -> save();
        }

    }

    public function add_folder(Request $request) {
        $order = TransactionDocumentsFolders::where('Listing_ID', $request -> Listing_ID) -> where('Agent_ID', $request -> Agent_ID) -> where('folder_name', '!=', 'Trash') -> max('order');
        $order += 1;
        $folder = new TransactionDocumentsFolders();
        $folder -> folder_name = $request -> folder;
        $folder -> order = $order;
        $folder -> Listing_ID = $request -> Listing_ID;
        $folder -> Agent_ID = $request -> Agent_ID;
        $folder -> save();
    }

    public function delete_folder(Request $request) {
        $folder_id = $request -> folder_id;
        $Listing_ID = $request -> Listing_ID;
        $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('folder_name', 'Trash') -> first();
        $move_documents_to_trash = TransactionDocuments::where('folder', $folder_id) -> update(['folder' => $trash_folder -> id]);
        $delete_folder = TransactionDocumentsFolders::where('id', $folder_id) -> delete();
    }

    public function save_add_template_documents(Request $request) {
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID;
        $folder = $request -> folder;

        $files = json_decode($request['files'], true);

        foreach($files as $file) {

            $file_id = $file['file_id'];
            $add_documents = new TransactionDocuments();
            $add_documents -> Agent_ID = $Agent_ID;
            $add_documents -> Listing_ID = $Listing_ID;
            $add_documents -> folder = $folder;
            $add_documents -> order = $file['order'];
            $add_documents -> orig_file_id = $file_id;
            $add_documents -> file_type = 'system';
            $add_documents -> file_name = $file['file_name'];
            $add_documents -> file_name_display = $file['file_name_display'];
            $add_documents -> pages_total = $file['pages_total'];
            $add_documents -> file_location = $file['file_location'];
            $add_documents -> save();

            $new_document_id = $add_documents -> id;

            $upload = Upload::where('file_id', $file_id) -> first();

            // create new upload
            $upload_copy = $upload -> replicate();
            $upload_copy -> orig_file_id = $file_id;
            $upload_copy -> file_type = 'system';
            $upload_copy -> ListingDocs_ID = $new_document_id;
            $upload_copy -> file_name_display = $upload -> file_name_display;
            $upload_copy -> Agent_ID = $Agent_ID;
            $upload_copy -> Listing_ID = $Listing_ID;
            $upload_new = $upload_copy -> toArray();
            $upload_new = TransactionUpload::create($upload_new);

            $new_file_id = $upload_new -> file_id;

            // update file_id in docs
            $add_documents -> file_id = $new_file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path.'/storage/app/public/';

            $copy_from = $storage_path.'doc_management/uploads/'.$file_id.'/*';
            $copy_to = $storage_path.'doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system';
            Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/converted');
            Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/layers');
            Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/combined');

            $copy = exec('cp -rp '.$copy_from.' '.$copy_to);
            $copy_converted = exec('cp '. $storage_path.'doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/'.$file['file_name'].' '.$copy_to .'/converted/'.$file['file_name']);

            $add_documents -> file_location = '/storage/doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/'.$file['file_name'];
            $add_documents -> file_location_converted = '/storage/doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/converted/'.$file['file_name'];
            $add_documents -> save();



            $upload_images = UploadImages::where('file_id', $file_id) -> get();
            $upload_pages = UploadPages::where('file_id', $file_id) -> get();

            foreach ($upload_images as $upload_image) {
                $copy = $upload_image -> replicate();
                $copy -> file_id = $new_file_id;
                $path = str_replace('/uploads/'.$file_id.'/', '/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_system/', $upload_image -> file_location);
                $copy -> file_location = $path;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $new = $copy -> toArray();
                TransactionUploadImages::create($new);
            }

            foreach ($upload_pages as $upload_page) {
                $copy = $upload_page -> replicate();
                $copy -> file_id = $new_file_id;
                $path = str_replace('/uploads/'.$file_id.'/', '/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/', $upload_page -> file_location);
                $copy -> file_location = $path;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $new = $copy -> toArray();
                TransactionUploadPages::create($new);
            }

            $fields = Fields::where('file_id', $file_id) -> get();
            $field_inputs = FieldInputs::where('file_id', $file_id) -> get();


            foreach ($fields as $field) {
                $copy = $field -> replicate();
                $copy -> file_id = $new_file_id;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> file_type = 'system';
                $copy -> field_inputs = 'yes';
                $new = $copy -> toArray();
                UserFields::create($new);
            }

            foreach ($field_inputs as $field_input) {
                $copy = $field_input -> replicate();
                $copy -> file_id = $new_file_id;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> file_type = 'system';
                $new = $copy -> toArray();
                UserFieldsInputs::create($new);
            }

        }

    }

    public function upload_documents(Request $request) {

        $file = $request -> file('file');
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID;
        $folder = $request -> folder;

        if ($file) {

            $ext = $file -> getClientOriginalExtension();
            $file_name_display = $file -> getClientOriginalName();
            $filename = $file_name_display;

            $date = date('YmdHis');
            $file_name_remove_numbers = preg_replace('/[0-9-_]+\.'.$ext.'/', '.'.$ext, $filename);
            $file_name_no_ext = str_replace('.'.$ext, '', $file_name_remove_numbers);
            $clean_filename = sanitize($file_name_no_ext);
            $new_filename = $date.'_'.$clean_filename.'.'.$ext;

            // convert to pdf if image
            if($ext != 'pdf') {
                $new_filename = $clean_filename.'_'.$date.'.pdf';
                $file_name_display = $file_name_no_ext.'.pdf';
                $create_images = exec('convert -quality 100 -density 300 -page letter '.$file.' /tmp/'.$new_filename, $output, $return);
                $file = '/tmp/'.$new_filename;
            }
            $pages_total = exec('pdftk '.$file.' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            // add to Documents
            $add_documents = new TransactionDocuments();
            $add_documents -> file_type = 'user';
            $add_documents -> Agent_ID = $Agent_ID;
            $add_documents -> Listing_ID = $Listing_ID;
            $add_documents -> folder = $folder;
            $add_documents -> file_name = $new_filename;
            $add_documents -> file_name_display = $file_name_display;
            $add_documents -> pages_total = $pages_total;
            $add_documents -> order = 0;
            $add_documents -> save();
            $ListingDocs_ID = $add_documents -> id;

            // add original file to uploads
            $upload = new TransactionUpload();
            $upload -> ListingDocs_ID = $ListingDocs_ID;
            $upload -> Agent_ID = $Agent_ID;
            $upload -> Listing_ID = $Listing_ID;
            $upload -> file_name = $new_filename;
            $upload -> file_name_display = $file_name_display;
            $upload -> file_type = 'user';
            $upload -> pages_total = $pages_total;
            $upload -> save();
            $file_id = $upload -> file_id;

            $add_documents -> file_id = $file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path.'/storage/app/public';
            $storage_dir = 'doc_management/transactions/listings/'.$Listing_ID.'/'.$file_id.'_user';
            $storage_public_path = '/storage/'.$storage_dir;
            $file_location = $storage_public_path.'/'.$new_filename;

            if (!Storage::disk('public') -> put($storage_dir.'/'.$new_filename, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Uploaded']);
                return ($fail);
            }
            // add to converted folder
            if (!Storage::disk('public') -> put($storage_dir.'/converted/'.$new_filename, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Added to Converted Directory']);
                return ($fail);
            }

            $storage_full_path = $storage_path.'/doc_management/transactions/listings/'.$Listing_ID.'/'.$file_id.'_user';
            chmod($storage_full_path.'/'.$new_filename, 0775);

            // update directory path in database
            $upload -> file_location = $file_location;
            $upload -> save();

            // create directories
            $storage_dir_pages = $storage_dir.'/pages';
            Storage::disk('public') -> makeDirectory($storage_dir_pages);
            $storage_dir_images = $storage_dir.'/images';
            Storage::disk('public') -> makeDirectory($storage_dir_images);


            // split pdf into pages and images
            $input_file = $storage_full_path.'/'.$new_filename;
            $output_files = $storage_path.'/'.$storage_dir_pages.'/page_%02d.pdf';
            $new_image_name = str_replace($ext, 'jpg', $new_filename);
            $output_images = $storage_path.'/'.$storage_dir_images.'/'.$new_image_name;

            // add individual pages to pages directory
            $create_pages = exec('pdftk '.$input_file.' burst output '.$output_files.' flatten', $output, $return);
            // remove data file
            exec('rm '.$storage_path.'/'.$storage_dir_pages.'/doc_data.txt');

            // add individual images to images directory
            $create_images = exec('convert -density 300 -quality 100 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files('public/'.$storage_dir.'/images');
            $images_public_path = $storage_public_path.'/images';

            foreach ($saved_images_directory as $saved_image) {
                // get just filename
                $images_file_name = basename($saved_image);
                $page_number = preg_match('/([0-9]+)\.jpg/', $images_file_name, $matches);
                $page_number = count($matches) > 1 ? $matches[1] + 1 : 1;

                // add images to database
                $upload_images = new TransactionUploadImages();
                $upload_images -> file_id = $file_id;
                $upload_images -> Agent_ID = $Agent_ID;
                $upload_images -> Listing_ID = $Listing_ID;
                $upload_images -> file_name = $images_file_name;
                $upload_images -> file_location = $images_public_path.'/'.$images_file_name;
                $upload_images -> pages_total = $pages_total;
                $upload_images -> page_number = $page_number;
                $upload_images -> save();

            }

            $saved_pages_directory = Storage::files('public/'.$storage_dir.'/pages');
            $pages_public_path = $storage_public_path.'/pages';

            $page_number = 1;
            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                $upload_pages = new TransactionUploadPages();
                $upload_pages -> Agent_ID = $Agent_ID;
                $upload_pages -> Listing_ID = $Listing_ID;
                $upload_pages -> file_id = $file_id;
                $upload_pages -> file_name = $pages_file_name;
                $upload_pages -> file_location = $pages_public_path.'/'.$pages_file_name;
                $upload_pages -> pages_total = $pages_total;
                $upload_pages -> page_number = $page_number;
                $upload_pages -> save();

                $page_number += 1;

            }

            $add_documents -> file_location = $file_location;
            $add_documents -> file_location_converted = $storage_public_path.'/converted/'.$new_filename;
            $add_documents -> save();



        }

    }

    public function move_documents_to_trash(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('folder_name', 'Trash') -> first();
        $document_ids = explode(',', $request -> document_ids);
        $update_folder = TransactionDocuments::whereIn('id', $document_ids) -> update(['folder' => $trash_folder -> id]);
    }

    public function move_documents_to_folder(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $folder_id = $request -> folder_id;
        $document_ids = explode(',', $request -> document_ids);
        $update_folder = TransactionDocuments::whereIn('id', $document_ids) -> update(['folder' => $folder_id]);
    }

    public function add_document_to_checklist_item_html(Request $request) {
        $checklist_id = $request -> checklist_id;
        $document_ids = $request -> document_ids;
        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_modal = new TransactionChecklistItems();
        $checklist_items = $transaction_checklist_items_modal -> where('checklist_id', $checklist_id) -> get();
        $transaction_checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_id', $checklist_id) -> get();
        $documents = TransactionDocuments::whereIn('id', $document_ids) -> get();
        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', ['listing', 'both']) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/add_document_to_checklist_item_html', compact('checklist_id', 'documents', 'transaction_checklist_item_documents', 'checklist_items_model', 'transaction_checklist_items_modal', 'checklist_items', 'checklist_groups'));
    }

    public function save_assign_documents_to_checklist(Request $request) {

        $checklist_items = json_decode($request['checklist_items']);
        $checklist_id = $request -> checklist_id;
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID;

        foreach($checklist_items as $checklist_item) {

            $checklist_item_id = $checklist_item -> checklist_item_id;
            $document_ids = collect($checklist_item -> document_ids);

            foreach($document_ids as $document_id) {

                $add_checklist_item_doc = new TransactionChecklistItemsDocs();
                $add_checklist_item_doc -> document_id = $document_id;
                $add_checklist_item_doc -> checklist_id = $checklist_id;
                $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
                $add_checklist_item_doc -> Agent_ID = $Agent_ID;
                $add_checklist_item_doc -> Listing_ID = $Listing_ID;
                $add_checklist_item_doc -> save();

                $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);

            }

        }

    }

    public function save_rename_document(Request $request) {

        $new_name = $request -> new_name;
        $document_id = $request -> document_id;
        $document = TransactionDocuments::where('id', $document_id) -> first();

        $file_name = sanitize(str_replace('.pdf', '', $new_name)).'.pdf';
        $file_name_display = str_replace('.pdf', '', $new_name).'.pdf';
        $file_location = str_replace($document -> file_name, $file_name, $document -> file_location);
        $file_location_converted = str_replace($document -> file_name, $file_name, $document -> file_location_converted);

        File::move($this -> get_path($document -> file_location), $this -> get_path($file_location));
        File::move($this -> get_path($document -> file_location_converted), $this -> get_path($file_location_converted));

        $transaction_upload = TransactionUpload::where('ListingDocs_ID', $document_id) -> update(['file_name_display' => $new_name]);
        $transaction_document = TransactionDocuments::where('id', $document_id) -> update(['file_name_display' => $new_name]);

        $document -> file_name = $file_name;
        $document -> file_name_display = $file_name_display;
        $document -> file_location = $file_location;
        $document -> file_location_converted = $file_location_converted;
        $document -> save();

        return true;
    }

    public function get_split_document_html(Request $request) {

        $checklist_id = $request -> checklist_id;
        $document_id = $request -> document_id;
        $document = TransactionDocuments::where('id', $document_id) -> first();
        $file_id = $document -> file_id;
        $file_type = $request -> file_type;
        $file_name = $request -> file_name;

        $document_images = TransactionUploadImages::where('file_id', $file_id) -> orderBy('page_number') -> get();

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_modal = new TransactionChecklistItems();
        $checklist_items = $transaction_checklist_items_modal -> where('checklist_id', $checklist_id) -> get();

        $transaction_checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_id', $checklist_id) -> get();
        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', ['listing', 'both']) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_split_document_html', compact('document_id', 'file_id', 'file_type', 'file_name', 'document', 'document_images', 'checklist_items', 'checklist_groups', 'transaction_checklist_item_documents', 'checklist_items_model', 'transaction_checklist_items_modal'));
    }

    public function copy_file($path, $newpath) {
        $location = $this -> applyPathPrefix($path);
        $destination = $this -> applyPathPrefix($newpath);
        $this -> ensureDirectory(dirname($destination));
        return copy($location, $destination);
    }

    public function save_split_document(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $Agent_ID = $request -> Agent_ID;
        $folder_id = $request -> folder_id;
        $document_name = $request -> document_name;
        $image_ids = explode(',', $request -> image_ids);
        $pages_total = count($image_ids);
        $file_type = $request -> file_type;
        $file_id = $request -> file_id;
        $checklist_item_id = $request -> checklist_item_id;
        $checklist_id = $request -> checklist_id;

        $document_images = TransactionUploadImages::whereIn('id', $image_ids) -> get();

        $document_image_files = [];
        $document_page_files = [];
        $page_numbers = [];
        foreach($document_images as $document_image) {

            $doc_file_id = $document_image -> file_id;
            $doc_page_number = $document_image -> page_number;
            $page_numbers[] = $doc_page_number;

            $pages = [];
            $images = [];

            $document_page = TransactionUploadPages::where('file_id', $doc_file_id) -> where('page_number', $doc_page_number) -> first();
            $pages = ['file_id' => $document_page -> file_id, 'file_location' => $document_page -> file_location];
            $images = ['file_id' => $document_image -> file_id, 'file_location' => $document_image -> file_location, 'page_number' => $doc_page_number];

            array_push($document_page_files, $pages);
            array_push($document_image_files, $images);
        }

        // if manually saving to documents
        if($document_name) {
            $file_name = sanitize($document_name).'.pdf';
            $file_name_display = $document_name.'.pdf';

        // if adding to checklist item
        // assign to checklist item
        } else {
            $checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> first();
            $checklist_form_id = $checklist_item -> checklist_form_id;
            $file_name_display = Upload::GetFormName($checklist_form_id);
            $file_name = sanitize($file_name_display).'.pdf';
        }

        // add to docs_transaction_docs
        $add_document = new TransactionDocuments();
        $add_document -> file_type = 'user';
        $add_document -> Agent_ID = $Agent_ID;
        $add_document -> Listing_ID = $Listing_ID;
        $add_document -> folder = $folder_id;
        $add_document -> file_name = $file_name;
        $add_document -> file_name_display = $file_name_display;
        $add_document -> pages_total = $pages_total;
        $add_document -> save();
        $ListingDocs_ID = $add_document -> id;

        // add to transaction uploads
        $upload = new TransactionUpload();
        $upload -> ListingDocs_ID = $ListingDocs_ID;
        $upload -> Agent_ID = $Agent_ID;
        $upload -> Listing_ID = $Listing_ID;
        $upload -> file_name = $file_name;
        $upload -> file_name_display = $file_name_display;
        $upload -> pages_total = $pages_total;
        $upload -> save();
        $new_file_id = $upload -> file_id;

        $add_document -> file_id = $new_file_id;
        $add_document -> save();

        Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/images');
        Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/pages');

        // copy images and pages and create merged file
        // copy images
        $page_number = 1;
        foreach($document_image_files as $image_file) {
            $image_file_name = basename($image_file['file_location']);
            $old_file_loc = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$document_image_files[0]['file_id'].'_'.$file_type.'/images/'.$image_file_name);
            $new_file_loc = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/images/'.$image_file_name);
            exec('cp '.$old_file_loc.' '.$new_file_loc);

            $upload_images = new TransactionUploadImages();
            $upload_images -> file_id = $new_file_id;
            $upload_images -> Agent_ID = $Agent_ID;
            $upload_images -> Listing_ID = $Listing_ID;
            $upload_images -> file_name = $file_name;
            $upload_images -> file_location = '/storage/doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/images/'.$image_file_name;
            $upload_images -> pages_total = count($document_image_files);
            $upload_images -> page_number = $page_number;
            $upload_images -> save();

            // copy from docs_transaction_fields ** update new page for each
            $add_user_fields = UserFields::where('file_id', $image_file['file_id']) -> where('page', $image_file['page_number']) -> get();
            $field_ids = [];
            foreach($add_user_fields as $add_user_field) {
                $field_ids[] = $add_user_field -> field_id;
                $add_user_fields_copy = $add_user_field -> replicate();
                $add_user_fields_copy -> page = $page_number;
                $add_user_fields_copy -> file_type = 'user';
                $add_user_fields_copy -> file_id = $new_file_id;
                $add_user_fields_copy -> save();
            }

            $user_fields_inputs = UserFieldsInputs::where('file_id', $image_file['file_id'])  -> get();

            foreach ($user_fields_inputs as $user_fields_input) {
                $add_user_fields_input_copy = $user_fields_input -> replicate();
                $add_user_fields_input_copy -> file_id = $new_file_id;
                $add_user_fields_input_copy -> file_type = 'user';
                $add_user_fields_input_copy -> save();
            }

            // copy from docs_transaction_fields_inputs_values
            $add_user_field_values = UserFieldsValues::whereIn('input_id', $field_ids) -> get();
            foreach($add_user_field_values as $add_user_field_value) {
                $add_user_field_values_copy = $add_user_field_value -> replicate();
                $add_user_field_values_copy -> file_type = 'user';
                $add_user_field_values_copy -> file_id = $new_file_id;
                $add_user_field_values_copy -> save();
            }

            $page_number += 1;

        }
        // copy pages
        $page_number = 1;
        foreach($document_page_files as $page_file) {
            $page_file_name = basename($page_file['file_location']);
            $old_file_loc = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$document_page_files[0]['file_id'].'_'.$file_type.'/pages/'.$page_file_name);
            $new_file_loc = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/pages/'.$page_file_name);
            exec('cp '.$old_file_loc.' '.$new_file_loc);

            $upload_pages = new TransactionUploadPages();
            $upload_pages -> file_id = $new_file_id;
            $upload_pages -> Agent_ID = $Agent_ID;
            $upload_pages -> Listing_ID = $Listing_ID;
            $upload_pages -> file_name = $file_name;
            $upload_pages -> file_location = '/storage/doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/pages/'.$page_file_name;
            $upload_pages -> pages_total = count($document_page_files);
            $upload_pages -> page_number = $page_number;
            $upload_pages -> save();
            $page_number += 1;
        }

        //merge pages into main file and move to converted
        $main_file_location = 'doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/'.$file_name;
        $converted_file_location = 'doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/converted/'.$file_name;

        $base_path = base_path();
        exec('mkdir '.$base_path.'/storage/app/public/doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/converted');

        // merge all pages and add to main directory and converted directory
        $pages = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/pages');
        exec('pdftk '.$pages.'/*.pdf cat output '.$base_path.'/storage/app/public/'.$main_file_location);
        //exec('cd '.$base_path.'/storage/app/public/ && cp '.$main_file_location.' '.$converted_file_location);
        // get split pages, merge and add to converted
        $old_converted_location = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$file_id.'_'.$file_type.'/converted');
        $new_converted_location = Storage::disk('public') -> path('doc_management/transactions/listings/'.$Listing_ID.'/'.$new_file_id.'_user/converted');

        exec('pdftk '.$old_converted_location.'/*.pdf cat '.implode(' ', $page_numbers).' output '.$new_converted_location.'/'.$file_name);

        // update file locations in docs_transaction and docs uploads
        $add_document -> file_location = '/storage/'.$main_file_location;
        $add_document -> file_location_converted = '/storage/'.$converted_file_location;
        $add_document -> save();

        $upload -> file_location = '/storage/'.$main_file_location;
        $upload -> save();

        // add to checklist
        if($checklist_id > 0) {
            $document_id = $ListingDocs_ID;
            $checklist_id = $checklist_id;
            $checklist_item_id = $checklist_item_id;
            $Agent_ID = $Agent_ID;
            $Listing_ID = $Listing_ID;

            $add_checklist_item_doc = new TransactionChecklistItemsDocs();
            $add_checklist_item_doc -> document_id = $document_id;
            $add_checklist_item_doc -> checklist_id = $checklist_id;
            $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
            $add_checklist_item_doc -> Agent_ID = $Agent_ID;
            $add_checklist_item_doc -> Listing_ID = $Listing_ID;
            $add_checklist_item_doc -> save();

            $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);

        }

    }

    public function duplicate_document(Request $request) {

        $document_id = $request -> document_id;
        $file_type = $request -> file_type;
        // get document details
        $document = TransactionDocuments::where('id', $document_id) -> first();
        $orig_upload_id = $document -> file_id;
        $Listing_ID = $document -> Listing_ID;
        $Agent_ID = $document -> Agent_ID;

        // copy to documents
        $document_copy = $document -> replicate();
        $document_copy -> save();
        $new_document_id = $document_copy -> id;

        $upload = TransactionUpload::where('file_id', $orig_upload_id) -> first();

        // create new upload
        $upload_copy = $upload -> replicate();
        $upload_copy -> ListingDocs_ID = $new_document_id;
        $upload_copy -> file_name_display = $upload -> file_name_display;
        $upload_copy -> Agent_ID = $Agent_ID;
        $upload_copy -> Listing_ID = $Listing_ID;
        $upload_copy -> save();
        $new_upload_id = $upload_copy -> file_id;

        $orig_uploads_path = 'doc_management/transactions/listings/'.$Listing_ID.'/'.$orig_upload_id.'_'.$file_type;
        $new_uploads_path = 'doc_management/transactions/listings/'.$Listing_ID.'/'.$new_upload_id.'_'.$file_type;

        // copy original file
        File::copyDirectory(Storage::disk('public') -> path($orig_uploads_path), Storage::disk('public') -> path($new_uploads_path));
        // add file_location to upload
        $upload_copy -> file_location = '/storage/'.$new_uploads_path.'/'.$upload -> file_name;
        $upload_copy -> save();


        // add other details to docs
        $document_copy -> file_location = '/storage/'.$new_uploads_path.'/'.$upload -> file_name;
        $document_copy -> file_location_converted = '/storage/'.$new_uploads_path.'/converted/'.$upload -> file_name;
        $document_copy -> file_name_display = $upload -> file_name_display.'-COPY';
        $document_copy -> file_id = $new_upload_id;
        //$document_copy -> order = $document_copy -> order + 1;
        $document_copy -> assigned = 'no';
        $document_copy -> save();

        $new_document_id = $document_copy -> id;

        // update uploads with new doc id
        $upload_copy -> ListingDocs_ID = $new_document_id;
        $upload_copy -> save();

        // copy all pages, images, fields and field values
        $data_sets = [TransactionUploadImages::where('file_id', $orig_upload_id) -> get(), TransactionUploadPages::where('file_id', $orig_upload_id) -> get()];

        foreach ($data_sets as $data_set) {
            foreach ($data_set as $row) {
                $copy = $row -> replicate();
                $copy -> file_id = $new_upload_id;
                $path = str_replace('/'.$orig_upload_id.'/', '/'.$new_upload_id.'_'.$file_type.'/', $row -> file_location);
                $copy -> file_location = $path;
                $copy -> save();
            }
        }

        $user_fields = UserFields::where('file_id', $orig_upload_id) -> get();

        foreach ($user_fields as $user_field) {
            $copy = $user_field -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> save();
        }

        $user_fields_inputs = UserFieldsInputs::where('file_id', $orig_upload_id)  -> get();

        foreach ($user_fields_inputs as $user_fields_input) {
            $copy = $user_fields_input -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> save();
        }

        // add input values
        $field_input_values = UserFieldsValues::where('file_id', $orig_upload_id) -> get();
        foreach ($field_input_values as $field_input_value) {
            $copy = $field_input_value -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> file_type = $file_type;
            $copy -> Agent_ID = $Agent_ID;
            $copy -> Listing_ID = $Listing_ID;
            $copy -> save();
        }

    }

    public function email_documents(Request $request) {

        $email = [];

        $from_address = $request -> from;
        $from_name = '';

        if(preg_match('/\<.*\>/', $from_address)) {
            preg_match('/(.*)[\s]*\<(.*)\>/', $from_address, $match);
            $from_name = $match[1];
            $from_address = $match[2];
        }
        $email['from'] = ['address' => $from_address, 'name' => $from_name];

        $email['subject'] = $request -> subject;
        $email['message'] = $request -> message;

        $email['tos_array'] = [];
        foreach(json_decode($request -> to_addresses) as $to_address) {
            $to = [];
            $to['type'] = $to_address -> type;
            $to['address'] = $to_address -> address;
            $email['tos_array'][] = $to;
        }

        $email['attachments'] = [];
        $attachment_size = 0;
        foreach(json_decode($request -> attachments) as $attachment) {
            $file = [];
            $file['name'] = $attachment -> filename;
            $file['location'] = $attachment -> file_location;
            $email['attachments'][] = $file;

            $attachment_size += filesize(Storage::disk('public') -> path($attachment -> file_location));

        }

        $attachment_size = get_mb($attachment_size);
        if($attachment_size > 20) {
            $fail = json_encode(['fail' => true, 'attachment_size' => $attachment_size]);
            return ($fail);
        }

        $email['tos'] = [];
        $email['ccs'] = [];
        $email['bccs'] = [];

        foreach($email['tos_array'] as $to) {

            $to_address = $to['address'];
            $to_name = '';

            if(preg_match('/\<.*\>/', $to['address'])) {
                preg_match('/(.*)[\s]*\<(.*)\>/', $to['address'], $match);
                $to_name = $match[1];
                $to_address = $match[2];
            }

            if($to['type'] == 'to') {
                $email['tos'][] = ['name' => $to_name, 'email' => $to_address];
            } else if($to['type'] == 'cc') {
                $email['ccs'][] = ['name' => $to_name, 'email' => $to_address];
            } else if($to['type'] == 'bcc') {
                $email['bccs'][] = ['name' => $to_name, 'email' => $to_address];
            }
        }

        //return (new Documents($email)) -> render();

        Mail::to($email['tos'])
            -> cc($email['ccs'])
            -> bcc($email['bccs'])
            -> send(new Documents($email));

    }

    public function email_get_documents(Request $request) {

        $docs_type = $request -> docs_type;

        $filenames = [];
        $file_locations = [];

        if($docs_type != '') {

            $file = $this -> merge_documents($request);

            // when multiple docs are emailed
            if($docs_type == 'merged') {
                $file_locations[] = str_replace('/storage/', '', $file['file_location']);
                $filenames[] = $file['filename'];
            } else if($docs_type == 'single') {
                foreach($file['single_documents'] as $doc) {
                    $file_locations[] = str_replace('/storage/', '', $doc -> file_location_converted);
                    $filenames[] = $doc -> file_name_display;
                }
            }

        } else {
            // when a single doc is emailed
            $doc = TransactionDocuments::where('id', $request -> document_ids) -> first();
            $file_locations[] = str_replace('/storage/', '', $doc -> file_location_converted);
            $filenames[] = $doc -> file_name_display;

        }

        return compact('file_locations', 'filenames');

    }

    public function get_path($url) {
        //debug($url);
        return Storage::disk('public') -> path(preg_replace('/^.*\/storage\//', '', $url));
    }

    public function merge_documents(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $folder_id = $request -> folder_id;
        $type = $request -> type;
        $docs_type = $request -> docs_type;

        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        // create filename for merged docs
        $filename = sanitize($listing -> FullStreetAddress).'_'.date('YmdHis').'.pdf';

        $document_ids = explode(',', $request -> document_ids);
        $documents = [];
        foreach($document_ids as $document_id) {
            if($type == 'filled') {
                $documents[] = TransactionDocuments::where('id', $document_id) -> pluck('file_location_converted') -> first();
                $single_documents[] = TransactionDocuments::select('file_location_converted', 'file_name_display') -> where('id', $document_id) -> first();
            } else if($type == 'blank') {
                $documents[] = TransactionDocuments::where('id', $document_id) -> pluck('file_location') -> first();
            }
        }

        $docs_array = array_map(array($this, 'get_path'), $documents);
        $docs = implode(' ', $docs_array);

        $tmp = Storage::disk('public') -> path('tmp');
        exec('pdftk '.$docs.' cat output '.$tmp.'/'.$filename);

        $file_location = '/storage/tmp/'.$filename;
        return compact('file_location', 'filename', 'single_documents');

    }


    // End Documents Tab


    // Listing Details
    public function listing_details(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $resource_items = new ResourceItems();
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', $resource_items -> SellerResourceId()) -> get();

        if ($listing -> ExpirationDate != '' && $listing -> ExpirationDate != '0000-00-00') {
            return view('/agents/doc_management/transactions/listings/details/listing_details', compact('listing', 'sellers'));
        } else {
            return redirect('/agents/doc_management/transactions/listings/listing_required_details/'.$Listing_ID);
        }

    }

    // Listing Details Header
    public function listing_details_header(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $resource_items = new ResourceItems();
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', $resource_items -> SellerResourceId()) -> get();
        $statuses = $resource_items -> where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();
        return view('/agents/doc_management/transactions/listings/details/listing_details_header', compact('listing', 'sellers', 'resource_items', 'statuses'));
    }

    public function mls_search(Request $request) {

        $listing_details = Listings::find($request -> Listing_ID);
        $mls_search_details = bright_mls_search($request -> ListingId);
        $mls_search_details = (object)$mls_search_details;

        // only if mls search produced results
        if (isset($mls_search_details -> ListingId)) {

            return response() -> json([
                'status' => 'ok',
                'county_match' => 'yes',
                'address' => $mls_search_details -> FullStreetAddress,
                'city' => $mls_search_details -> City,
                'state' => $mls_search_details -> StateOrProvince,
                'zip' => $mls_search_details -> PostalCode,
                'picture_url' => $mls_search_details -> ListPictureURL,
                'list_company' => $mls_search_details -> ListOfficeName,
            ]);
        }

        return response() -> json([
            'status' => 'not found',
        ]);
    }

    public function save_mls_search(Request $request) {

        $listing_details = Listings::find($request -> Listing_ID);
        $mls_search_details = bright_mls_search($request -> ListingId);
        $mls_search_details = (object)$mls_search_details;
        $resource_items = new ResourceItems();
        $checklist = TransactionChecklists::where('Listing_ID', $request -> Listing_ID) -> where('Agent_ID', $listing_details -> Agent_ID) -> first();
        $checklist_id = $checklist -> id;

        // set values
        $property_type_val = $mls_search_details -> PropertyType;
        $sale_rent = '';
        if($property_type_val) {
            if(stristr($property_type_val, 'lease')) {
                $sale_rent = 'rental';
                $property_type_val = str_replace(' Lease', '', $property_type_val);
            } else {
                $sale_rent = 'sale';
            }
        }
        $property_type_id = $resource_items -> GetResourceID($property_type_val, 'checklist_property_types');

        $property_sub_type = $mls_search_details -> SaleType;
        if($property_sub_type) {
            $end = strpos($property_sub_type, ',');
            if(!$end) {
                $end = strlen($property_sub_type);
            }
            $property_sub_type = trim(substr($property_sub_type, 0, $end));

            if(preg_match('/(hud|reo)/i', $property_sub_type)) {
                $property_sub_type = 'REO/Bank/HUD Owned';
            } else if(preg_match('/foreclosure/i', $property_sub_type)) {
                $property_sub_type = 'Foreclosure';
            } else if(preg_match('/auction/i', $property_sub_type)) {
                $property_sub_type = 'Auction';
            } else if(preg_match('/(short|third)/i', $property_sub_type)) {
                $property_sub_type = 'Short Sale';
            } else if(preg_match('/standard/i', $property_sub_type)) {
                $property_sub_type = 'Standard';
            } else {
                $property_sub_type = '';
            }
            // if no results check new construction
            if($property_sub_type == '') {
                if($mls_search_details -> NewConstructionYN == 'Y') {
                    $property_sub_type = 'New Construction';
                }
            }
        }
        $property_sub_type_id = $resource_items -> GetResourceID($property_sub_type, 'checklist_property_sub_types');

        $hoa_condo = 'none';
        $condo = $mls_search_details -> CondoYN ?? null;
        if($condo && $condo == 'Y') {
            $hoa_condo = 'condo';
        }
        $hoa = $mls_search_details -> AssociationYN ?? null;
        if($hoa && $hoa == 'Y') {
            if($mls_search_details -> AssociationFee > 0) {
                $hoa_condo = 'hoa';
            }
        }

        if($mls_search_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> StateOrProvince, 'checklist_locations');
        }

        TransactionChecklists::CreateListingChecklist($checklist_id, $request -> Listing_ID, $listing_details -> Agent_ID, 'seller', 'listing', $property_type_id, $property_sub_type_id, $sale_rent, $mls_search_details -> StateOrProvince, $location_id, '', '');

        $listing_details -> ListingId = $request -> ListingId;

        // get cols and vals for mls search
        foreach ($mls_search_details as $col => $val) {

            // if listing_details col matches then update it if it doesn't match original value
            if (isset($listing_details -> $col)) {
                if ($listing_details -> $col != $val && $val != '') {

                    // if a name field only replace if blank
                    if (in_array($listing_details -> $col, config('global.vars.select_columns_bright_agents'))) {
                        if ($val == '') {
                            $listing_details -> $col = $val;
                        }

                    } else {

                        if($col == 'PropertyType') {

                            $listing_details -> $col = $property_type_id;

                        } else if ($col == 'PropertySubType') {

                            $listing_details -> $col = $property_sub_type_id;

                        } else if ($col == 'County') {

                            $listing_details -> $col = $location_id;

                        } else if($col == 'HoaCondoFees') {

                            $listing_details -> $col = $hoa_condo;

                        } else {

                            $listing_details -> $col = $val;

                        }

                    }

                }

            }

        }

        $listing_details -> MLS_Verified = 'yes';
        $listing_details -> save();

        return response() -> json([
            'status' => 'ok',
        ]);

    }

    public function update_sellers($Listing_ID) {
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', ResourceItems::SellerResourceId());

        $seller_two_first = $seller_two_last = '';
        $seller_one_first = $sellers -> first() -> first_name;
        $seller_one_last = $sellers -> first() -> last_name;

        if ($sellers -> take(1) -> first()) {
            $seller_two_first = $sellers -> take(1) -> first() -> first_name;
            $seller_two_last = $sellers -> take(1) -> first() -> last_name;
        }

        $listing = Listings::find($Listing_ID);
        $listing -> SellerOneFirstName = $seller_one_first;
        $listing -> SellerOneLastName = $seller_one_last;
        $listing -> SellerTwoFirstName = $seller_two_first;
        $listing -> SellerTwoLastName = $seller_two_last;
        $listing -> save();
    }

    // TEMP get all listings
    public function listings_all(Request $request) {
        $listings = Listings::where('Agent_ID', auth() -> user() -> user_id) -> get();
        return view('/agents/doc_management/transactions/listings/listings_all', compact('listings'));
    }



}
