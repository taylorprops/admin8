import SimpleBar from 'simplebar';
import $ from 'jquery';
window.$ = window.jQuery = $;
const axios = require('axios');
//const datepicker = require('js-datepicker'); // in fill_fields.js




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
require('./agents/doc_management/transactions/listings/listing_add.js');
require('./agents/doc_management/transactions/listings/listing_details.js');
require('./agents/doc_management/transactions/listings/listings_all.js');


require('./agents/doc_management/transactions/contracts/contract_add_details.js');
require('./agents/doc_management/transactions/contracts/contract_add.js');
require('./agents/doc_management/transactions/contracts/contract_details.js');
require('./agents/doc_management/transactions/contracts/contracts_all.js');


