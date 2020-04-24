<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Listings;

use App\Http\Controllers\Controller;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\CRM\CRMContacts;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
use App\Models\Employees\Agents;
use App\Models\Employees\Teams;
use App\Models\Resources\LocationData;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;


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

        $transaction_checklist = TransactionChecklists::where('Listing_ID', $Listing_ID) -> first();
        $transaction_checklist_id = $transaction_checklist -> id;

        $transaction_checklist_items = $transaction_checklist_items_model -> where('Listing_ID', $Listing_ID) -> where('checklist_id' , $transaction_checklist_id)  -> orderBy('checklist_item_order') -> get();

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', ['listing', 'both']) -> orderBy('resource_order') -> get();

        $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('folder_name', 'Trash') -> first();
        $documents = TransactionDocuments::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('order') -> get();
        $folders = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> where('folder_name', '!=', 'Trash') -> orderBy('order') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_checklist', compact('Listing_ID', 'checklist_items_model', 'transaction_checklist', 'transaction_checklist_id',  'transaction_checklist_items', 'transaction_checklist_item_docs_model', 'transaction_checklist_item_notes_model', 'transaction_checklist_items_model','checklist_groups', 'documents', 'folders'));
    }

    public function add_document_to_checklist_item(Request $request) {
        $document_id = $request -> document_id;
        $checklist_id = $request -> checklist_id;
        $checklist_item_id = $request -> checklist_item_id;
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID;

        // pdf to attach is file_location_convert

        // set assigned = 'yes'
    }
    // End Checklist Tab

    // Members Tab
    public function get_members(Request $request) {
        $listing = Listings::find($request -> Listing_ID);
        $members = Members::where('Listing_ID', $request -> Listing_ID) -> get();
        $resource_items = new ResourceItems();
        $contact_types = $resource_items -> where('resource_type', 'contact_type') -> get();
        $states = LocationData::AllStates();
        $contacts = CRMContacts::where('Agent_ID', $listing -> Agent_ID) -> get();
        return view('/agents/doc_management/transactions/listings/details/data/get_members', compact('members', 'contact_types', 'resource_items', 'states', 'contacts'));
    }

    public function add_member_html(Request $request) {
        $contact_types = ResourceItems::where('resource_type', 'contact_type') -> get();
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

        $FullStreetAddress = $data['StreetNumber'] . ' ' . $data['StreetName'] . ' ' . $data['StreetSuffix'];

        if ($data['StreetDirSuffix']) {
            $FullStreetAddress .= ' ' . $data['StreetDirSuffix'];
        }

        if ($data['UnitNumber']) {
            $FullStreetAddress .= ' ' . $data['UnitNumber'];
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
        $documents = TransactionDocuments::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> orderBy('order') -> get();
        $folders = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> orderBy('order') -> get();
        $checklist_items = TransactionChecklistItems::where('Listing_ID', $Listing_ID) -> orderBy('checklist_item_order') -> get();
        $checklist_form_ids = $checklist_items -> pluck('checklist_form_id') -> all();
        $checklist_forms = Upload::whereIn('file_id', $checklist_form_ids) -> get();

        $available_files = new Upload();

        $resource_items = new ResourceItems();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> where('resource_association', 'yes') -> orderBy('resource_order') -> get();
        $form_tags = $resource_items -> where('resource_type', 'form_tags') -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_documents', compact('Agent_ID', 'Listing_ID', 'documents', 'folders', 'checklist_forms', 'available_files', 'resource_items', 'form_groups', 'form_tags'));
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
            $add_documents -> file_id = $file_id;
            $add_documents -> file_type = 'system';
            $add_documents -> file_name = $file['file_name'];
            $add_documents -> file_name_display = $file['file_name_display'];
            $add_documents -> pages_total = $file['pages_total'];
            $add_documents -> file_location = $file['file_location'];
            $add_documents -> save();

            // copy all original documents from system uploads to document uploads
            $base_path = base_path();
            $storage_path = $base_path . '/storage/app/public/';

            $copy_from = $storage_path . 'doc_management/uploads/' . $file_id . '/*';
            $copy_to = $storage_path . 'doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_system';
            Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_system/converted');
            Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_system/layers');
            Storage::disk('public') -> makeDirectory('doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_system/combined');
            $copy = exec('cp -r ' . $copy_from . ' ' . $copy_to);
            $copy_converted = exec('cp '. $storage_path . 'doc_management/uploads/' . $file_id . '/'.$file['file_name'] . ' ' . $copy_to .'/converted/'.$file['file_name']);

            $add_documents -> file_location_converted = '/storage/doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_system/converted/'.$file['file_name'];
            $add_documents -> save();

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
            $file_name_no_ext = str_replace('.' . $ext, '', $filename);
            $clean_filename = sanitize($file_name_no_ext);
            $new_filename = $clean_filename . '_' . $date . '.' . $ext;
            // convert to pdf if image
            if($ext != 'pdf') {
                $new_filename = $clean_filename . '_' . $date . '.pdf';
                $file_name_display = $file_name_no_ext.'.pdf';
                $create_images = exec('convert -quality 100 -density 300 ' . $file . ' /tmp/'.$new_filename, $output, $return);
                $file = '/tmp/'.$new_filename;
            }
            $pages_total = exec('pdftk ' . $file . ' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            // add to Documents first because of foreign key restraint on TransactionUpload
            $add_documents = new TransactionDocuments();
            $add_documents -> file_type = 'user';
            $add_documents -> Agent_ID = $Agent_ID;
            $add_documents -> Listing_ID = $Listing_ID;
            $add_documents -> folder = $folder;
            $add_documents -> file_name = $new_filename;
            $add_documents -> file_name_display = $file_name_display;
            $add_documents -> pages_total = $pages_total;
            $add_documents -> save();
            $ListingDocs_ID = $add_documents -> id;

            // add original file to database
            $upload = new TransactionUpload();
            $upload -> ListingDocs_ID = $ListingDocs_ID;
            $upload -> Agent_ID = $Agent_ID;
            $upload -> Listing_ID = $Listing_ID;
            $upload -> file_name = $new_filename;
            $upload -> file_name_display = $file_name_display;
            $upload -> pages_total = $pages_total;
            $upload -> pages_total = $pages_total;
            $upload -> save();
            $file_id = $upload -> file_id;

            $add_documents -> file_id = $file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path . '/storage/app/public';
            $storage_dir = 'doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_user';
            $storage_public_path = '/storage/'.$storage_dir;
            $file_location = $storage_public_path . '/' . $new_filename;

            if (!Storage::disk('public') -> put($storage_dir . '/' . $new_filename, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Uploaded']);
                return ($fail);
            }
            // add to converted folder
            if (!Storage::disk('public') -> put($storage_dir . '/converted/' . $new_filename, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Added to Converted Directory']);
                return ($fail);
            }

            $storage_full_path = $storage_path . '/doc_management/transactions/listings/' . $Listing_ID . '/' . $file_id . '_user';
            chmod($storage_full_path . '/' . $new_filename, 0775);

            // update directory path in database
            $upload -> file_location = $file_location;
            $upload -> save();

            // create directories
            $storage_dir_pages = $storage_dir . '/pages';
            Storage::disk('public') -> makeDirectory($storage_dir_pages);
            $storage_dir_images = $storage_dir . '/images';
            Storage::disk('public') -> makeDirectory($storage_dir_images);


            // split pdf into pages and images
            $input_file = $storage_full_path . '/' . $new_filename;
            $output_files = $storage_path . '/' . $storage_dir_pages . '/page_%02d.pdf';
            $new_image_name = str_replace($ext, 'jpg', $new_filename);
            $output_images = $storage_path . '/' . $storage_dir_images . '/' . $new_image_name;

            // add individual pages to pages directory
            $create_pages = exec('pdftk ' . $input_file . ' burst output ' . $output_files, $output, $return);
            // remove data file
            exec('rm ' . $storage_path . '/' . $storage_dir_pages . '/doc_data.txt');

            // add individual images to images directory
            $create_images = exec('convert -density 100 -quality 100 ' . $input_file . ' -background white -alpha remove -strip ' . $output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files('public/' . $storage_dir . '/images');
            $images_public_path = $storage_public_path . '/images';

            $page_number = 1;
            foreach ($saved_images_directory as $saved_image) {
                // get just filename
                $images_file_name = basename($saved_image);

                // add images to database
                $upload_images = new TransactionUploadImages();
                $upload_images -> file_id = $file_id;
                $upload_images -> Agent_ID = $Agent_ID;
                $upload_images -> Listing_ID = $Listing_ID;
                $upload_images -> file_name = $images_file_name;
                $upload_images -> file_location = $images_public_path . '/' . $images_file_name;
                $upload_images -> pages_total = $pages_total;
                $upload_images -> page_number = $page_number;
                $upload_images -> save();
                $page_number += 1;

            }

            $saved_pages_directory = Storage::files('public/' . $storage_dir . '/pages');
            $pages_public_path = $storage_public_path . '/pages';

            $page_number = 1;
            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                $upload_pages = new TransactionUploadPages();
                $upload_pages -> Agent_ID = $Agent_ID;
                $upload_pages -> Listing_ID = $Listing_ID;
                $upload_pages -> file_id = $file_id;
                $upload_pages -> file_name = $pages_file_name;
                $upload_pages -> file_location = $pages_public_path . '/' . $pages_file_name;
                $upload_pages -> pages_total = $pages_total;
                $upload_pages -> page_number = $page_number;
                $upload_pages -> save();

                $page_number += 1;

            }

            $add_documents -> file_location = $file_location;
            $add_documents -> file_location_converted =  $storage_public_path . '/converted/' . $new_filename;
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
            return redirect('/agents/doc_management/transactions/listings/listing_required_details/' . $Listing_ID);
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

        TransactionChecklists::CreateListingChecklist($checklist_id, $request -> Listing_ID, $listing_details -> Agent_ID, 'seller', 'listing', $property_type_id, $property_sub_type_id, $sale_rent, $mls_search_details -> StateOrProvince, $location_id);

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
