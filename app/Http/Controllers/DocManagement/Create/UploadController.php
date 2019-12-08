<?php

namespace App\Http\Controllers\DocManagement\Create;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Upload;
use App\Models\DocManagement\UploadImages;
use App\Models\DocManagement\UploadPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class UploadController extends Controller {
    public function delete_upload(Request $request) {
        $delete_file = Upload::where('file_id', $request -> file_id) -> delete();
    }

    public function get_docs(Request $request) {

        $files = Upload::select('file_name_orig', 'file_id') -> groupBy('file_id', 'file_name_orig') -> get() -> toArray();
        return view('/doc_management/create/upload/files', ['files' => $files]);
    }

    public function upload_file(Request $request) {

        $files = $request -> file('file_upload');
        // switch all to linux slashes so it works on windows and linux - use only forward slashes except for various server functions on windows
        $doc_root = $_SERVER['DOCUMENT_ROOT'];

        foreach ($files as $file) {

            // get file parts
            $file_id = date('YmdHis');
            $orig_name = $file -> getClientOriginalName();
            $name = $file_id . '_' . str_replace(' ', '_', $file -> getClientOriginalName());
            $ext = $file -> getClientOriginalExtension();

            // set directories
            $upload_dir = 'doc_management/uploads/' . $file_id;
            $upload_dir_pages = $upload_dir . '/pages';
            $upload_dir_images = $upload_dir . '/images';
            // create directories
            if (!Storage::disk('public') -> exists($upload_dir_pages)) {
                Storage::disk('public') -> makeDirectory($upload_dir_pages);
            }
            if (!Storage::disk('public') -> exists($upload_dir_images)) {
                Storage::disk('public') -> makeDirectory($upload_dir_images);
            }
            if (!Storage::disk('public') -> put($upload_dir . '/' . $name, file_get_contents($file))) {
                return false;
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
            $images_total = count($saved_images_directory);
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
                $upload -> pages_total = $images_total;
                $upload -> page_number = $page_number;
                $upload -> save();

            }

            $pages_dir = 'public/'.$upload_dir_pages;
            $saved_pages_directory = Storage::files($pages_dir);
            $pages_total = count($saved_pages_directory);
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
            // add original file to database
            $upload = new Upload();
            $upload -> file_id = $file_id;
            $upload -> file_location = 'storage/'.$upload_dir . '/' . $name;
            $upload -> file_name = $name;
            $upload -> file_name_orig = $orig_name;
            $upload -> pages_total = $pages_total;
            $upload -> save();

        }

        return ('success');

    }
}
