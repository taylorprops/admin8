<?php

    // ++++++ Doc Management ++++++ //

    // Global functions
    Route::get('/agents/doc_management/global_functions/get_location_details', 'Agents\DocManagement\Functions\GlobalFunctionsController@get_location_details');

    // Add new transaction
    Route::get('/agents/doc_management/transactions/listings/add_listing', 'Agents\DocManagement\Transactions\AddTransactionController@add_listing');
    Route::get('/agents/doc_management/transactions/listings/add_listing_details/{bright_type?}/{bright_id?}/{state?}/{tax_id?}', 'Agents\DocManagement\Transactions\AddTransactionController@add_listing_details');
    Route::get('/agents/doc_management/transactions/get_property_info', 'Agents\DocManagement\Transactions\AddTransactionController@get_property_info');
    Route::get('/agents/doc_management/transactions/contracts/add_contract', 'Agents\DocManagement\Transactions\AddTransactionController@add_contract');
    Route::get('/agents/doc_management/transactions/update_county_select', 'Agents\DocManagement\Transactions\AddTransactionController@update_county_select');


    // List of docs to fill fields
    Route::get('/doc_management/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/doc_management/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');
    /* get common fields for select options */
    Route::get('/doc_management/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields');


