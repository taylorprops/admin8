<?php
namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Transactions\Data\ListingsData;
use App\Models\DocManagement\Transactions\Data\ListingsRemoved\Data;
use App\Models\DocManagement\Resources\Zips;
use Illuminate\Http\Request;
use Config;
use App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Listings;

class AddTransactionController extends Controller {

    public function add_contract() {
        return view('/agents/doc_management/transactions/contracts/add_contract');
    }

    public function listing(Request $request) {
        $id = $request -> id;
        // if agent logged in filter by agent_id
        $listing = Listings::where('listing_id', $id) -> first();
        return view('/agents/doc_management/transactions/listing', compact('listing'));

    }

    public function add_listing_page() {
        $states = Zips::ActiveStates();
        return view('/agents/doc_management/transactions/add_listing/add_listing_page', compact('states'));
    }

    public function save_add_listing(Request $request) {

        $property_details = (object) session('property_details');

        if($request -> agent_id) {
            $property_details -> agent_id = $request -> agent_id;
        } else {
            $property_details -> agent_id = auth() -> user() -> id;
        }
        $property_details -> SaleRent = $request -> listing_type ?? null;
        $property_details -> PropertyType = $request -> property_type ?? null;
        $property_details -> PropertySubType = $request -> property_sub_type ?? null;
        $property_details -> YearBuilt = $request -> year_built ?? null;
        $property_details -> ListPrice = $request -> list_price ?? null;
        $property_details -> HoaCondoFees = $request -> hoa_condo ?? null;

        $listing = new Listings();
        foreach($property_details as $key => $val) {
            $listing -> $key = $val ?? null;
        }
        $listing -> save();
        $transaction_id = $listing -> id;

        return $transaction_id;

    }

    public function add_listing_details_new(Request $request) {

        $property_details = [
            'StreetNumber' => $request -> street_number,
            'StreetName' => $request -> street_name,
            'StreetDirPrefix' => $request -> street_dir,
            'UnitNumber' => $request -> unit_number,
            'City' => $request -> city,
            'StateOrProvince' => $request -> state,
            'PostalCode' => $request -> zip,
            'County' => $request -> county
        ];

        $property_details = (object) $property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('/agents/doc_management/transactions/add_listing/add_listing_details', compact('property_details', 'property_types', 'property_sub_types'));
    }

    public function add_listing_details_existing(Request $request) {

        $bright_type = $request -> bright_type;
        $bright_id = $request -> bright_id;
        $state = $request -> state;
        $tax_id = '';
        $bright_db_search = '';
        // only pulling tax records from MD
        if($state == 'MD') {
            $tax_id = $request -> tax_id;
        }


        $select_columns_bright = 'Appliances,AssociationFee,AssociationFeeFrequency,AssociationYN,AttachedGarageYN,BasementFinishedPercent,BasementYN,BathroomsTotalInteger,BedroomsTotal,City,CloseDate,CondoYN,Cooling,County,ElementarySchool,FireplaceYN,FullStreetAddress,GarageYN,Heating,HighSchool,Latitude,ListingId,ListingSourceRecordKey,ListingTaxID,ListPictureURL,ListPrice,LivingArea,Longitude,LotSizeAcres,LotSizeSquareFeet,MajorChangeTimestamp,MiddleOrJuniorSchool,MLSListDate,MlsStatus,NewConstructionYN,NumAttachedGarageSpaces,NumDetachedGarageSpaces,Pool,PostalCode,PropertySubType,PropertyType,PublicRemarks,SaleType,StateOrProvince,StreetDirPrefix,StreetDirSuffix,StreetName,StreetNumber,StreetSuffix,StreetSuffixModifier,StructureDesignType,SubdivisionName,TotalPhotos,UnitBuildingType,UnitNumber,YearBuilt,ListOfficeName,ListOfficeMlsId,ListAgentMlsId,ListAgentFirstName,ListAgentLastName,ListAgentEmail,ListAgentPreferredPhone,BuyerOfficeName,BuyerOfficeMlsId,BuyerAgentMlsId,BuyerAgentFirstName,BuyerAgentLastName,BuyerAgentEmail,BuyerAgentPreferredPhone';
        $select_columns_db = explode(',', $select_columns_bright);
        $select_columns_db_closed = 'AssociationFee,AssociationYN,AttachedGarageYN,BasementFinishedPercent,BasementYN,BathroomsTotalInteger,BedroomsTotal,City,CondoYN,County,FireplaceYN,FullStreetAddress,GarageYN,Heating,Latitude,ListingTaxID,ListPictureURL,Longitude,LotSizeAcres,LotSizeSquareFeet,NewConstructionYN,NumAttachedGarageSpaces,NumDetachedGarageSpaces,Pool,PostalCode,PropertySubType,PropertyType,StateOrProvince,StreetDirPrefix,StreetDirSuffix,StreetName,StreetNumber,StreetSuffix,StreetSuffixModifier,StructureDesignType,SubdivisionName,UnitBuildingType,UnitNumber,YearBuilt';
        $select_columns_db_closed = explode(',', $select_columns_db_closed);

        if($bright_type == 'db_active') {

            $bright_db_search = ListingsData::select($select_columns_db) -> where('ListingId', $bright_id) -> first() -> toArray();

        } else if($bright_type == 'db_closed') {

            $bright_db_search = ListingsRemovedData::select($select_columns_db_closed) -> where('ListingId', $bright_id) -> first() -> toArray();

        } else if($bright_type == 'bright') {

            $rets = new \PHRETS\Session(Config::get('rets.rets.rets_config'));
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';
            $query = '(ListingId='.$bright_id.')';

            $bright_db_search = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Select' => $select_columns_bright
                ]
            );

            $bright_db_search = $bright_db_search[0] -> toArray();

            if($bright_db_search['MlsStatus'] == 'CLOSED' && $bright_db_search['CloseDate'] < date("Y-m-d", strtotime("-3 month"))) {
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

        }

        $tax_record_search = null;
        if($tax_id != '') {
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
            $property_details = array_merge($bright_db_search, $tax_record_search);
        }
        $property_details = (object) $property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('/agents/doc_management/transactions/add_listing/add_listing_details', compact('property_details', 'property_types', 'property_sub_types'));
    }

    public function get_property_info(Request $request) {

        $street_number = $street_name = $unit = $zip = $tax_id = $state = '';

        if($request -> mls) {

            $ListingId = $request -> mls;
            $state = substr($request -> mls, 0, 2);

        } else {

            $street_number = $request -> street_number;
            $street_name = $request -> street_name;
            $street_suffix = '';
            $street_dir_suffix = '';
            $street_dir_suffix_alt = '';

            // remove all suffixes and dir suffixes to get just street name. Save them for later
            $street_suffixes_array = array('ALLEY', 'AVENUE', 'BEND', 'BOULEVARD', 'BRANCH', 'CIRCLE', 'CIR', 'CORNER', 'COURSE', 'COURT', 'COVE', 'CRESCENT', 'CROSSING', 'DRIVE', 'DRIVEWAY', 'EXTENSION', 'GARDENS', 'GARTH', 'GATEWAY', 'GLEN', 'GROVE', 'HARBOR', 'HIGHWAY', 'HILL', 'HOLLOW', 'KNOLLS', 'LANDING', 'LANE', 'LOOP', 'MEWS', 'MILLS', 'NORTHWAY', 'PARKWAY', 'PASSAGE', 'PATH', 'PIKE', 'PLACE', 'RIDGE', 'ROAD', 'ROUTE', 'ROW', 'RUN', 'SQUARE', 'STREET', 'TERRACE', 'TRACE', 'TRAIL', 'TURN', 'VIEW', 'VISTA', 'WALK', 'WAY');

            $street_dir_suffixes_array = array('E', 'EAST', 'N', 'NE', 'NORTH', 'NORTHEAST', 'NORTHWEST', 'NW', 'S', 'SE', 'SOUTH', 'SOUTHEAST', 'SOUTHWEST', 'SW', 'W', 'WEST');

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
                array('orig' => 'SOUTHWEST', 'alt' => 'SW')
            );

            foreach ($street_suffixes_array as $street_suffixes) {
                if (preg_match('/\s\b(' . $street_suffixes . '(?!.*' . $street_suffixes . '))\b/i', $street_name, $matches)) {
                    $street_name = preg_replace('/\\s\b(' . $street_suffixes . '(?!.*' . $street_suffixes . '))\b/i', '', $street_name);
                    $street_suffix = trim($matches[0]);
                }
            }

            foreach ($street_dir_suffixes_array as $street_dir_suffixes) {
                if (preg_match('/\s\b(' . $street_dir_suffixes . ')\b/i', $street_name, $matches)) {
                    $street_name = preg_replace('/\s\b(' . $street_dir_suffixes . ')\b/i', '', $street_name);
                    $street_dir_suffix = trim($matches[0]);

                    foreach($street_dir_suffixes_alt_array as $dir) {
                        if(strtolower($dir['orig']) == strtolower($street_dir_suffix)) {
                            $street_dir_suffix_alt = $dir['alt'];
                        }
                    }
                }
            }

            $street_dir_suffix_bright_dmql = '';
            if($street_dir_suffix != '') {
                $street_dir_suffix_bright_dmql = ',(((StreetDirSuffix=|'.$street_dir_suffix.')|(StreetDirSuffix=|'.$street_dir_suffix_alt.'))|((StreetDirPrefix=|'.$street_dir_suffix.')|(StreetDirPrefix=|'.$street_dir_suffix_alt.')))';
            }

            $unit = $request -> unit;
            $unit_bright_dmql = '';
            if($unit != '') {
                $unit_bright_dmql = ',(UnitNumber=*'.$unit.'*)';
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
        if($request -> mls) {

            $bright_db_search = ListingsData::select($select_columns_db) -> where('ListingId', $ListingId) -> get() -> toArray();

        } else {

            $bright_db_search = ListingsData::select($select_columns_db) -> ListingSearch($state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt);

        }

        if (count($bright_db_search) > 0) {
            $results['results_bright_type'] = 'db_active';
            if (count($bright_db_search) > 1) {
                // see if results have different unit numbers
                if($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
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
            if($request -> mls) {
                $query = '(ListingId='.$ListingId.')';
            } else {
                $query = '(StateOrProvince=|'.$state.'),(PostalCode='.$zip.'),(StreetNumber='.$street_number.'),(StreetName='.$street_name.'*)' . $unit_bright_dmql . $street_dir_suffix_bright_dmql;
            }

            $bright_db_search = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Select' => $select_columns_bright
                ]
            );

            $bright_db_search = $bright_db_search -> toArray();

            $rets -> disconnect();

            if (count($bright_db_search) > 0) {
                $results['results_bright_type'] = 'bright';
                if (count($bright_db_search) > 1) {
                    // see if results have different unit numbers
                    if($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
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

            if($request -> mls) {

                $bright_db_search = ListingsRemovedData::select($select_columns_db) -> where('ListingId', $ListingId) -> get() -> toArray();

            } else {

                $bright_db_search = ListingsRemovedData::select($select_columns_db) -> ListingSearch($state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt);

            }

            if (count($bright_db_search) > 0) {
                $results['results_bright_type'] = 'db_closed';
                if (count($bright_db_search) > 1) {
                    // see if results have different unit numbers
                    if($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
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
            if(is_array($tax_record_search)) {
                // set tax id in case searched by address
                $results['results_tax_id'] = $tax_record_search['ListingTaxID'];
            }
        }


        // if only brightmls results
        if ($bright_db_search && !is_array($tax_record_search)) {

            $property_details = $bright_db_search[0];

        // if only tax record results
        } else if (!$bright_db_search && is_array($tax_record_search)) {

            $property_details = $tax_record_search;

        // if both results
        } else if ($bright_db_search && is_array($tax_record_search)) {

            // keep bright results, replace a few and add rest from tax records
            $property_details = $bright_db_search[0];
            $property_details['Owner1'] = null;
            $property_details['Owner2'] = null;
            if($tax_record_search['Owner1'] != '') {
                $property_details['Owner1'] = $tax_record_search['Owner1'] ?? null;
                $property_details['Owner2'] = $tax_record_search['Owner2'] ?? null;
            }
            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'] ?? null;
            $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'] ?? null;

        } else {

            $property_details = [];

        }

        if(count($property_details) > 0) {
            $property_details['results_bright_type'] = $results['results_bright_type'] ?? null;
            $property_details['results_bright_id'] = $results['results_bright_id'] ?? null;
            $property_details['results_tax_id'] = $results['results_tax_id'] ?? null;
            $property_details['multiple'] = false;
        }

        return $property_details;

    }

    public function update_county_select(Request $request) {
        $counties = Zips::select('county') -> where('state', $request -> state) -> groupBy('county') -> orderBy('county') -> get() -> toJson();
        return $counties;
    }

}
