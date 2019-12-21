<?php
use Illuminate\Support\Facades\Route;

/****************** COMPONENT PAGES: DOCUMENT MANAGEMENT ******************/

Route::middleware('admin') -> group(function () {

    /* Upload page */
    // Route::get('/doc_management/create/upload/add/{state?}/{id?}', 'DocManagement\Create\UploadController@upload_file_page');

    /* List of uploads */
    Route::get('/doc_management/create/upload/files', 'DocManagement\Create\UploadController@get_uploaded_files') -> name('create.upload.files');

});

Route::middleware('agent') -> group(function () {

    /* Add fields page */
    Route::get('/doc_management/create/add_fields/{file_id}', 'DocManagement\Fill\FieldsController@add_fields');

    // List of docs to fill fields
    Route::get('/doc_management/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/doc_management/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');

});

/****************** END COMPONENT: DOCUMENT MANAGEMENT ******************/




//************************** COMPONENT DATA: DOCUMENT MANAGEMENT **************************//

/**********  DATA - ADD/EDIT/DELETE /**********/
// Upload //
Route::post('/doc_management/upload_file', 'DocManagement\Create\UploadController@upload_file') -> name('doc_management.upload_file');
// get updated list of association's files after adding a new one
Route::get('/doc_management/get_association_files', 'DocManagement\Create\UploadController@get_association_files') -> name('doc_management.get_association_files');
// get upload details for edit
Route::get('/doc_management/get_upload_details', 'DocManagement\Create\UploadController@get_upload_details');
// save edited upload
Route::post('/doc_management/save_file_edit', 'DocManagement\Create\UploadController@save_file_edit');
// Duplicate uploaded files
Route::post('/doc_management/duplicate_upload', 'DocManagement\Create\UploadController@duplicate_upload');
// Delete uploaded files
Route::post('/doc_management/delete_upload', 'DocManagement\Create\UploadController@delete_upload') -> name('doc_management.delete_upload');

// Fields //
Route::post('/doc_management/save_add_fields', 'DocManagement\Fill\FieldsController@save_add_fields');
Route::post('/doc_management/save_fill_fields', 'DocManagement\Fill\FieldsController@save_fill_fields');
/**********  DATA - GET /**********/
/* get common fields for select options */
Route::get('/doc_management/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields');
// Route::get('/field', 'DocManagement\Fill\FieldsController@get_field') -> name('field');


// Export filled fields to pdf
Route::post('/doc_management/save_pdf_client_side', 'DocManagement\Fill\FieldsController@save_pdf_client_side');


//************************** END COMPONENT: DOCUMENT MANAGEMENT **************************//



