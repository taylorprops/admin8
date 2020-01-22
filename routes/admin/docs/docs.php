<?php
use Illuminate\Support\Facades\Route;

/****************** COMPONENT PAGES: DOCUMENT MANAGEMENT ******************/

// ######### ADMIN ONLY ##########//
Route::middleware('admin') -> group(function () {

    /* Upload page */
    // Route::get('/doc_management/create/upload/add/{state?}/{id?}', 'DocManagement\Create\UploadController@upload_file_page');

    /* List of uploads */
    Route::get('/doc_management/create/upload/files', 'DocManagement\Create\UploadController@get_uploaded_files') -> name('create.upload.files');

    /* Add fields page */
    Route::get('/doc_management/create/add_fields/{file_id}', 'DocManagement\Fill\FieldsController@add_fields');

    /* Resources | Add/remove associations, tags, etc. */
    Route::get('/doc_management/resources/resources', 'DocManagement\Resources\ResourcesController@resources');

    /* Checklists  */
    Route::get('/doc_management/checklists', 'DocManagement\Checklists\ChecklistsController@checklists');



});

// ######### ADMIN AND AGENTS ##########//
Route::middleware(['admin', 'agent']) -> group(function () {

    // List of docs to fill fields
    Route::get('/doc_management/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/doc_management/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');

});

/****************** END COMPONENT: DOCUMENT MANAGEMENT ******************/




//************************** COMPONENT DATA: DOCUMENT MANAGEMENT **************************//

/**********  DATA - ADD/EDIT/DELETE /**********/


// ######### ADMIN AND AGENTS ##########//
Route::middleware(['admin', 'agent']) -> group(function () {

    // Upload //
    Route::post('/doc_management/upload_file', 'DocManagement\Create\UploadController@upload_file') -> name('doc_management.upload_file');
    // Edit uploaded File
    Route::post('/doc_management/save_file_edit', 'DocManagement\Create\UploadController@save_file_edit');
    // Duplicate uploaded files
    Route::post('/doc_management/duplicate_upload', 'DocManagement\Create\UploadController@duplicate_upload');
    // Activate/Deactivate uploaded files
    Route::post('/doc_management/activate_upload', 'DocManagement\Create\UploadController@activate_upload');
    // Publish upload
    Route::post('/doc_management/publish_upload', 'DocManagement\Create\UploadController@publish_upload');
    // Delete uploaded files
    Route::post('/doc_management/delete_upload', 'DocManagement\Create\UploadController@delete_upload');
    // Replace uploaded files in checklists
    Route::post('/doc_management/replace_upload', 'DocManagement\Create\UploadController@replace_upload');

    /* Add Resource  */
    Route::post('/doc_management/resources/add', 'DocManagement\Resources\ResourcesController@resources_add');
    /* Save edit Resources | Add/remove associations, tags, etc. */
    Route::post('/doc_management/resources/edit', 'DocManagement\Resources\ResourcesController@resources_edit');
    /* Delete Resources  */
    Route::post('/doc_management/resources/delete', 'DocManagement\Resources\ResourcesController@resources_delete');
    /* Reorder Resources */
    Route::post('/doc_management/resources/reorder', 'DocManagement\Resources\ResourcesController@resources_reorder');

    // Fields //
    Route::post('/doc_management/save_add_fields', 'DocManagement\Fill\FieldsController@save_add_fields');
    Route::post('/doc_management/save_fill_fields', 'DocManagement\Fill\FieldsController@save_fill_fields');

    // Export filled fields to pdf
    Route::post('/doc_management/save_pdf_client_side', 'DocManagement\Fill\FieldsController@save_pdf_client_side');

    /* checklists */
    /* Add Checklists */
    Route::post('/doc_management/add_checklist', 'DocManagement\Checklists\ChecklistsController@add_checklist');
    /* Edit Checklists */
    Route::post('/doc_management/edit_checklist', 'DocManagement\Checklists\ChecklistsController@edit_checklist');
    /* Delete Checklists */
    Route::post('/doc_management/delete_checklist', 'DocManagement\Checklists\ChecklistsController@delete_checklist');
    /* Reorder Checklists */
    Route::post('/doc_management/reorder_checklists', 'DocManagement\Checklists\ChecklistsController@reorder_checklists');

    /* Add Checklist Items */
    Route::post('/doc_management/add_checklist_items', 'DocManagement\Checklists\ChecklistsController@add_checklist_items');



    /**********  DATA - GET /**********/

    /* get common fields for select options */
    Route::get('/doc_management/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields');

    // get updated list of form_group files after adding a new one
    Route::get('/doc_management/get_form_group_files', 'DocManagement\Create\UploadController@get_form_group_files') -> name('doc_management.get_form_group_files');

    // get upload details for edit
    Route::get('/doc_management/get_upload_details', 'DocManagement\Create\UploadController@get_upload_details');

    // get checklist after adding
    Route::get('/doc_management/get_checklists', 'DocManagement\Checklists\ChecklistsController@get_checklists');

    // get checklist items
    Route::get('/doc_management/get_checklist_items', 'DocManagement\Checklists\ChecklistsController@get_checklist_items');

    // get checklist item details
    Route::get('/doc_management/get_checklist_item_details', 'DocManagement\Checklists\ChecklistsController@get_checklist_item_details');

    // get details to replace form in checklist
    Route::get('/doc_management/get_replace_upload_details', 'DocManagement\Create\UploadController@get_replace_upload_details');


});

//************************** END COMPONENT: DOCUMENT MANAGEMENT **************************//
