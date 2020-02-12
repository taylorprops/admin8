<?php





    // ++++++ Doc Management ++++++ //

    // Add new transaction
    Route::get('/agents/doc_management/transactions/add_listing', 'Agents\DocManagement\Transactions\AddTransactionController@add_listing');
    Route::get('/agents/doc_management/transactions/add_contract', 'Agents\DocManagement\Transactions\AddTransactionController@add_contract');


    // List of docs to fill fields
    Route::get('/doc_management/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/doc_management/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');
    /* get common fields for select options */
    Route::get('/doc_management/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields');


