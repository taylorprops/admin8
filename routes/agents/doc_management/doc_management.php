<?php

    // ++++++ Doc Management ++++++ //

    // Global functions
    Route::get('/agents/doc_management/global_functions/get_location_details', 'Agents\DocManagement\Functions\GlobalFunctionsController@get_location_details');


    // all transactions page
    Route::get('/agents/doc_management/transactions', 'Agents\DocManagement\Transactions\TransactionsController@get_transactions');



    // Add new transaction
    Route::get('/agents/doc_management/transactions/add/{type}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@add_transaction');
    // Add listing details if existing
    Route::get('/agents/doc_management/transactions/add/transaction_add_details_existing/{transaction_type}/{state?}/{tax_id?}/{bright_type?}/{bright_id?}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@add_transaction_details_existing');
    // Add listing details if new
    Route::get('/agents/doc_management/transactions/add/transaction_add_details_new/{transaction_type}/{street_number?}/{street_name?}/{city?}/{state?}/{zip?}/{county?}/{street_dir?}/{unit_number?}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@add_transaction_details_new');
    // Required Details page
    Route::get('/agents/doc_management/transactions/add/transaction_required_details/{id}/{transaction_type}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_required_details');



    // axios calls
    // save add listing
    Route::post('/agents/doc_management/transactions/save_add_transaction', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@save_add_transaction');
    // save required details
    Route::post('/agents/doc_management/transactions/save_transaction_required_details', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@save_transaction_required_details');

    // listing details page
    Route::get('/agents/doc_management/transactions/transaction_details/{id}/{type}', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@transaction_details');
    // get header for listing details page
    Route::get('/agents/doc_management/transactions/transaction_details_header', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@transaction_details_header');
    // get details, members, checklist, etc for listing page
    Route::get('/agents/doc_management/transactions/get_details', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_details');
    Route::get('/agents/doc_management/transactions/get_members', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_members');
    Route::get('/agents/doc_management/transactions/get_checklist', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_checklist');
    Route::get('/agents/doc_management/transactions/get_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_documents');
    Route::get('/agents/doc_management/transactions/get_contracts', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_contracts');
    // get mls details
    Route::get('/agents/doc_management/transactions/mls_search', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@mls_search');
    Route::get('/agents/doc_management/transactions/save_mls_search', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_mls_search');
    // get add contact html
    Route::get('/agents/doc_management/transactions/add_member_html', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@add_member_html');
    // save details
    Route::post('/agents/doc_management/transactions/save_details', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_details');
    // save member
    Route::post('/agents/doc_management/transactions/save_member', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_member');
    // delete member
    Route::post('/agents/doc_management/transactions/delete_member', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@delete_member');
    // add documents folder
    Route::post('/agents/doc_management/transactions/add_folder', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@add_folder');
    // delete documents folder
    Route::post('/agents/doc_management/transactions/delete_folder', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@delete_folder');
    // upload documents
    Route::post('/agents/doc_management/transactions/upload_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@upload_documents');
    // save add template documents
    Route::post('/agents/doc_management/transactions/save_add_template_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_add_template_documents');
    // move documents to trash
    Route::post('/agents/doc_management/transactions/move_documents_to_trash', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@move_documents_to_trash');
    // move documents to different folder
    Route::post('/agents/doc_management/transactions/move_documents_to_folder', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@move_documents_to_folder');
    // reorder documents
    Route::post('/agents/doc_management/transactions/reorder_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@reorder_documents');
    // get add document to checklist html
    Route::get('/agents/doc_management/transactions/add_document_to_checklist_item_html', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@add_document_to_checklist_item_html');

    // delete document from checklist item
    Route::post('/agents/doc_management/transactions/remove_document_from_checklist_item', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@remove_document_from_checklist_item');
    // add notes checklist item
    Route::post('/agents/doc_management/transactions/add_notes_to_checklist_item', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@add_notes_to_checklist_item');
    // mark note read
    Route::post('/agents/doc_management/transactions/mark_note_read', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@mark_note_read');

    // add one document to checklist item from checklist
    Route::post('/agents/doc_management/transactions/add_document_to_checklist_item', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@add_document_to_checklist_item');
    // save assign items to checklist from documents
    Route::post('/agents/doc_management/transactions/save_assign_documents_to_checklist', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_assign_documents_to_checklist');
    // change checklist
    Route::post('/agents/doc_management/transactions/change_checklist', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@change_checklist');
    // save rename document
    Route::post('/agents/doc_management/transactions/save_rename_document', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_rename_document');
    // get split document html
    Route::get('/agents/doc_management/transactions/get_split_document_html', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_split_document_html');
    // save add split document to documents
    Route::post('/agents/doc_management/transactions/save_split_document', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_split_document');
    // duplicate document
    Route::post('/agents/doc_management/transactions/duplicate_document', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@duplicate_document');
    // get email documents
    Route::post('/agents/doc_management/transactions/email_get_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@email_get_documents');
    // email documents
    Route::post('/agents/doc_management/transactions/email_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@email_documents');
    // merge documents
    Route::post('/agents/doc_management/transactions/merge_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@merge_documents');




    // Accept new contract for listing
    Route::post('/agents/doc_management/transactions/accept_contract', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@accept_contract');

    // TODO this is shared by add listing and add contract
    // Add listing and contract
    Route::get('/agents/doc_management/transactions/get_property_info', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@get_property_info');
    Route::get('/agents/doc_management/transactions/update_county_select', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@update_county_select');









    // ** FILL FIELDS

    // fill fields
    Route::get('/agents/doc_management/transactions/edit_files/{document_id}/{saved?}', 'Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController@file_view');

    // rotate document
    Route::post('/agents/doc_management/transactions/edit_files/rotate_document', 'Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController@rotate_document');

    Route::post('/agents/doc_management/transactions/edit_files/save_field_input_values', 'Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController@save_field_input_values');
    // Export filled fields to pdf
    Route::post('/agents/doc_management/transactions/edit_files/convert_to_pdf', 'Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController@convert_to_pdf') -> name('convert_to_pdf');
    // save editing form - user added text, highlighting, etc
    Route::post('/agents/doc_management/transactions/edit_files/save_edit_options', 'Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController@save_edit_options');
    // get user fields
    Route::post('/agents/doc_management/transactions/edit_files/get_user_fields', 'Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController@get_user_fields');







