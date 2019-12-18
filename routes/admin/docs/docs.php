<?php
use Illuminate\Support\Facades\Route;

/****************** COMPONENT PAGES: DOCUMENT MANAGEMENT ******************/

Route::middleware('admin') -> group(function () {

    /* Upload page */
    Route::get('/create/upload', 'DocManagement\Create\UploadController@upload_file_page');

    /* List of uploads */
    Route::get('/create/upload/files', 'DocManagement\Create\UploadController@get_docs') -> name('create.upload.files');

});

Route::middleware('agent') -> group(function () {

    /* Add fields page */
    Route::get('/create/add_fields/{file_id}', 'DocManagement\Fill\FieldsController@add_fields');

    // List of docs to fill fields
    Route::get('/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');

});

/****************** END COMPONENT: DOCUMENT MANAGEMENT ******************/




//************************** COMPONENT DATA: DOCUMENT MANAGEMENT **************************//

/**********  DATA - ADD/EDIT/DELETE /**********/
// Upload //
Route::post('/upload_file', 'DocManagement\Create\UploadController@upload_file') -> name('upload_file');

// Delete uploaded files
Route::post('/delete_upload', 'DocManagement\Create\UploadController@delete_upload') -> name('delete_upload');

// Fields //
Route::post('/save_add_fields', 'DocManagement\Fill\FieldsController@save_add_fields') -> name('save_add_fields');
Route::post('/save_fill_fields', 'DocManagement\Fill\FieldsController@save_fill_fields') -> name('save_fill_fields');
/**********  DATA - GET /**********/
/* get common fields for select options */
Route::get('/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields') -> name('common_fields');
Route::get('/field', 'DocManagement\Fill\FieldsController@get_field') -> name('field');


// Export filled fields to pdf
Route::post('/save_pdf_client_side', 'DocManagement\Fill\FieldsController@save_pdf_client_side');


//************************** END COMPONENT: DOCUMENT MANAGEMENT **************************//



