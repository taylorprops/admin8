import SimpleBar from 'simplebar';
import $ from 'jquery';
window.$ = window.jQuery = $;
const axios = require('axios');
import 'jquery-ui/ui/widgets/datepicker.js';
require('dm-file-uploader');




require('./bootstrap');

require('./global.js');
require('./form_elements.js');
require('./nav/nav.js');

// Document Management
require('./doc_management/create/add_fields.js');
require('./doc_management/create/files.js');
require('./doc_management/resources/resources.js');
require('./doc_management/fill/fill_fields.js');
require('./doc_management/checklists/checklists.js');

// Agents
require('./agents/doc_management/transactions/listings/listing_add_details.js');
require('./agents/doc_management/transactions/listings/listing_required_details.js');
require('./agents/doc_management/transactions/listings/listing_add.js');
require('./agents/doc_management/transactions/listings/listing_details.js');
require('./agents/doc_management/transactions/listings/listings_all.js');
// details tabs
require('./agents/doc_management/transactions/listings/details_tabs/checklist.js');
require('./agents/doc_management/transactions/listings/details_tabs/commission.js');
require('./agents/doc_management/transactions/listings/details_tabs/contracts.js');
require('./agents/doc_management/transactions/listings/details_tabs/details.js');
require('./agents/doc_management/transactions/listings/details_tabs/documents.js');
require('./agents/doc_management/transactions/listings/details_tabs/members.js');
require('./agents/doc_management/transactions/upload/upload.js');

// edit files
require('./agents/doc_management/transactions/edit_files/edit_files.js');


require('./agents/doc_management/transactions/contracts/contract_add_details.js');
require('./agents/doc_management/transactions/contracts/contract_add.js');
require('./agents/doc_management/transactions/contracts/contract_details.js');
require('./agents/doc_management/transactions/contracts/contracts_all.js');


