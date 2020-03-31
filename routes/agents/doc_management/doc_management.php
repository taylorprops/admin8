<?php

    // ++++++ Doc Management ++++++ //

    // Global functions
    Route::get('/agents/doc_management/global_functions/get_location_details', 'Agents\DocManagement\Functions\GlobalFunctionsController@get_location_details');

    // Add new listing
    Route::get('/agents/doc_management/transactions/listings/listing_add', 'Agents\DocManagement\Transactions\Listings\ListingAddController@add_listing_page');
    // Add listing details if existing
    Route::get('/agents/doc_management/transactions/listings/listing_add_details_existing/{state?}/{tax_id?}/{bright_type?}/{bright_id?}', 'Agents\DocManagement\Transactions\Listings\ListingAddController@add_listing_details_existing');
    // Add listing details if new
    Route::get('/agents/doc_management/transactions/listings/listing_add_details_new/{street_number?}/{street_name?}/{city?}/{state?}/{zip?}/{county?}/{street_dir?}/{unit_number?}', 'Agents\DocManagement\Transactions\Listings\ListingAddController@add_listing_details_new');
    // Required Details page
    Route::get('/agents/doc_management/transactions/listings/listing_required_details/{id}', 'Agents\DocManagement\Transactions\Listings\ListingAddController@listing_required_details');



    // axios calls
    // save add listing
    Route::post('/agents/doc_management/transactions/save_add_listing', 'Agents\DocManagement\Transactions\Listings\ListingAddController@save_add_listing');
    // save required details
    Route::post('/agents/doc_management/transactions/save_listing_required_details', 'Agents\DocManagement\Transactions\Listings\ListingAddController@save_listing_required_details');

    // listing details page
    Route::get('/agents/doc_management/transactions/listings/listing_details/{Listing_ID}', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@listing_details');
    // get header for listing details page
    Route::get('/agents/doc_management/transactions/listings/listing_details_header', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@listing_details_header');
    // get details, members, checklist, etc for listing page
    Route::get('/agents/doc_management/transactions/listings/get_details', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@get_details');
    Route::get('/agents/doc_management/transactions/listings/get_members', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@get_members');
    Route::get('/agents/doc_management/transactions/listings/get_checklist', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@get_checklist');
    Route::get('/agents/doc_management/transactions/listings/get_documents', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@get_documents');
    Route::get('/agents/doc_management/transactions/listings/get_contracts', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@get_contracts');
    // get mls details
    Route::get('/agents/doc_management/transactions/listings/mls_search', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@mls_search');
    Route::get('/agents/doc_management/transactions/listings/save_mls_search', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@save_mls_search');
    // get add contact html
    Route::get('/agents/doc_management/transactions/listings/add_member_html', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@add_member_html');
    // save details
    Route::post('/agents/doc_management/transactions/listings/save_details', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@save_details');
    // save member
    Route::post('/agents/doc_management/transactions/listings/save_member', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@save_member');
    // delete member
    Route::post('/agents/doc_management/transactions/listings/delete_member', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@delete_member');
    // update status in header
    Route::post('/agents/doc_management/transactions/listings/update_status', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@update_status');



    // Add new contract
    Route::get('/agents/doc_management/transactions/add_contract/add_contract', 'Agents\DocManagement\Transactions\ContractAddController@add_contract');

    // TODO this is shared by add listing and add contract
    // Add listing and contract
    Route::get('/agents/doc_management/transactions/get_property_info', 'Agents\DocManagement\Transactions\Listings\ListingAddController@get_property_info');
    Route::get('/agents/doc_management/transactions/update_county_select', 'Agents\DocManagement\Transactions\Listings\ListingAddController@update_county_select');


    // all listings page
    Route::get('/agents/doc_management/transactions/listings/listings_all', 'Agents\DocManagement\Transactions\Listings\ListingDetailsController@listings_all');






    // ** FILL FIELDS
    // List of docs to fill fields
    Route::get('/doc_management/create/fill/fillable_files', 'DocManagement\Fill\FieldsController@fillable_files');
    // fill fields
    Route::get('/doc_management/create/fill_fields/{file_id}', 'DocManagement\Fill\FieldsController@fill_fields');
    /* get common fields for select options */
    Route::get('/doc_management/common_fields', 'DocManagement\Fill\FieldsController@get_common_fields');


