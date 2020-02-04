<?php

namespace App\Http\Controllers\DocManagement\Create;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\FieldInputs;
use App\Models\DocManagement\Fields;
use App\Models\DocManagement\FilledFields;
use App\Models\DocManagement\ResourceItems;
use App\Models\DocManagement\Upload;
use App\Models\DocManagement\UploadImages;
use App\Models\DocManagement\UploadPages;
use App\Models\DocManagement\Zips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller {

    public function add_form_get_checklist_items(Request $request) {

        $file_id = $request -> file_id;
        // get form details just to display
        $uploaded_file = Upload::where('file_id', $file_id) -> first();
        $checklist_id = $request -> checklist_id;
        $checklist_items = new ChecklistsItems();
        $items = ChecklistsItems::where('checklist_id', $checklist_id) -> get();
        $upload = new Upload();

        return view('/doc_management/create/upload/get_checklist_items_html', compact('file_id', 'uploaded_file', 'checklist_id','checklist_items', 'items', 'upload'));

    }

    public function activate_upload(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        $upload -> active = $request -> active;
        $upload -> save();
    }

    public function delete_upload(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> delete();
        $upload = Fields::where('file_id', $upload_id) -> delete();
        $upload = FieldInputs::where('file_id', $upload_id) -> delete();
        $upload = FilledFields::where('file_id', $upload_id) -> delete();
        $upload_dir = base_path() . '/storage/app/public/doc_management/uploads/' . $upload_id;
        exec('rm -r ' . $upload_dir);
    }

    public function duplicate_upload(Request $request) {

        $upload_id = $request -> upload_id;

        // insert copy to get new id to use to create folder. update file location after
        $upload = Upload::find($upload_id);
        $upload_copy = $upload -> replicate();
        $upload_copy -> save();

        if ($upload -> file_location != '') {

            $file_id = $upload_copy -> file_id;
            $uploads_path = base_path() . '/storage/app/public/doc_management/uploads';
            exec('cp -r ' . $uploads_path . '/' . $upload_id . ' ' . $uploads_path . '/' . $file_id);

            $copy_path = str_replace('/' . $upload_id . '/', '/' . $file_id . '/', $upload -> file_location);
            // update file location
            $upload_copy -> file_location = $copy_path;
            $upload_copy -> published = 'no';
            $upload_copy -> save();

            // copy db data for admin.docs_fields, admin.docs_filled_fields_values, admin.docs_fields_inputs
            $data_sets = [UploadImages::where('file_id', $upload_id) -> get(), UploadPages::where('file_id', $upload_id) -> get()];

            foreach ($data_sets as $data_set) {
                foreach ($data_set as $row) {
                    $copy = $row -> replicate();
                    $copy -> file_id = $file_id;
                    $path = str_replace('/' . $upload_id . '/', '/' . $file_id . '/', $row -> file_location);
                    $copy -> file_location = $path;
                    $copy -> save();
                }
            }

            $data_sets = [Fields::where('file_id', $upload_id) -> get(), FieldInputs::where('file_id', $upload_id) -> get()];

            foreach ($data_sets as $data_set) {
                foreach ($data_set as $row) {
                    $copy = $row -> replicate();
                    $copy -> file_id = $file_id;
                    $copy -> save();
                }
            }

        } else {

            $upload_copy -> published = 'no';
            $upload_copy -> save();

        }

    }

    public function get_add_to_checklists_details(Request $request) {

        $file_id = $request -> form_id;
        // get form details just to display
        $uploaded_file = Upload::where('file_id', $file_id) -> first();
        // to run functions from ResourceItems
        $resource_items = new ResourceItems();
        // checklists to add to
        $checklists = Checklists::where('checklist_type', $request -> checklist_type) -> orderBy('checklist_state', 'ASC')
            -> orderBy('checklist_location_id', 'ASC')
            -> orderBy('checklist_represent', 'DESC')
            -> orderBy('checklist_sale_rent', 'DESC')
            -> orderBy('checklist_property_type_id', 'ASC')
            -> get();
        // checklist items to view in checklists
        $checklists_items = new ChecklistsItems();
        // options for required and form_group
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();
        $checklist_groups = $resource_items -> where('resource_type', 'checklist_groups') -> orderBy('resource_order') -> get();
        $checklist_locations = $resource_items -> where('resource_type', 'checklist_locations') -> orderBy('resource_order') -> get();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();
        // for upload functions
        $upload = new Upload();

        // get required and form group id for add to checklists
        $checklist_item_details = $checklists_items -> where('checklist_form_id', $file_id) -> first();
        $checklist_item_group_id = $checklist_item_details -> checklist_item_group_id ?? null;
        $checklist_item_required = $checklist_item_details -> checklist_item_required ?? null;

        $states = Zips::States();

        return view('/doc_management/create/upload/get_add_to_checklists_details_html', compact('file_id', 'uploaded_file', 'resource_items', 'checklists', 'checklists_items', 'form_groups', 'checklist_groups', 'checklist_locations', 'property_types', 'property_sub_types', 'upload', 'checklist_item_required', 'checklist_item_group_id', 'states'));

    }

    public function get_form_group_files(Request $request) {

        $form_group_id = $request -> form_group_id;
        $state = $request -> state;
        $order = $request -> order ?? null;

        $order_by = 'file_name_display';
        $dir = 'ASC';
        if ($order == 'added') {
            $order_by = 'created_at';
            $dir = 'DESC';
        }
        $resource_items = new ResourceItems();

        $files = Upload::where('form_group_id', $form_group_id) -> orderBy($order_by, $dir) -> get();

        $files_count = count($files);

        $checklists = new ChecklistsItems();

        return view('/doc_management/create/upload/get_form_group_files_html', compact('files', 'files_count', 'form_group_id', 'state', 'resource_items', 'checklists'));
    }

    public function get_manage_upload_details(Request $request) {

        $file_id = $request -> form_id;
        // get form details just to display
        $uploaded_file = Upload::where('file_id', $file_id) -> first();
        // all forms to select replacement from
        $uploads = Upload::where('form_group_id', $request -> form_group_id) -> where('published', 'yes') -> where('active', 'yes')
        -> whereNotIn('file_id', function ($query) use ($file_id) {
            $query -> select('checklist_form_id')
                -> from('docs_checklists_items')
                -> groupBy('checklist_form_id');
        })
        -> orderBy('file_name_display', 'ASC') -> get();
        // checklists that form is located in to display
        $checklists = Checklists::whereIn('id', function ($query) use ($file_id) {
            $query -> select('checklist_id')
                -> from('docs_checklists_items')
                -> where('checklist_form_id', $file_id);
            })
            -> orderBy('checklist_state', 'ASC')
            -> orderBy('checklist_location_id', 'ASC')
            -> orderBy('checklist_type', 'DESC')
            -> orderBy('checklist_represent', 'DESC')
            -> orderBy('checklist_sale_rent', 'DESC')
            -> orderBy('checklist_property_type_id', 'ASC')
            -> get();

        // to run functions from ResourceItems
        $resource_items = new ResourceItems();

        return view('/doc_management/create/upload/get_manage_upload_details_html', compact('file_id', 'uploaded_file', 'uploads', 'checklists', 'resource_items'));
    }

    public function get_upload_details(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        return $upload;
    }

    public function get_uploaded_files(Request $request) {

        $form_group_id = $request -> form_group_id ?: null;
        //$files = Upload::groupBy('file_id', 'file_name_orig') -> get();
        $files = Upload::orderBy('file_name_display') -> get();
        $states = Zips::States();
        //dd(ResourceItems::getTagName('11'));
        $resource_items = new ResourceItems();
        $resources = ResourceItems::orderBy('resource_order') -> get();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();

        return view('/doc_management/create/upload/files', compact('files', 'states', 'resources', 'form_group_id', 'resource_items', 'form_groups'));
        //  -> withModel($associations)
    }

    public function publish_upload(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        $upload -> published = 'yes';
        $upload -> save();
    }

    public function remove_upload(Request $request) {
        $form_id = $request -> form_id;
        ChecklistsItems::where('checklist_form_id', $form_id)
            -> delete();
    }

    public function replace_upload(Request $request) {
        $old_id = $request -> old_form_id;
        $new_id = $request -> new_form_id;

        ChecklistsItems::where('checklist_form_id', $old_id)
            -> update(['checklist_form_id' => $new_id]);
    }

    public function save_add_non_form(Request $request) {

        $file_name_display = $request -> no_form_file_name_display;
        $state = $request -> no_form_state;
        $form_group_id = $request -> no_form_form_group_id;
        $sale_type = implode(',', $request -> no_form_sale_type);

        $upload = new Upload();
        $upload -> file_name_display = $file_name_display;
        $upload -> state = $state;
        $upload -> sale_type = $sale_type;
        $upload -> form_group_id = $form_group_id;
        $upload -> save();
    }

    public function save_add_to_checklists(Request $request) {

        $file_id = $request -> file_id;
        $checklists = json_decode($request -> checklists);
        $checklists = $checklists -> checklist;
        $form_group_id = $request -> form_group_id;
        $required = $request -> required;

        // delete file from all checklists
        $delete_from_checklists = ChecklistsItems::where('checklist_form_id', $file_id) -> delete();

        foreach ($checklists as $checklist) {
            if ($checklist -> checklist_id != '') {
                $checklist_order = $checklist -> checklist_order;
                $checklist_items = new ChecklistsItems();
                $checklist_items -> checklist_id = $checklist -> checklist_id;
                $checklist_items -> checklist_form_id = $file_id;
                $checklist_items -> checklist_item_required = $required;
                $checklist_items -> checklist_item_order = $checklist_order;
                $checklist_items -> checklist_item_group_id = $form_group_id;
                $checklist_items -> save();

                $current_items = new ChecklistsItems();

                $set_order = $current_items -> where('checklist_id', $checklist -> checklist_id) -> where('checklist_item_order', '>=', $checklist_order) -> where('checklist_form_id', '!=', $file_id) -> update(['checklist_item_order' => DB::raw('checklist_item_order + 1')]);

                $current_items -> updateChecklistItemsOrder($checklist -> checklist_id);

                // set checklist count column
                $checklist_item_count = $current_items -> where('checklist_id', $checklist -> checklist_id) -> count();
                $update_count = Checklists::where('id', $checklist -> checklist_id) -> first();
                $update_count -> checklist_count = $checklist_item_count;
                $update_count -> save();

            }
        }

    }

    public function save_file_edit(Request $request) {
        $file_id = $request -> edit_file_id;
        $file_name_display = $request -> edit_file_name_display;
        $state = $request -> edit_state;
        $form_group_id = $request -> edit_form_group_id;
        $sale_type = implode(',', $request -> edit_sale_type);

        $upload = Upload::where('file_id', $file_id) -> first();
        $upload -> file_name_display = $file_name_display;
        $upload -> state = $state;
        $upload -> sale_type = $sale_type;
        $upload -> form_group_id = $form_group_id;
        $upload -> save();
    }

    public function upload_file(Request $request) {

        $file = $request -> file('file_upload');

        if ($file) {

            $file_name_orig = $file -> getClientOriginalName();
            $filename = $file_name_orig;
            $date = date('YmdHis');
            $ext = $file -> getClientOriginalExtension();
            $file_name_no_ext = str_replace('.' . $ext, '', $filename);
            $clean_filename = sanitize($file_name_no_ext);
            $new_filename = $date . '_' . $clean_filename . '.' . $ext;
            $state = $request['state'];
            $sale_type = implode(',', $request['sale_type']);
            $form_group_id = $request['form_group_id'];
            $file_name_display = $request['file_name_display'];

            $pages_total = exec('pdftk ' . $file . ' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            // add original file to database
            $upload = new Upload();
            $upload -> file_name = $new_filename;
            $upload -> file_name_orig = $file_name_orig;
            $upload -> file_name_display = $file_name_display;
            $upload -> pages_total = $pages_total;
            $upload -> state = $state;
            $upload -> sale_type = $sale_type;
            $upload -> form_group_id = $form_group_id;
            $upload -> pages_total = $pages_total;
            $upload -> save();
            $file_id = $upload -> file_id;

            $base_path = base_path();
            $storage_path = $base_path . '/storage/app/public';
            $storage_dir = 'doc_management/uploads/' . $file_id;

            if (!Storage::disk('public') -> put($storage_dir . '/' . $new_filename, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Uploaded']);
                return ($fail);
            }
            $storage_full_path = $storage_path . '/doc_management/uploads/' . $file_id;
            chmod($storage_full_path . '/' . $new_filename, 0775);

            // update directory path in database
            $storage_public_path = 'storage/doc_management/uploads/' . $file_id;
            $upload -> file_location = $storage_public_path . '/' . $new_filename;
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
            $create_images = exec('convert -density 200 -quality 100 ' . $input_file . ' -background white -alpha remove -strip ' . $output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files('public/' . $storage_dir . '/images');
            $images_public_path = $storage_public_path . '/images';

            $page_number = 1;
            foreach ($saved_images_directory as $saved_image) {
                // get just filename
                $images_file_name = basename($saved_image);

                // add images to database
                $upload = new UploadImages();
                $upload -> file_id = $file_id;
                $upload -> file_name = $images_file_name;
                $upload -> file_location = '/' . $images_public_path . '/' . $images_file_name;
                $upload -> pages_total = $pages_total;
                $upload -> page_number = $page_number;
                $upload -> save();
                $page_number += 1;

            }

            $saved_pages_directory = Storage::files('public/' . $storage_dir . '/pages');
            $pages_public_path = $storage_public_path . '/pages';

            $page_number = 1;
            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                $upload = new UploadPages();
                $upload -> file_id = $file_id;
                $upload -> file_name = $pages_file_name;
                $upload -> file_location = '/' . $pages_public_path . '/' . $pages_file_name;
                $upload -> pages_total = $pages_total;
                $upload -> page_number = $page_number;
                $upload -> save();

                $page_number += 1;

            }
            $success = json_encode(['success' => true]);
            return ($success);

        }

    }
}
