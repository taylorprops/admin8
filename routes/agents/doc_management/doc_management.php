<?php

    // ++++++ Doc Management ++++++ //

    // Global functions
    Route::get('/agents/doc_management/global_functions/get_location_details', 'Agents\DocManagement\Functions\GlobalFunctionsController@get_location_details');


    // all transactions page
    Route::get('/agents/doc_management/transactions', 'Agents\DocManagement\Transactions\TransactionsController@get_transactions');

    // Add new transaction
    Route::get('/agents/doc_management/transactions/add/{type}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@add_transaction');
    // Add listing details if existing
    Route::get('/agents/doc_management/transactions/add/transaction_add_details_existing/{Agent_ID}/{transaction_type}/{state?}/{tax_id?}/{bright_type?}/{bright_id?}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_add_details_existing');
    // Add listing details if new
    Route::get('/agents/doc_management/transactions/add/transaction_add_details_new/{Agent_ID}/{transaction_type}/{street_number?}/{street_name?}/{city?}/{state?}/{zip?}/{county?}/{street_dir?}/{unit_number?}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_add_details_new');
    // Add transaction details if referral
    Route::post('/agents/doc_management/transactions/add/transaction_add_details_referral', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_add_details_referral');
     // Save transaction details if referral
    Route::post('/agents/doc_management/transactions/add/transaction_save_details_referral', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_save_details_referral');
    // Required Details page
    Route::get('/agents/doc_management/transactions/add/transaction_required_details/{id}/{transaction_type}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_required_details');
    // Required Details page referral
    Route::get('/agents/doc_management/transactions/add/transaction_required_details_referral/{Referral_ID}', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@transaction_required_details_referral');



    // axios calls
    // save add listing
    Route::post('/agents/doc_management/transactions/save_add_transaction', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@save_add_transaction');
    // save required details
    Route::post('/agents/doc_management/transactions/save_transaction_required_details', 'Agents\DocManagement\Transactions\Add\TransactionsAddController@save_transaction_required_details');

    // listing details page
    Route::get('/agents/doc_management/transactions/transaction_details/{id}/{transaction_type}', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@transaction_details');
    // get header for listing details page
    Route::get('/agents/doc_management/transactions/transaction_details_header', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@transaction_details_header');

    // get details, members, checklist, etc for listing page
    Route::get('/agents/doc_management/transactions/get_details', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_details');
    Route::get('/agents/doc_management/transactions/get_members', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_members');
    Route::get('/agents/doc_management/transactions/get_checklist', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_checklist');
    // get checklist notes
    Route::get('/doc_management/get_notes', 'DocManagement\Review\DocumentReviewController@get_notes');
    Route::post('/doc_management/delete_note', 'DocManagement\Review\DocumentReviewController@delete_note');
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
    // get documents for add document to checklist html
    Route::get('/agents/doc_management/transactions/get_add_document_to_checklist_documents_html', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_add_document_to_checklist_documents_html');

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
    Route::post('/agents/doc_management/transactions/send_email', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@send_email');
    // merge documents
    Route::post('/agents/doc_management/transactions/merge_documents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@merge_documents');

    // make sure all required fields are filled out before allowing adding documents to the checklist
    Route::post('/agents/doc_management/transactions/check_required_contract_fields', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@check_required_contract_fields');
    // save required fields
    Route::post('/agents/doc_management/transactions/save_required_fields', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_required_fields');


    /////// COMMISSION
    // get commission
    Route::get('/agents/doc_management/transactions/get_commission', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_commission');
    // get check in details from pdf
    Route::post('/agents/doc_management/transactions/get_check_in_details', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_check_in_details');
    // get checks in
    Route::get('/agents/doc_management/transactions/get_checks_in', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_checks_in');
    // get commission notes
    Route::get('/agents/doc_management/transactions/get_commission_notes', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_commission_notes');
    // save add check in
    Route::post('/agents/doc_management/transactions/save_add_check_in', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_add_check_in');
    // delete check in
    Route::post('/agents/doc_management/transactions/save_delete_check_in', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_delete_check_in');




    // get earnest
    Route::get('/agents/doc_management/transactions/get_earnest', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_earnest');



    // Accept new contract for listing
    Route::post('/agents/doc_management/transactions/accept_contract', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@accept_contract');
    // Release contract on listing
    Route::post('/agents/doc_management/transactions/cancel_contract', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@cancel_contract');
    // UNDO release or canceled contract
    Route::post('/agents/doc_management/transactions/undo_cancel_contract', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@undo_cancel_contract');
    // UNDO canceled listing
    Route::post('/agents/doc_management/transactions/undo_cancel_listing', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@undo_cancel_listing');
    // check if docs submitted and accepted
    Route::get('/agents/doc_management/transactions/check_docs_submitted_and_accepted', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@check_docs_submitted_and_accepted');
    // cancel listing
    Route::post('/agents/doc_management/transactions/cancel_listing', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@cancel_listing');
    // update contract status
    Route::post('/agents/doc_management/transactions/update_contract_status', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@update_contract_status');

    // search bright mls agents
    Route::get('/agents/doc_management/transactions/search_bright_agents', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@search_bright_agents');


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


///////////////////////////////// ADMIN ONLY //////////////////////////////////////////////
/**********  File review /**********/

Route::middleware('admin') -> group(function () {

    // accept reject checklist items
    Route::post('/agents/doc_management/transactions/set_checklist_item_review_status', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@set_checklist_item_review_status');
    // mark checklist items required or if applicable
    Route::post('/agents/doc_management/transactions/mark_required', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@mark_required');
    // save add checklist item
    Route::post('/agents/doc_management/transactions/save_add_checklist_item', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@save_add_checklist_item');
    // remove checklist item
    Route::post('/agents/doc_management/transactions/remove_checklist_item', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@remove_checklist_item');
    // get email checklist html
    Route::get('/agents/doc_management/transactions/get_email_checklist_html', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_email_checklist_html');




});



