<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Add;

use Config;
use Illuminate\Http\Request;

use App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Data\ListingsData;
use App\Models\DocManagement\Transactions\Data\ListingsRemovedData;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Members\Members;
// use App\Models\DocManagement\Checklists\Checklists;
// use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\Employees\Agents;
use App\Models\Resources\LocationData;
use App\Models\CRM\CRMContacts;
use App\Models\BrightMLS\Offices;


class TransactionsAddController extends Controller {

    public function add_transaction(Request $request) {
        $transaction_type = 'Contract/Lease';
        if($request -> type == 'listing') {
            $transaction_type = 'listing';
        }
        $states = LocationData::ActiveStates();
        return view('/agents/doc_management/transactions/add/transaction_add', compact('transaction_type', 'states'));
    }

    public function add_transaction_details_existing(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $bright_type = $request -> bright_type;
        $bright_id = $request -> bright_id;
        $state = $request -> state;
        $tax_id = '';
        $mls_verified = 'no';
        $bright_db_search = '';

        // only pulling tax records from MD
        if ($state == 'MD') {
            $tax_id = $request -> tax_id;
        }

        $select_columns_bright = config('global.vars.select_columns_bright');
        $select_columns_db = explode(',', $select_columns_bright);
        $select_columns_db_closed = 'AssociationFee,AssociationYN,AttachedGarageYN,BasementFinishedPercent,BasementYN,BathroomsTotalInteger,BedroomsTotal,City,CondoYN,County,FireplaceYN,FullStreetAddress,GarageYN,Heating,Latitude,ListingTaxID,ListPictureURL,Longitude,LotSizeAcres,LotSizeSquareFeet,NewConstructionYN,NumAttachedGarageSpaces,NumDetachedGarageSpaces,Pool,PostalCode,PropertySubType,PropertyType,StateOrProvince,StreetDirPrefix,StreetDirSuffix,StreetName,StreetNumber,StreetSuffix,StreetSuffixModifier,StructureDesignType,SubdivisionName,UnitBuildingType,UnitNumber,YearBuilt';
        $select_columns_db_closed = explode(',', $select_columns_db_closed);

        if ($bright_type == 'db_active') {

            $bright_db_search = ListingsData::select($select_columns_db) -> where('ListingId', $bright_id) -> first() -> toArray();

            $mls_verified = 'yes';

        } elseif ($bright_type == 'db_closed') {

            $bright_db_search = ListingsRemovedData::select($select_columns_db_closed) -> where('ListingId', $bright_id) -> first() -> toArray();

        } elseif ($bright_type == 'bright') {

            $rets = new \PHRETS\Session(Config::get('rets.rets.rets_config'));
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';
            $query = '(ListingId=' . $bright_id . ')';

            $bright_db_search = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Select' => $select_columns_bright,
                ]
            );

            $bright_db_search = $bright_db_search[0]-> toArray();

            // remove all fields that do not apply to current listing
            if ($bright_db_search['MlsStatus'] == 'CLOSED' && $bright_db_search['CloseDate'] < date("Y-m-d", strtotime("-3 month"))) {
                $bright_db_search['MlsStatus'] = '';
                $bright_db_search['CloseDate'] = '';
                $bright_db_search['ListingId'] = '';
                $bright_db_search['ListPrice'] = '';
                $bright_db_search['ListOfficeName'] = '';
                $bright_db_search['PropertyType'] = '';
                $bright_db_search['PropertySubType'] = '';
                $bright_db_search['MLSListDate'] = '';
                $bright_db_search['ListAgentFirstName'] = '';
                $bright_db_search['ListAgentLastName'] = '';
                $bright_db_search['PublicRemarks'] = '';
                $bright_db_search['NewConstructionYN'] = '';
                $bright_db_search['ListOfficeMlsId'] = '';
                $bright_db_search['ListAgentPreferredPhone'] = '';
                $bright_db_search['ListAgentMlsId'] = '';
                $bright_db_search['ListAgentEmail'] = '';
                $bright_db_search['BuyerAgentEmail'] = '';
                $bright_db_search['BuyerAgentFirstName'] = '';
                $bright_db_search['BuyerAgentLastName'] = '';
                $bright_db_search['BuyerAgentMlsId'] = '';
                $bright_db_search['BuyerAgentPreferredPhone'] = '';
                $bright_db_search['BuyerOfficeMlsId'] = '';
                $bright_db_search['BuyerOfficeName'] = '';
            }

            $rets -> disconnect();

            $mls_verified = 'yes';

        }

        $tax_record_search = null;
        if ($tax_id != '') {
            $functions = new GlobalFunctionsController();
            $tax_record_search = $functions -> tax_records('', '', '', '', $tax_id, $state);
        }

        $property_details = array();

        // if only brightmls results
        if ($bright_db_search && !$tax_record_search) {

            $property_details = $bright_db_search;

            // if only tax record results
        } else if (!$bright_db_search && $tax_record_search) {

            $property_details = $tax_record_search;

        } else if ($bright_db_search && $tax_record_search) {

            // keep bright results, replace a few and add rest from tax records
            $property_details = array_merge($tax_record_search, $bright_db_search);

        }

        $property_details['MLS_Verified'] = $mls_verified;
        $property_details['transaction_type'] = $transaction_type;

        $property_details = (object)$property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('/agents/doc_management/transactions/add/transaction_add_details', compact('property_details', 'property_types', 'property_sub_types'));
    }

    public function add_transaction_details_new(Request $request) {

        $property_details = [
            'FullStreetAddress' => $request -> street_number . ' ' . $request -> street_name . ' ' . $request -> street_dir . ' ' . $request -> unit_number,
            'StreetNumber' => $request -> street_number,
            'StreetName' => $request -> street_name,
            'StreetDirPrefix' => $request -> street_dir,
            'UnitNumber' => $request -> unit_number,
            'City' => $request -> city,
            'StateOrProvince' => $request -> state,
            'PostalCode' => $request -> zip,
            'County' => $request -> county,
        ];

        $transaction_type = strtolower($request -> transaction_type);
        $property_details['transaction_type'] = $transaction_type;
        $property_details = (object)$property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('/agents/doc_management/transactions/add/transaction_add_details', compact('property_details', 'property_types', 'property_sub_types'));
    }

    public function transaction_required_details(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);

        $property_details = Contracts::where('Contract_ID', $request -> id) -> first();
        if($transaction_type == 'listing') {
            $property_details = Listings::where('Listing_ID', $request -> id) -> first();
        }
        $states = LocationData::AllStates();
        $states_json = $states -> toJson();
        $statuses = ResourceItems::where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();
        $contacts = CRMContacts::where('Agent_ID', $property_details -> Agent_ID) -> get();
        $resource_items = new ResourceItems();

        return view('/agents/doc_management/transactions/add/transaction_required_details_'.$transaction_type, compact('property_details', 'states', 'states_json', 'statuses', 'contacts', 'resource_items', 'transaction_type'));
    }

    public function save_add_transaction(Request $request) {

        $property_details = (object)session('property_details');
        $transaction_type = strtolower($property_details -> transaction_type);
        unset($property_details -> transaction_type);
        $resource_items = new ResourceItems();

        // TODO add more agent fields

        // add user id if logged in, otherwise it will be added from admin
        if ($request -> Agent_ID) {
            $agent = Agents::AgentDetails($request -> Agent_ID);

        } else {
            $agent = \Session::get('agent_details');
        }
        // add agent details
        $property_details -> Agent_ID = $agent -> id;

        /* if($transaction_type == 'listing') {
            $property_details -> ListAgentFirstName = $agent -> first_name;
            $property_details -> ListAgentLastName = $agent -> last_name;
            $property_details -> ListAgentEmail = $agent -> email;
            $property_details -> ListAgentPreferredPhone = $agent -> cell_phone;
        } else {
            $property_details -> BuyerAgentFirstName = $agent -> first_name;
            $property_details -> BuyerAgentLastName = $agent -> last_name;
            $property_details -> BuyerAgentEmail = $agent -> email;
            $property_details -> BuyerAgentPreferredPhone = $agent -> cell_phone;
            $property_details -> ContractPrice = preg_replace('/[\$,]+/', '', $request -> contract_price) ?? null;
        } */

        if($transaction_type == 'contract') {
            $property_details -> ContractPrice = preg_replace('/[\$,]+/', '', $request -> contract_price) ?? null;
        }

        // replace current values from property details with new data
        $property_details -> SaleRent = $request -> listing_type;
        $property_details -> PropertyType = $resource_items -> GetResourceID($request -> property_type, 'checklist_property_types'); // convert to integer
        $property_details -> PropertySubType = $resource_items -> GetResourceID($request -> property_sub_type, 'checklist_property_sub_types'); // convert to integer
        $property_details -> YearBuilt = $request -> year_built ?? null;
        $property_details -> ListPrice = preg_replace('/[\$,]+/', '', $request -> list_price) ?? null;
        $property_details -> HoaCondoFees = $request -> hoa_condo ?? null;
        if($property_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($property_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($property_details -> StateOrProvince, 'checklist_locations');
        }
        $property_details -> Location_ID = $location_id;


        $new_transaction = new Contracts;
        if($transaction_type == 'listing') {
            $new_transaction = new Listings;
        }

        foreach ($property_details as $key => $val) {
            $new_transaction -> $key = $val ?? null;
        }

        $new_transaction -> save();

        // add default docs folders
        $Listing_ID = '';
        $Contract_ID = '';
        if($transaction_type == 'listing') {
            $Listing_ID = $new_transaction -> Listing_ID;
            $new_folder = new TransactionDocumentsFolders();
            $new_folder -> Listing_ID = $Listing_ID;
            $new_folder -> Contract_ID = $Contract_ID;
            $new_folder -> Agent_ID = $agent -> id;
            $new_folder -> folder_name = 'Listing Documents';
            $new_folder -> order = 0;
            $new_folder -> save();
        } else {
            $Contract_ID = $new_transaction -> Contract_ID;
        }


        $new_folder = new TransactionDocumentsFolders();
        $new_folder -> Listing_ID = $Listing_ID;
        $new_folder -> Contract_ID = $Contract_ID;
        $new_folder -> Agent_ID = $agent -> id;
        $new_folder -> folder_name = 'Contract Documents';
        $new_folder -> order = 0;
        $new_folder -> save();

        $new_folder = new TransactionDocumentsFolders();
        $new_folder -> Listing_ID = $Listing_ID;
        $new_folder -> Contract_ID = $Contract_ID;
        $new_folder -> Agent_ID = $agent -> id;
        $new_folder -> folder_name = 'Trash';
        $new_folder -> order = 100;
        $new_folder -> save();

        return response() -> json(['id' => $Listing_ID.$Contract_ID]);

    }

    public function save_transaction_required_details(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? null;
        $Contract_ID = $request -> Contract_ID ?? null;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = strtolower($request -> transaction_type);

        $field = 'Listing_ID';
        $id = $Listing_ID;
        if($transaction_type == 'contract') {
            $field = 'Contract_ID';
            $id = $Contract_ID;
            $property = Contracts::find($Contract_ID);
        } else {
            $property = Listings::find($Listing_ID);
        }

        $agent = Agents::where('id', $Agent_ID) -> first();
        // get state to see which bright_mls_id is required
        $state = $property -> StateOrProvince;

        $bright_mls_id = $agent -> bright_mls_id_md_dc_tp;
        if($state == 'MD' && $agent -> company == 'Anne Arundel Properties'){
            $bright_mls_id = $agent -> bright_mls_id_md_aap;
        } else if($state == 'VA') {
            $bright_mls_id = $agent -> bright_mls_id_va_tp;
        }

        // get property details to add listing agent to members if contract
        if($transaction_type == 'contract') {

            $listing_agent = new Members();
            $listing_agent -> first_name = $property -> ListAgentFirstName;
            $listing_agent -> last_name = $property -> ListAgentLastName;
            $listing_agent -> cell_phone = $property -> ListAgentPreferredPhone;
            $listing_agent -> email = $property -> ListAgentEmail;
            $listing_agent -> company = $property -> ListOfficeName;

            $list_office_mls_id = $property -> ListOfficeMlsId;
            $list_office_details = Offices::where('OfficeMlsId', $list_office_mls_id) -> first();

            $listing_agent -> bright_mls_id = $property -> ListAgentMlsId;
            $listing_agent -> address_office_street = $list_office_details -> OfficeAddress1;
            $listing_agent -> address_office_city = $list_office_details -> OfficeCity;
            $listing_agent -> address_office_state = $list_office_details -> OfficeStateOrProvince;
            $listing_agent -> address_office_zip = $list_office_details -> OfficePostalCode;
            $listing_agent -> Contract_ID = $Contract_ID;
            $listing_agent -> member_type_id = ResourceItems::ListingAgentResourceId();
            $listing_agent -> save();

            $buyers_agent = new Members();
            $buyers_agent -> first_name = $agent -> first_name;
            $buyers_agent -> last_name = $agent -> last_name;
            $buyers_agent -> cell_phone = $agent -> cell_phone;
            $buyers_agent -> email = $agent -> email;
            $buyers_agent -> company = $agent -> company;
            $buyers_agent -> bright_mls_id = $bright_mls_id;
            $buyers_agent -> address_office_street = config('global.vars.company_street');
            $buyers_agent -> address_office_city = config('global.vars.company_city');
            $buyers_agent -> address_office_state = config('global.vars.company_state');
            $buyers_agent -> address_office_zip = config('global.vars.company_zip');
            $buyers_agent -> Contract_ID = $Contract_ID;
            $buyers_agent -> Agent_ID = $Agent_ID;
            $buyers_agent -> member_type_id = ResourceItems::BuyerAgentResourceId();
            $buyers_agent -> disabled = true;
            $buyers_agent -> save();

        } else {
            // add listing agent to members if just a listing
            $listing_agent = new Members();
            $listing_agent -> first_name = $agent -> first_name;
            $listing_agent -> last_name = $agent -> last_name;
            $listing_agent -> cell_phone = $agent -> cell_phone;
            $listing_agent -> email = $agent -> email;
            $listing_agent -> company = $agent -> company;
            $listing_agent -> bright_mls_id = $bright_mls_id;
            $listing_agent -> address_office_street = config('global.vars.company_street');
            $listing_agent -> address_office_city = config('global.vars.company_city');
            $listing_agent -> address_office_state = config('global.vars.company_state');
            $listing_agent -> address_office_zip = config('global.vars.company_zip');
            $listing_agent -> Listing_ID = $Listing_ID;
            $listing_agent -> Agent_ID = $Agent_ID;
            $listing_agent -> member_type_id = ResourceItems::ListingAgentResourceId();
            $listing_agent -> disabled = true;
            $listing_agent -> save();

            $property -> ListAgentFirstName = $agent -> first_name;
            $property -> ListAgentLastName = $agent -> last_name;
            $property -> ListAgentEmail = $agent -> email;
            $property -> ListAgentPreferredPhone = $agent -> cell_phone;
            $property -> ListAgentMlsId = $bright_mls_id;
            $property -> ListOfficeName = $agent -> company;

            $office_bright_mls_id = 'AAP1';
            if($property -> StateOrProvince == 'VA') {
                $office_bright_mls_id = 'TAYL13';
            } else {
                if(stristr($agent -> company, 'taylor')) {
                    $office_bright_mls_id = 'TAYL1';
                }
            }
            $property -> ListOfficeMlsId = $office_bright_mls_id;

        }


        // add sellers to doc_members
        $seller_first = $request -> seller_first_name;
        $seller_last = $request -> seller_last_name;
        $seller_phone = $request -> seller_phone;
        $seller_email = $request -> seller_email;
        $seller_entity_name = $request -> seller_entity_name;
        $seller_address_street = $request -> seller_street;
        $seller_address_city = $request -> seller_city;
        $seller_address_state = $request -> seller_state;
        $seller_address_zip = $request -> seller_zip;
        $seller_crm_contact_id = $request -> seller_crm_contact_id;

        for ($i = 0; $i < count($seller_first); $i++) {

            $sellers = null;
            if($seller_email[$i] != '') {
                $sellers = Members::where('email', $seller_email[$i]) -> where($field, $id) -> where('Agent_ID', $Agent_ID) -> first();
            }
            if(!$sellers) {
                $sellers = new Members();
            }
            if($i == 0) {
                $sellers -> entity_name = $seller_entity_name;
            }
            $sellers -> first_name = $seller_first[$i];
            $sellers -> last_name = $seller_last[$i];
            $sellers -> cell_phone = $seller_phone[$i] ?? null;
            $sellers -> email = $seller_email[$i] ?? null;
            $sellers -> address_home_street = $seller_address_street[$i] ?? null;
            $sellers -> address_home_city = $seller_address_city[$i] ?? null;
            $sellers -> address_home_state = $seller_address_state[$i] ?? null;
            $sellers -> address_home_zip = $seller_address_zip[$i] ?? null;
            $sellers -> CRMContact_ID = $seller_crm_contact_id[$i] ?? 0;
            $sellers -> member_type_id = ResourceItems::SellerResourceId();
            $sellers -> Agent_ID = $Agent_ID;
            $sellers -> Listing_ID = $Listing_ID;
            $sellers -> Contract_ID = $Contract_ID;
            $sellers -> save();


            if ($i == 0) {
                $seller_one_first = $seller_first[$i];
                $seller_one_last = $seller_last[$i];
                $seller_two_first = null;
                $seller_two_last = null;
            } else if ($i == 1) {
                $seller_two_first = $seller_first[$i];
                $seller_two_last = $seller_last[$i];
            }

        }

        // add buyers to doc_members
        if($request -> buyer_first_name) {
            $buyer_entity_name = $request -> buyer_entity_name;
            $buyer_first = $request -> buyer_first_name;
            $buyer_last = $request -> buyer_last_name;
            $buyer_phone = $request -> buyer_phone;
            $buyer_email = $request -> buyer_email;
            $buyer_address_street = $request -> buyer_street;
            $buyer_address_city = $request -> buyer_city;
            $buyer_address_state = $request -> buyer_state;
            $buyer_address_zip = $request -> buyer_zip;
            $buyer_crm_contact_id = $request -> buyer_crm_contact_id;

            for ($i = 0; $i < count($buyer_first); $i++) {
                $buyers = null;
                if($buyer_email[$i] != '') {
                    $buyers = Members::where('email', $buyer_email[$i]) -> where($field, $id) -> where('Agent_ID', $Agent_ID) -> first();
                }
                if(!$buyers) {
                    $buyers = new Members();
                }
                if($i == 0) {
                    $buyers -> entity_name = $buyer_entity_name;
                }
                $buyers -> first_name = $buyer_first[$i];
                $buyers -> last_name = $buyer_last[$i];
                $buyers -> cell_phone = $buyer_phone[$i] ?? null;
                $buyers -> email = $buyer_email[$i] ?? null;
                $buyers -> address_home_street = $buyer_address_street[$i] ?? null;
                $buyers -> address_home_city = $buyer_address_city[$i] ?? null;
                $buyers -> address_home_state = $buyer_address_state[$i] ?? null;
                $buyers -> address_home_zip = $buyer_address_zip[$i] ?? null;
                $buyers -> CRMContact_ID = $buyer_crm_contact_id[$i] ?? 0;
                $buyers -> member_type_id = ResourceItems::BuyerResourceId();
                $buyers -> Agent_ID = $Agent_ID;
                $buyers -> Listing_ID = $Listing_ID;
                $buyers -> Contract_ID = $Contract_ID;
                $buyers -> save();


                if ($i == 0) {
                    $buyer_one_first = $buyer_first[$i];
                    $buyer_one_last = $buyer_last[$i];
                    $buyer_two_first = null;
                    $buyer_two_last = null;
                } else if ($i == 1) {
                    $buyer_two_first = $buyer_first[$i];
                    $buyer_two_last = $buyer_last[$i];
                }

            }
        }

        if($transaction_type == 'listing') {

            // update listing
            $property -> SellerOneFirstName = $seller_one_first;
            $property -> SellerOneLastName = $seller_one_last;
            $property -> SellerTwoFirstName = $seller_two_first;
            $property -> SellerTwoLastName = $seller_two_last;
            $property -> MLSListDate = $request -> MLSListDate;
            $property -> ExpirationDate = $request -> ExpirationDate;
            if(date('Y-m-d') < $request -> MLSListDate) {
                $property -> Status = ResourceItems::GetResourceID('Pre-Listing', 'listing_status');
            } else if(date('Y-m-d') > $request -> MLSListDate && date('Y-m-d') < $request -> ExpirationDate) {
                $property -> Status = ResourceItems::GetResourceID('Active', 'listing_status');
            } else {
                $property -> Status = ResourceItems::GetResourceID('Expired', 'listing_status');
            }
            $property -> save();

            $checklist_property_type_id = $property -> PropertyType;
            $checklist_property_sub_type_id = $property -> PropertySubType;
            $checklist_sale_rent = $property -> SaleRent;
            $checklist_state = $property -> StateOrProvince;
            $checklist_location_id = $property -> Location_ID;
            $checklist_hoa_condo = $property -> HoaCondoFees;
            $checklist_year_built = $property -> YearBuilt;

        } else if($transaction_type == 'contract') {

            // update contract
            $property -> SellerOneFirstName = $seller_one_first;
            $property -> SellerOneLastName = $seller_one_last;
            $property -> SellerTwoFirstName = $seller_two_first;
            $property -> SellerTwoLastName = $seller_two_last;
            $property -> BuyerOneFirstName = $buyer_one_first;
            $property -> BuyerOneLastName = $buyer_one_last;
            $property -> BuyerTwoFirstName = $buyer_two_first;
            $property -> BuyerTwoLastName = $buyer_two_last;
            $property -> ContractDate = $request -> ContractDate;
            $property -> CloseDate = $request -> CloseDate;
            $property -> Status = ResourceItems::GetResourceID('Active', 'contract_status');
            $property -> save();

            $checklist_property_type_id = $property -> PropertyType;
            $checklist_property_sub_type_id = $property -> PropertySubType;
            $checklist_sale_rent = $property -> SaleRent;
            $checklist_state = $property -> StateOrProvince;
            $checklist_location_id = $property -> Location_ID;
            $checklist_hoa_condo = $property -> HoaCondoFees;
            $checklist_year_built = $property -> YearBuilt;

        }

        // add checklist and checklist items
        $checklist_represent = ($Listing_ID > 0 ? 'seller' : 'buyer');
        $checklist_type = $transaction_type;

        $checklist_id = '';

        TransactionChecklists::CreateTransactionChecklist($checklist_id, $Listing_ID, $Contract_ID, $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        if($transaction_type == 'listing') {
            return response() -> json([
                'type' => 'listing',
                'id' => $Listing_ID
            ]);
        }
        return response() -> json([
            'type' => 'contract',
            'id' => $Contract_ID
        ]);



    }

    public function get_property_info(Request $request) {

        $street_number = $street_name = $unit = $zip = $tax_id = $state = '';

        if ($request -> mls) {

            $ListingId = $request -> mls;
            $state = substr($request -> mls, 0, 2);

        } else {

            $street_number = $request -> street_number;
            $street_name = $request -> street_name;
            $street_suffix = '';
            $street_dir_suffix = '';
            $street_dir_suffix_alt = '';

            // remove all suffixes and dir suffixes to get just street name. Save them for later
            $street_suffixes_array = config('global.vars.street_suffixes');

            $street_dir_suffixes_array = config('global.vars.street_dir_suffixes');

            $street_dir_suffixes_alt_array = array(
                array('orig' => 'E', 'alt' => 'EAST'),
                array('orig' => 'EAST', 'alt' => 'E'),
                array('orig' => 'W', 'alt' => 'WEST'),
                array('orig' => 'WEST', 'alt' => 'W'),
                array('orig' => 'S', 'alt' => 'SOUTH'),
                array('orig' => 'SOUTH', 'alt' => 'S'),
                array('orig' => 'N', 'alt' => 'NORTH'),
                array('orig' => 'NORTH', 'alt' => 'N'),
                array('orig' => 'NE', 'alt' => 'NORTHEAST'),
                array('orig' => 'NORTHEAST', 'alt' => 'NE'),
                array('orig' => 'NW', 'alt' => 'NORTHWEST'),
                array('orig' => 'NORTHWEST', 'alt' => 'NW'),
                array('orig' => 'SE', 'alt' => 'SOUTHEAST'),
                array('orig' => 'SOUTHEAST', 'alt' => 'SE'),
                array('orig' => 'SW', 'alt' => 'SOUTHWEST'),
                array('orig' => 'SOUTHWEST', 'alt' => 'SW'),
            );

            foreach ($street_suffixes_array as $street_suffixes) {

                if (preg_match('/\s\b(' . $street_suffixes . '(?!.*' . $street_suffixes . '))\b/i', $street_name, $matches)) {
                    $street_name = preg_replace('/\\s\b(' . $street_suffixes . '(?!.*' . $street_suffixes . '))\b/i', '', $street_name);
                    $street_suffix = trim($matches[0]);
                }

            }

            foreach ($street_dir_suffixes_array as $street_dir_suffixes) {

                // do not pull prefixes i.e. 234 SW some st
                if (!preg_match('/[0-9]+\s(' . $street_dir_suffixes . ')/i', $street_name)) {

                    // only pull suffixes i.e. 234 Main St. Southwest
                    if (preg_match('/\s\b(' . $street_dir_suffixes . ')\b/i', $street_name, $matches)) {
                        $street_name = preg_replace('/\s\b(' . $street_dir_suffixes . ')\b/i', '', $street_name);
                        $street_dir_suffix = trim($matches[0]);

                        foreach ($street_dir_suffixes_alt_array as $dir) {
                            if (strtolower($dir['orig']) == strtolower($street_dir_suffix)) {
                                $street_dir_suffix_alt = $dir['alt'];
                            }

                        }

                    }

                }

            }

            $street_dir_suffix_bright_dmql = '';
            if ($street_dir_suffix != '') {
                $street_dir_suffix_bright_dmql = ',(((StreetDirSuffix=|' . $street_dir_suffix . ')|(StreetDirSuffix=|' . $street_dir_suffix_alt . '))|((StreetDirPrefix=|' . $street_dir_suffix . ')|(StreetDirPrefix=|' . $street_dir_suffix_alt . ')))';
            }

            $unit = $request -> unit;
            $unit_bright_dmql = '';
            if ($unit != '') {
                $unit_bright_dmql = ',(UnitNumber=*' . $unit . '*)';
            }

            $city = $request -> city;
            $state = $request -> state;
            $zip = $request -> zip;
            $county = $request -> county;

        }

        $select_columns_db = array('ListPictureURL', 'FullStreetAddress', 'City', 'StateOrProvince', 'County', 'PostalCode', 'YearBuilt', 'BathroomsTotalInteger', 'BedroomsTotal', 'MlsStatus', 'ListingId', 'ListPrice', 'PropertyType', 'ListOfficeName', 'MLSListDate', 'ListAgentFirstName', 'ListAgentLastName', 'UnitNumber', 'CloseDate', 'ListingTaxID');
        $select_columns_bright = 'ListPictureURL, FullStreetAddress, City, StateOrProvince, County, PostalCode, YearBuilt, BathroomsTotalInteger, BedroomsTotal, MlsStatus, ListingId, ListPrice, PropertyType, ListOfficeName, MLSListDate, ListAgentFirstName, ListAgentLastName, UnitNumber, CloseDate, ListingTaxID';

        $property_details = null;
        $results = [];
        $results['multiple'] = false;

        ///// DATABASE SEARCH FOR PROPERTY /////
        if ($request -> mls) {

            $bright_db_search = ListingsData::select($select_columns_db) -> where('ListingId', $ListingId) -> get() -> toArray();

        } else {

            $bright_db_search = ListingsData::select($select_columns_db) -> ListingSearch($state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt);

        }

        if (count($bright_db_search) > 0) {
            $results['results_bright_type'] = 'db_active';
            if (count($bright_db_search) > 1) {

            // see if results have different unit numbers
                if ($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
                    $property_details['multiple'] = true;
                    return $property_details;
                    die();
                } else {
                    $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                }

            } else {
                $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
            }

        }

        ///// END DATABASE SEARCH FOR PROPERTY /////

        ///// BRIGHT SEARCH /////

        // If not found in database search search bright mls
        if (count($bright_db_search) == 0) {

            $rets = new \PHRETS\Session(Config::get('rets.rets.rets_config'));
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';

            // get property results from brightmls
            if ($request -> mls) {
                $query = '(ListingId=' . $ListingId . ')';
            } else {
                $query = '(StateOrProvince=|' . $state . '),(PostalCode=' . $zip . '),(StreetNumber=' . $street_number . '),(StreetName=' . $street_name . '*)' . $unit_bright_dmql . $street_dir_suffix_bright_dmql;
            }

            $bright_db_search = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Select' => $select_columns_bright,
                ]
            );

            $bright_db_search = $bright_db_search -> toArray();

            $rets -> disconnect();

            if (count($bright_db_search) > 0) {
                $results['results_bright_type'] = 'bright';
                if (count($bright_db_search) > 1) {

                    // see if results have different unit numbers
                    if ($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
                        $property_details['multiple'] = true;
                        return $property_details;
                        die();
                    } else {
                        $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                    }

                } else {
                    $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                }

            }

        }

        ///// END BRIGHT SEARCH /////

        ///// DATABASE SEARCH FOR OLD PROPERTIES /////
        if (count($bright_db_search) == 0) {

            if ($request -> mls) {

                $bright_db_search = ListingsRemovedData::select($select_columns_db) -> where('ListingId', $ListingId) -> get() -> toArray();

            } else {

                $bright_db_search = ListingsRemovedData::select($select_columns_db) -> ListingSearch($state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt);

            }

            if (count($bright_db_search) > 0) {
                $results['results_bright_type'] = 'db_closed';
                if (count($bright_db_search) > 1) {

                    // see if results have different unit numbers
                    if ($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
                        $property_details['multiple'] = true;
                        return $property_details;
                        die();
                    } else {
                        $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                    }

                } else {
                    $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                }

            }

        }

        ///// END DATABASE SEARCH FOR OLD PROPERTIES /////

        // get only most recent result
        if (count($bright_db_search) == 0) {
            $bright_db_search = null;
        }

        // get tax id to search for more property details
        $tax_id = '';

        if (isset($bright_db_search[0]['ListingTaxID'])) {
            $tax_id = $bright_db_search[0]['ListingTaxID'];
        }

        // search tax records by tax id if exists, otherwise use address
        $tax_record_search = '';

        if ($state == 'MD') {
            $functions = new GlobalFunctionsController();
            $tax_record_search = $functions -> tax_records($street_number, $street_name, $unit, $zip, $tax_id, $state);

            if (is_array($tax_record_search)) {
                // set tax id in case searched by address
                $results['results_tax_id'] = $tax_record_search['ListingTaxID'];
            }

        }

        // if only brightmls results
        if ($bright_db_search && !is_array($tax_record_search)) {

            $property_details = $bright_db_search[0];

            // if only tax record results
        } elseif (!$bright_db_search && is_array($tax_record_search)) {

            $property_details = $tax_record_search;

            // if both results
        } elseif ($bright_db_search && is_array($tax_record_search)) {

            // keep bright results, replace a few and add rest from tax records
            $property_details = $bright_db_search[0];
            $property_details['Owner1'] = null;
            $property_details['Owner2'] = null;

            if (isset($tax_record_search['Owner1']) && $tax_record_search['Owner1'] != '') {
                $property_details['Owner1'] = $tax_record_search['Owner1'] ?? null;
                $property_details['Owner2'] = $tax_record_search['Owner2'] ?? null;
            }

            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'] ?? null;
            $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'] ?? null;

        } else {

            $property_details = [];

        }

        if (count($property_details) > 0) {
            $property_details['results_bright_type'] = $results['results_bright_type'] ?? null;
            $property_details['results_bright_id'] = $results['results_bright_id'] ?? null;
            $property_details['results_tax_id'] = $results['results_tax_id'] ?? null;
            $property_details['multiple'] = false;
        }

        return $property_details;

    }

    public function update_county_select(Request $request) {
        $counties = LocationData::select('county') -> where('state', $request -> state) -> groupBy('county') -> orderBy('county') -> get() -> toJson();
        return $counties;
    }

}
