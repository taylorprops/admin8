<?php

//////////////////////////////////////////////////////////////////
//                      DOC MANAGEMENT                          //
//////////////////////////////////////////////////////////////////

/***********  PAGES ************/
// List of all files
Route::get('/', 'UploadController@get_docs');
Route::get('/doc_management', 'UploadController@get_docs') -> name('doc_management');
// Upload page
Route::get('/upload', function () {
    return view('/doc_management/upload/upload');
});
// Add fields page
Route::get('/add_fields/{file_id}', 'UploadController@add_fields');


/***********  AJAX ************/

///  DATA ADD/EDIT/DELETE ///
// upload file ajax route
Route::post('/upload_file', 'UploadController@upload_file') -> name('upload_file');

/// DATA RETRIEVE ///
// Fields
Route::get('/common_fields', 'FieldsController@get_common_fields') -> name('common_fields');
Route::get('/save_fields', 'FieldsController@save_fields') -> name('save_fields');

//////////////////////////////////////////////////////////////////
//                      END DOC MANAGEMENT                      //
//////////////////////////////////////////////////////////////////