<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Listings;

use Config;
use Illuminate\Http\Request;

use App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Data\ListingsData;
use App\Models\DocManagement\Transactions\Data\ListingsRemovedData;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Members\Members;
// use App\Models\DocManagement\Checklists\Checklists;
// use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\Employees\Agents;
use App\Models\Resources\LocationData;
use App\Models\CRM\CRMContacts;



class ListingAddController extends Controller {

    public function add_listing_details_existing(Request $request) {

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

        $property_details = (object)$property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('/agents/doc_management/transactions/listings/listing_add_details', compact('property_details', 'property_types', 'property_sub_types'));
    }

    public function add_listing_details_new(Request $request) {

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

        $property_details = (object)$property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('/agents/doc_management/transactions/listings/listing_add_details', compact('property_details', 'property_types', 'property_sub_types'));
    }

    public function add_listing_page() {
        $states = LocationData::ActiveStates();
        return view('/agents/doc_management/transactions/listings/listing_add', compact('states'));
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

    public function listing_required_details(Request $request) {
        $property_details = Listings::where('Listing_ID', $request -> id) -> first();
        $states = LocationData::AllStates();
        $states_json = $states -> toJson();
        $statuses = ResourceItems::where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();
        $contacts = CRMContacts::where('Agent_ID', $property_details -> Agent_ID) -> get();
        $resource_items = new ResourceItems();

        return view('/agents/doc_management/transactions/listings/listing_required_details', compact('property_details', 'states', 'states_json', 'statuses', 'contacts', 'resource_items'));
    }

    public function save_add_listing(Request $request) {

        $property_details = (object)session('property_details');
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
        $property_details -> ListAgentFirstName = $agent -> first_name;
        $property_details -> ListAgentLastName = $agent -> last_name;
        $property_details -> ListAgentEmail = $agent -> email;
        $property_details -> ListAgentPreferredPhone = $agent -> cell_phone;

        // replace current values from property details with new data
        $property_details -> SaleRent = $request -> listing_type;
        $property_details -> PropertyType = $resource_items -> GetResourceID($request -> property_type, 'checklist_property_types'); // convert to integer
        $property_details -> PropertySubType = $resource_items -> GetResourceID($request -> property_sub_type, 'checklist_property_sub_types'); // convert to integer
        $property_details -> YearBuilt = $request -> year_built ?? null;
        $property_details -> ListPrice = preg_replace('/[\$,]+/', '', $request -> list_price);
        $property_details -> HoaCondoFees = $request -> hoa_condo ?? null;
        if($property_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($property_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($property_details -> StateOrProvince, 'checklist_locations');
        }
        $property_details -> Location_ID = $location_id;

        $listing = new Listings;

        foreach ($property_details as $key => $val) {
            $listing -> $key = $val ?? null;
        }

        $listing -> save();
        $listing_id = $listing -> Listing_ID;

        // add default docs folders
        $new_folder = new TransactionDocumentsFolders();
        $new_folder -> Listing_ID = $listing -> Listing_ID;
        $new_folder -> Agent_ID = $agent -> id;
        $new_folder -> folder_name = 'Listing Documents';
        $new_folder -> order = 0;
        $new_folder -> save();

        $new_folder = new TransactionDocumentsFolders();
        $new_folder -> Listing_ID = $listing -> Listing_ID;
        $new_folder -> Agent_ID = $agent -> id;
        $new_folder -> folder_name = 'Trash';
        $new_folder -> order = 100;
        $new_folder -> save();


        return $listing_id;

    }

    public function save_listing_required_details(Request $request) {
        $Listing_ID = $request -> Listing_ID;

        // add sellers to doc_members
        $seller_first = $request -> seller_first_name;
        $seller_last = $request -> seller_last_name;
        $seller_phone = $request -> seller_phone;
        $seller_email = $request -> seller_email;
        $address_street = $request -> seller_street;
        $address_city = $request -> seller_city;
        $address_state = $request -> seller_state;
        $address_zip = $request -> seller_zip;
        $seller_crm_contact_id = $request -> seller_crm_contact_id;
        $Agent_ID = $request -> Agent_ID;


        for ($i = 0; $i < count($seller_first); $i++) {

            $members = Members::where('email', $seller_email[$i]) -> where('Listing_ID', $Listing_ID) -> where('Agent_ID', $Agent_ID) -> first();
            if(!$members) {
                $members = new Members;
            }
            $members -> first_name = $seller_first[$i];
            $members -> last_name = $seller_last[$i];
            $members -> cell_phone = $seller_phone[$i];
            $members -> email = $seller_email[$i] ?? null;
            $members -> address_street = $address_street[$i];
            $members -> address_city = $address_city[$i];
            $members -> address_state = $address_state[$i];
            $members -> address_zip = $address_zip[$i];
            $members -> CRMContact_ID = $seller_crm_contact_id[$i] ?? 0;
            $members -> member_type_id = ResourceItems::SellerResourceId();
            $members -> Agent_ID = $Agent_ID;
            $members -> Listing_ID = $Listing_ID;
            $members -> save();


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

        // update listing
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $listing -> SellerOneFirstName = $seller_one_first;
        $listing -> SellerOneLastName = $seller_one_last;
        $listing -> SellerTwoFirstName = $seller_two_first;
        $listing -> SellerTwoLastName = $seller_two_last;
        $listing -> MLSListDate = $request -> MLSListDate;
        $listing -> ExpirationDate = $request -> ExpirationDate;
        if(date('Y-m-d') < $request -> MLSListDate) {
            $listing -> Status = ResourceItems::GetResourceID('Pre-Listing', 'listing_status');
        } else if(date('Y-m-d') > $request -> MLSListDate && date('Y-m-d') < $request -> ExpirationDate) {
            $listing -> Status = ResourceItems::GetResourceID('Active', 'listing_status');
        } else {
            $listing -> Status = ResourceItems::GetResourceID('Expired', 'listing_status');
        }
        $listing -> save();

        // add checklist and checklist items
        $checklist_represent = 'seller';
        $checklist_type = 'listing';
        $checklist_property_type_id = $listing -> PropertyType;
        $checklist_property_sub_type_id = $listing -> PropertySubType;
        $checklist_sale_rent = $listing -> SaleRent;
        $checklist_state = $listing -> StateOrProvince;
        $checklist_location_id = $listing -> Location_ID;
        $checklist_hoa_condo = $listing -> HoaCondoFees;
        $checklist_year_built = $listing -> YearBuilt;
        $checklist_id = '';

        TransactionChecklists::CreateListingChecklist($checklist_id, $Listing_ID, $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        return $Listing_ID;

    }

    public function update_county_select(Request $request) {
        $counties = LocationData::select('county') -> where('state', $request -> state) -> groupBy('county') -> orderBy('county') -> get() -> toJson();
        return $counties;
    }

}
