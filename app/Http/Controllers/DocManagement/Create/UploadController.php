<?php

namespace App\Http\Controllers\DocManagement\Create;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Upload;

class UploadController extends Controller {



    public function get_docs(Request $request) {

        $files = Upload::select('file_name_orig', 'file_id') -> groupBy('file_id', 'file_name_orig') -> get() -> toArray();
        return view('/doc_management/create/upload/files', ['files' => $files]);
    }

    public function upload_file(Request $request) {
        // make slashes work on win and linux
        $ds       = DIRECTORY_SEPARATOR;
        $doc_root = preg_replace('[/]', $ds, $_SERVER['DOCUMENT_ROOT']);
        $doc_root = preg_replace('[\\\]', $ds, $doc_root);

        $files = $request -> file('file_upload');

        foreach ($files as $file) {

            $orig_name = $file -> getClientOriginalName();
            $name      = date('YmdHis').'_'.str_replace(' ', '', $file -> getClientOriginalName());
            $ext       = $file -> getClientOriginalExtension();
            $file_id   = date('YmdHis');

            $file_path = 'ajax' . $ds . 'upload' . $ds . 'uploads' . $ds . $file_id;
            $dir       = $doc_root . $file_path;
            mkdir($dir);

            $file_path_image = 'ajax' . $ds . 'upload' . $ds . 'uploads' . $ds . $file_id . $ds . 'images';
            $image_dir       = $dir . $ds . 'images' . $ds;
            mkdir($image_dir);

            if ($file -> isValid()) {
                $file -> move($dir, $name);
            }

            $new_name      = str_replace($ext, 'jpg', $name);
            $new_image_location = $image_dir . $ds . $new_name;
            //-compress lzw
            $convert = exec('magick -density 200 -quality 100 ' . $dir . $ds . $name . ' -background white -alpha remove -strip ' . $new_image_location, $output, $return);

            sleep(1);
            if (!$return) {
                sleep(1);
            }

            $saved_images_directory = directory($image_dir);
            $images_total = count($saved_images_directory);

            foreach ($saved_images_directory as $saved_image) {

                $start_pos = strrpos($saved_image, '-');
                if($start_pos !== false) {
                    $start_pos = strrpos($saved_image, '-') + 1;
                    $end_pos = strrpos($saved_image, '.');
                    $chars = $end_pos - $start_pos;
                    $page_number = substr($saved_image, $start_pos, $chars);
                    $page_number += 1;
                } else {
                    $page_number = 1;
                }

                $upload                = new Upload();
                $upload -> file_id       = $file_id;
                $upload -> file_name     = $saved_image;
                $upload -> file_name_orig = $orig_name;
                $upload -> file_location = $ds . $file_path_image . $ds . $saved_image;
                $upload -> images_total = $images_total;
                $upload -> page_number = $page_number;
                $upload -> save();

            }

        }

        return('success');

    }
}
