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


class UploadController extends Controller {

    public function duplicate_upload(Request $request) {

        $upload_id = $request -> upload_id;
        // copy files and get new storage location
        // set published = 'no'

        // create directories
        if (!Storage::disk('public') -> exists($upload_dir_pages)) {
            Storage::disk('public') -> makeDirectory($upload_dir_pages);
        }
        if (!Storage::disk('public') -> exists($upload_dir_images)) {
            Storage::disk('public') -> makeDirectory($upload_dir_images);
        }


        $upload = Upload::find($upload_id);
        $copy = $upload -> replicate();
        $copy -> id = $new_id;
        $copy -> data = $new_data;
        $copy -> save();

    }

    public function save_file_edit(Request $request) {
        $file_id = $request -> edit_file_id;
        $file_name_display = $request -> edit_file_name_display;
        $state = $request -> edit_state;
        $association_id = $request -> edit_association_id;
        $sale_type = implode(',', $request -> edit_sale_type);

        $upload = Upload::where('file_id', $file_id) -> first();
        $upload -> file_name_display = $file_name_display;
        $upload -> state = $state;
        $upload -> sale_type = $sale_type;
        $upload -> association_id = $association_id;
        $upload -> save();
    }

    public function delete_upload(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> delete();
        $upload_dir = base_path().'/storage/app/public/doc_management/uploads/' . $upload_id;
        exec('rm -r '.$upload_dir);
    }

    public function get_upload_details(Request $request) {
        $upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        return $upload;
    }

    public function get_uploaded_files(Request $request) {

        $association_id = $request -> association_id ?: null;
        //$files = Upload::groupBy('file_id', 'file_name_orig') -> get();
        $files = Upload::orderBy('file_name_display') -> get();
        $states = Zips::States();
        $associations = Associations::GetAssociations();

        return view('/doc_management/create/upload/files', compact('files', 'states', 'associations', 'association_id')) -> withModel($associations);
    }

    public function get_association_files(Request $request) {

        $association_id = $request -> association_id;
        $state = $request -> state;
        $files = Upload::where('association_id', $association_id) -> orderBy('file_name_display') -> get();

        $files_count = count($files);

        return view('/doc_management/create/upload/get_association_files_html', compact('files', 'files_count', 'association_id', 'state'));
    }

    public function upload_file_page(Request $request) {

        $state = $request -> state;
        $id = $request -> id;
        $association_details = [];
        $association_details['state'] = $state;
        $association_details['id'] = $id;

        $states = Zips::States();
        $associations = Associations::GetAssociations();

        return view('/doc_management/create/upload/upload', compact('states', 'associations', 'association_details'));
    }

    public function upload_file(Request $request) {

        $file = $request -> file('file_upload');

        if($file) {

            $orig_name = $file -> getClientOriginalName();
            $date = date('YmdHis');
            $filename = $file -> getClientOriginalName();
            $name = $date . "_" . preg_replace("/[\s\(\)]+/", "_", $filename);
            $ext = $file -> getClientOriginalExtension();
            $state = $request['state'];
            $sale_type = implode(',', $request['sale_type']);
            $association_id = $request['association_id'];
            $file_name_display = $request['file_name_display'];

            $pages_total = exec('pdftk '.$file.' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            // add original file to database
            $upload = new Upload();
            $upload -> file_name = $name;
            $upload -> file_name_orig = $orig_name;
            $upload -> file_name_display = $file_name_display;
            $upload -> pages_total = $pages_total;
            $upload -> state = $state;
            $upload -> sale_type = $sale_type;
            $upload -> association_id = $association_id;
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
            if (!Storage::disk('public') -> put($storage_dir . '/' . $name, file_get_contents($file))) {
                return false;
            }

            // update directory path in database
            $upload -> file_location = $storage_public_path . '/' . $name;
            $upload -> save();

            // create directories
            if (!Storage::disk('public') -> exists($storage_dir_pages)) {
                Storage::disk('public') -> makeDirectory($storage_dir_pages);
            }
            if (!Storage::disk('public') -> exists($storage_dir_images)) {
                Storage::disk('public') -> makeDirectory($storage_dir_images);
            }

            // new file name
            $new_name = str_replace($ext, 'jpg', $name);

            // add individual pages to pages directory
            $input_file = $storage_full_path . '/' . $name;
            $output_files = $storage_path.'/'.$storage_dir_pages. '/page_%02d.pdf';
            $output_images = $storage_path.'/'.$storage_dir_images .'/' . $new_name;

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
                /*
                // get page number from name
                $start_pos = strrpos($saved_image, '-');
                if ($start_pos !== false) {
                    $start_pos = $start_pos + 1;
                    $end_pos = strrpos($saved_image, '.');
                    $chars = $end_pos - $start_pos;
                    $page_number = substr($saved_image, $start_pos, $chars);
                    if (preg_match('/[0-9]{1,2}/', $page_number)) {
                        $page_number += 1;
                    } else {
                        $page_number = 1;
                    }
                } else {
                    $page_number = 1;
                } */
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

            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                if (preg_match('/page_/', $saved_page)) {
                    // get page number from name
                    $start_pos = strrpos($saved_page, '_');
                    if ($start_pos !== false) {
                        $start_pos = $start_pos + 1;
                        $end_pos = strrpos($saved_page, '.');
                        $chars = $end_pos - $start_pos;
                        $page_number = substr($saved_page, $start_pos, $chars);

                    } else {
                        $page_number = 1;
                    }

                    //$saved_page = str_replace('jpg', 'pdf', $saved_page);
                    $upload = new UploadPages();
                    $upload -> file_id = $file_id;
                    $upload -> file_name = $pages_file_name;
                    $upload -> file_location = '/' . $pages_public_path . '/' . $pages_file_name;
                    $upload -> pages_total = $pages_total;
                    $upload -> page_number = $page_number;
                    $upload -> save();

                }

            }
            $success = json_encode(['success' => true]);
            return ($success);

            /*





            // set directories
            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            $upload_dir = 'doc_management/uploads/' . $file_id;
            $upload_dir_pages = $upload_dir . '/pages';
            $upload_dir_images = $upload_dir . '/images';

            if (!Storage::disk('public') -> put($upload_dir . '/' . $name, file_get_contents($file))) {
                return false;
            }

            $upload -> file_location = 'storage/'.$upload_dir . '/' . $name;
            $upload -> save();

            // create directories
            if (!Storage::disk('public') -> exists($upload_dir_pages)) {
                Storage::disk('public') -> makeDirectory($upload_dir_pages);
            }
            if (!Storage::disk('public') -> exists($upload_dir_images)) {
                Storage::disk('public') -> makeDirectory($upload_dir_images);
            }

            // new file name
            $new_name = str_replace($ext, 'jpg', $name);

            // get full path for pdftk
            $full_path_dir = $doc_root . 'storage/'. $upload_dir;

            // add individual pages to pages directory
            // reverse the slashes to work only for splitting pdfs
            $full_path_page_dir = $full_path_dir . '/' . $name;
            $pdf_output_dir = $doc_root . '/storage/'.$upload_dir_pages. '/page_%02d.pdf';

            exec('pdftk ' . $full_path_page_dir . ' burst output ' . $pdf_output_dir);
            exec('rm '.$doc_root . '/storage/'.$upload_dir_pages. '/doc_data.txt');

            // add individual images to images directory
            $full_path_new_image_dir = $full_path_dir . '/images/' . $new_name;
            $convert = exec('convert -density 200 -quality 100 ' . $full_path_dir . '/' . $name . ' -background white -alpha remove -strip ' . $full_path_new_image_dir, $output, $return);

            // images not converted give an extra seconds
            sleep(1);
            if (!$return) {
                sleep(1);
            }

            // get all image files images_storage_path to use as file location
            $images_dir = 'public/'.$upload_dir_images;
            $saved_images_directory = Storage::files($images_dir);
            $images_storage_path = Storage::url($images_dir);

            foreach ($saved_images_directory as $saved_image) {
                // get just filename
                $images_file_name = basename($saved_image);
                // get page number from name
                $start_pos = strrpos($saved_image, '-');
                if ($start_pos !== false) {
                    $start_pos = $start_pos + 1;
                    $end_pos = strrpos($saved_image, '.');
                    $chars = $end_pos - $start_pos;
                    $page_number = substr($saved_image, $start_pos, $chars);
                    if (preg_match('/[0-9]{1,2}/', $page_number)) {
                        $page_number += 1;
                    } else {
                        $page_number = 1;
                    }
                } else {
                    $page_number = 1;
                }
                // add images to database
                $upload = new UploadImages();
                $upload -> file_id = $file_id;
                $upload -> file_name = $images_file_name;
                $upload -> file_location = $images_storage_path . '/' . $images_file_name;
                $upload -> pages_total = $pages_total;
                $upload -> page_number = $page_number;
                $upload -> save();

            }

            $pages_dir = 'public/'.$upload_dir_pages;
            $saved_pages_directory = Storage::files($pages_dir);
            $pages_storage_path = Storage::url($pages_dir);

            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                if (preg_match('/page_/', $saved_page)) {
                    // get page number from name
                    $start_pos = strrpos($saved_page, '_');
                    if ($start_pos !== false) {
                        $start_pos = $start_pos + 1;
                        $end_pos = strrpos($saved_page, '.');
                        $chars = $end_pos - $start_pos;
                        $page_number = substr($saved_page, $start_pos, $chars);

                    } else {
                        $page_number = 1;
                    }

                    //$saved_page = str_replace('jpg', 'pdf', $saved_page);
                    $upload = new UploadPages();
                    $upload -> file_id = $file_id;
                    $upload -> file_name = $pages_file_name;
                    $upload -> file_location = $pages_storage_path . '/' . $pages_file_name;
                    $upload -> pages_total = $pages_total;
                    $upload -> page_number = $page_number;
                    $upload -> save();

                }

            }
            $success = json_encode(['success' => true]);
            return ($success);

            */

        }

    }
}
