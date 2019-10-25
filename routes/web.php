<?php


// PAGE List of all files
Route::get('/', 'UploadController@get_docs');
Route::get('/doc_management', 'UploadController@get_docs') -> name('doc_management');
// PAGE Upload page
Route::get('/upload', function () {
    return view('/doc_management/upload/upload');
});
// upload file ajax route
Route::post('/upload_file', 'UploadController@upload_file') -> name('upload_file');
// PAGE Add fields page
Route::get('/add_fields/{file_id}', 'UploadController@add_fields');

// Fields
Route::get('/common_fields', 'FieldsController@get_common_fields') -> name('common_fields');