<?php

    // ++++++ Doc Management ++++++ //

    // Global functions
    Route::get('/agents/doc_management/global_functions/get_location_details', 'Agents\DocManagement\Functions\GlobalFunctionsController@get_location_details');

    // Add new listing
    Route::get('/agents/doc_management/transactions/listings/listing_add', 'Agents\DocManagement\Transactions\Listings\ListingAddController@add_listing_page');

    Route::get('/agents/doc_management/transactions/listings/listing_add_details_existing/{state?}/{tax_id?}/{bright_type?}/{bright_id?}', 'Agents\DocManagement\Transactions\Listings\ListingAddController@add_listing_details_existing');

    Route::get('/agents/doc_management/transactions/listings/listing_add_details_new/{street_number?}/{street_name?}/{city?}/{state?}/{zip?}/{county?}/{street_dir?}/{unit_number?}', 'Agents\DocManagement\Transactions\Listings\ListingAddController@add_listing_details_new');

    // axios calls
    Route::post('/agents/doc_management/transactions/save_add_listing', 'Agents\DocManagement\Transactions\Listings\ListingAddController@save_add_listing');

    // Add new contract
    Route::get('/agents/doc_management/transactions/add_contract/add_contract', 'Agents\DocManagement\Transactions\ContractAddController@add_contract');

    // TODO this is shared by add listing and add contract
    // Add listing and contract
    Route::get('/agents/doc_management/transactions/get_property_info', 'Agents\DocManagement\Transactions\Listings\ListingAddController@get_property_info');
    Route::get('/agents/doc_management/transactions/update_county_select', 'Agents\DocManagement\Transactions\Listings\ListingAddController@update_county_select');


    // listing page
    Route::get('/transactions/listings/listing_details/{id}', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@listing');




    // List of docs to fill fields
    Route::get('/doc_management/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/doc_management/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');
    /* get common fields for select options */
    Route::get('/doc_management/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields');


