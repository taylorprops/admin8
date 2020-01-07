<?php

namespace App\Http\Controllers\DocManagement\Create;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Upload;
use App\Models\DocManagement\UploadImages;
use App\Models\DocManagement\UploadPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Zips;
use App\Models\DocManagement\Associations;
use App\Models\DocManagement\Fields;
use App\Models\DocManagement\FieldInputs;
use App\Models\DocManagement\FilledFields;
use App\Models\DocManagement\ResourceItems;
use Illuminate\Support\Facades\Schema;


class UploadController extends Controller {

    /****************** RESOURCES ******************/
    public function resources() {
        $states = Zips::States();
        $resources = ResourceItems::groupBy('resource_type') -> get();
        $resources_items = ResourceItems::orderBy('resource_order') -> get();
        return view('/doc_management/create/upload/resources', compact('resources', 'resources_items', 'states'));
    }

    public function resources_reorder(Request $request) {

        $data = json_decode($request['data'], true);
        $data = $data['resource'];

        foreach($data as $item) {
            $resource_id = $item['resource_id'];
            $resource_order = $item['resource_index'];
            $reorder = ResourceItems::whereResourceId($resource_id) -> first();
            $reorder -> resource_order = $resource_order;
            $reorder -> save();
        }

    }

    public function resources_add(Request $request) {

        $resource_type = $request -> resource_type;
        $resource_type_title = ucwords(str_replace('_', ' ', $resource_type));
        $resource_name = $request -> resource_name;
        $resource_state = $request -> resource_state;
        $resource_color = $request -> resource_color;
        $resource_association = $request -> resource_association;
        $resource_county_abbr = $request -> resource_county_abbr;

        // get default values from existing
        // $defaults = ResourceItems::whereResourceType($resource_type) -> first();
        // $resource_type_title = $defaults -> resource_type_title;

        $resource_item = new ResourceItems();
        $resource_item -> resource_type = $resource_type;
        $resource_item -> resource_type_title = $resource_type_title;
        $resource_item -> resource_name = $resource_name;
        $resource_item -> resource_state = $resource_state;
        $resource_item -> resource_color = $resource_color;
        $resource_item -> resource_association = $resource_association;
        $resource_item -> resource_county_abbr = $resource_county_abbr;
        $resource_item -> resource_order = 0;
        $resource_item -> save();
        return $resource_item -> resource_id;

    }

    public function resources_edit(Request $request) {

        $resource_item = ResourceItems::whereResourceId($request -> resource_id) -> first();
        $resource_item -> resource_name = $request -> resource_name;
        $resource_item -> resource_state = $request -> resource_state;
        $resource_item -> resource_color = $request -> resource_color;
        $resource_item -> resource_association = $request -> resource_association;
        $resource_item -> resource_county_abbr = $request -> resource_county_abbr;
        $resource_item -> save();

    }

    public function resources_delete(Request $request) {

        $resource_item = ResourceItems::whereResourceId($request -> resource_id) -> delete();

    }
    /****************** RESOURCES ******************/

    /****************** UPLOADS ******************/
    public function publish_upload(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        $upload -> published = 'yes';
        $upload -> save();
    }

    public function duplicate_upload(Request $request) {

        $upload_id = $request -> upload_id;

        // insert copy to get new id to use to create folder. update file location after
        $upload = Upload::find($upload_id);
        $upload_copy = $upload -> replicate();
        $upload_copy -> save();
        $file_id = $upload_copy -> file_id;

        $uploads_path = base_path().'/storage/app/public/doc_management/uploads';
        exec('cp -r '.$uploads_path.'/'.$upload_id.' '.$uploads_path.'/'.$file_id);

        $copy_path = str_replace('/'.$upload_id.'/', '/'.$file_id.'/', $upload -> file_location);
        // update file location
        $upload_copy -> file_location = $copy_path;
        $upload_copy -> published = 'no';
        $upload_copy -> save();

        // copy db data for admin.docs_fields, admin.docs_filled_fields_values, admin.docs_fields_inputs
        $data_sets = [ UploadImages::where('file_id', $upload_id) -> get(), UploadPages::where('file_id', $upload_id) -> get() ];

        foreach ($data_sets as $data_set) {
            foreach($data_set as $row) {
                $copy = $row -> replicate();
                $copy -> file_id = $file_id;
                $path = str_replace('/'.$upload_id.'/', '/'.$file_id.'/', $row -> file_location);
                $copy -> file_location = $path;
                $copy -> save();
            }
        }

        $data_sets = [ Fields::where('file_id', $upload_id) -> get(), FieldInputs::where('file_id', $upload_id) -> get() ];

        foreach ($data_sets as $data_set) {
            foreach($data_set as $row) {
                $copy = $row -> replicate();
                $copy -> file_id = $file_id;
                $copy -> save();
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

    public function delete_upload(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> delete();
        $upload = Fields::where('file_id', $upload_id) -> delete();
        $upload = FieldInputs::where('file_id', $upload_id) -> delete();
        $upload = FilledFields::where('file_id', $upload_id) -> delete();
        $upload_dir = base_path().'/storage/app/public/doc_management/uploads/' . $upload_id;
        exec('rm -r '.$upload_dir);
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

        return view('/doc_management/create/upload/files', compact('files', 'states', 'resources', 'form_group_id', 'resource_items'));
        //  -> withModel($associations)
    }

    public function get_form_group_files(Request $request) {

        $form_group_id = $request -> form_group_id;
        $state = $request -> state;
        $resource_items = new ResourceItems();
        $files = Upload::where('form_group_id', $form_group_id) -> orderBy('file_name_display') -> get();

        $files_count = count($files);

        return view('/doc_management/create/upload/get_form_group_files_html', compact('files', 'files_count', 'form_group_id', 'state', 'resource_items'));
    }

    /* public function upload_file_page(Request $request) {

        $state = $request -> state;
        $id = $request -> id;
        $association_details = [];
        $association_details['state'] = $state;
        $association_details['id'] = $id;

        $states = Zips::States();

        $resources = ResourceItems::orderBy('resource_order') -> get();

        return view('/doc_management/create/upload/upload', compact('states', 'resources', 'association_details'));
    } */

    public function upload_file(Request $request) {

        $file = $request -> file('file_upload');

        if($file) {

            $file_name_orig = $file -> getClientOriginalName();
            $date = date('YmdHis');
            $filename = $file -> getClientOriginalName();
            $ext = $file -> getClientOriginalExtension();
            $file_name_no_ext = str_replace('.'.$ext, '', $filename);
            $clean_filename = sanitize($file_name_no_ext);
            $new_filename = $date . '_' . $clean_filename.'.'.$ext;
            $state = $request['state'];
            $sale_type = implode(',', $request['sale_type']);
            $form_group_id = $request['form_group_id'];
            $file_name_display = $request['file_name_display'];

            $pages_total = exec('pdftk '.$file.' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

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

            // set paths
            $base_path = base_path();
            $storage_path = $base_path.'/storage/app/public';
            $storage_dir = 'doc_management/uploads/' . $file_id;
            $storage_dir_pages = $storage_dir.'/pages';
            $storage_dir_images = $storage_dir.'/images';
            $storage_public_path = 'storage/doc_management/uploads/' . $file_id;
            $storage_full_path = $storage_path.'/doc_management/uploads/' . $file_id;

            // add upload to upload dir
            if (!Storage::disk('public') -> put($storage_dir . '/' . $new_filename, file_get_contents($file))) {
                return false;
            }

            // update directory path in database
            $upload -> file_location = $storage_public_path . '/' . $new_filename;
            $upload -> save();

            // create directories
            if (!Storage::disk('public') -> exists($storage_dir_pages)) {
                Storage::disk('public') -> makeDirectory($storage_dir_pages);
            }
            if (!Storage::disk('public') -> exists($storage_dir_images)) {
                Storage::disk('public') -> makeDirectory($storage_dir_images);
            }

            // new file name
            $new_image_name = str_replace($ext, 'jpg', $new_filename);

            // add individual pages to pages directory
            $input_file = $storage_full_path . '/' . $new_filename;
            $output_files = $storage_path.'/'.$storage_dir_pages. '/page_%02d.pdf';
            $output_images = $storage_path.'/'.$storage_dir_images .'/' . $new_image_name;

            exec('pdftk ' . $input_file . ' burst output ' . $output_files);
            // remove data file
            exec('rm '.$storage_path.'/'.$storage_dir_pages. '/doc_data.txt');

            // add individual images to images directory
            $convert = exec('convert -density 200 -quality 100 ' . $input_file . ' -background white -alpha remove -strip ' . $output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files('public/'.$storage_dir.'/images');
            $images_public_path = $storage_public_path.'/images';

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

            $saved_pages_directory = Storage::files('public/'.$storage_dir.'/pages');
            $pages_public_path = $storage_public_path.'/pages';

            $page_number = 1;
            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);

                // get page number from name
                /* $start_pos = strrpos($saved_page, '_');
                if ($start_pos !== false) {
                    $start_pos = $start_pos + 1;
                    $end_pos = strrpos($saved_page, '.');
                    $chars = $end_pos - $start_pos;
                    $page_number = substr($saved_page, $start_pos, $chars);

                } else {
                    $page_number = 1;
                } */

                //$saved_page = str_replace('jpg', 'pdf', $saved_page);
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
    /****************** UPLOADS ******************/
}
