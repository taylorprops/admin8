<?php

<?php
namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Listings;
use App\Models\DocManagement\ListingsRemoved;
use App\Models\DocManagement\Zips;
use Illuminate\Http\Request;
use Config;
use App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController;

class AddTransactionController extends Controller {
    public function add_contract() {
        return view('/agents/doc_management/transactions/contracts/add_contract');
    }

    public function add_listing() {
        $states = Zips::ActiveStates();
        return view('/agents/doc_management/transactions/listings/add_listing', compact('states'));
    }

    public function add_listing_details_existing(Request $request) {

        $bright_type = $request -> bright_type;
        $bright_id = $request -> bright_id;
        $tax_id = $request -> tax_id;

        $select_columns_db = array('ListPictureURL', 'FullStreetAddress', 'City', 'StateOrProvince', 'County', 'PostalCode', 'YearBuilt', 'BathroomsTotalInteger', 'BedroomsTotal', 'MlsStatus', 'ListingId', 'ListPrice', 'PropertyType', 'ListOfficeName', 'MLSListDate', 'ListAgentFirstName', 'ListAgentLastName', 'UnitNumber');
        $select_columns_bright = 'ListPictureURL, FullStreetAddress, City, StateOrProvince, County, PostalCode, YearBuilt, BathroomsTotalInteger, BedroomsTotal, MlsStatus, ListingId, ListPrice, PropertyType, ListOfficeName, MLSListDate, ListAgentFirstName, ListAgentLastName, UnitNumber';

        if($bright_type == 'db_active') {

            $bright_db_search = Listings::select( $select_columns_db ) -> where('ListingId', $bright_id) -> first();


        } else if( $bright_type == 'db_closed' ) {

            $bright_db_search = ListingsRemoved::select($select_columns_db) -> where('ListingId', $bright_id) -> first();

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

            $bright_db_search = $bright_db_search -> toArray();

            $rets -> disconnect();

        }

        if($tax_id != '') {
            $functions = new GlobalFunctionsController();
            $tax_record_search = $functions -> tax_records('', '', '', '', $tax_id, '');
            $tax_record_search = (object) $tax_record_search;
        }

        $property_details = array();
        // if only brightmls results
        if ($bright_db_search && $tax_id == '') {

            $property_details = $bright_db_search;

        // if only tax record results
        } else if (!$bright_db_search && $tax_id != '') {

            $property_details = $tax_record_search;

        } else if ($bright_db_search && $tax_id != '') {

            // keep bright results, replace a few and add rest from tax records
            $property_details = $bright_db_search;
            $property_details -> Owner1 = '';
            $property_details -> Owner2 = '';
            if($property_details -> Owner1 != '') {
                $property_details -> Owner1 = $t7ax_record_search -> Owner1 ?? null;
                $property_details -> Owner2 = $tax_record_search -> Owner2 ?? null;
            }
            $property_details -> ResidenceType = $tax_record_search -> ResidenceType ?? null;
            $property_details -> TaxRecordLink = $tax_record_search -> TaxRecordLink ?? null;
        }

dd($property_details);

        return view('/agents/doc_management/transactions/listings/add_listing_details_existing', compact('property_details'));
    }

    public function get_property_info(Request $request) {

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

        $select_columns_db = array('ListPictureURL', 'FullStreetAddress', 'City', 'StateOrProvince', 'County', 'PostalCode', 'YearBuilt', 'BathroomsTotalInteger', 'BedroomsTotal', 'MlsStatus', 'ListingId', 'ListPrice', 'PropertyType', 'ListOfficeName', 'MLSListDate', 'ListAgentFirstName', 'ListAgentLastName', 'UnitNumber', 'CloseDate');
        $select_columns_bright = 'ListPictureURL, FullStreetAddress, City, StateOrProvince, County, PostalCode, YearBuilt, BathroomsTotalInteger, BedroomsTotal, MlsStatus, ListingId, ListPrice, PropertyType, ListOfficeName, MLSListDate, ListAgentFirstName, ListAgentLastName, UnitNumber, CloseDate';

        $property_details = null;
        $results = [];
        $results['multiple'] = false;

        ///// DATABASE SEARCH FOR PROPERTY /////
        $bright_db_search = Listings::select($select_columns_db) -> where('StateOrProvince', $state) -> where('PostalCode', $zip)
            -> where('StreetNumber', $street_number)
            -> where('StreetName', 'LIKE', $street_name . '%')
            -> where('UnitNumber', 'LIKE', '%' . $unit . '%')
            -> where(function ($q) use ($street_dir_suffix, $street_dir_suffix_alt) {
                $q -> where('StreetDirSuffix', $street_dir_suffix)
                    -> orWhere('StreetDirSuffix', $street_dir_suffix_alt);
                $q -> where('StreetDirPrefix', $street_dir_suffix)
                    -> orWhere('StreetDirPrefix', $street_dir_suffix_alt);
            })
            -> orderBy('MLSListDate', 'DESC')
            -> get() -> toArray();

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

        ///// DATABASE SEARCH FOR OLD PROPERTIES /////
        if (count($bright_db_search) == 0) {
            $bright_db_search = ListingsRemoved::select($select_columns_db) -> where('StateOrProvince', $state) -> where('PostalCode', $zip)
                -> where('StreetNumber', $street_number)
                -> where('StreetName', 'LIKE', $street_name . '%')
                -> where('UnitNumber', 'LIKE', '%' . $unit . '%')
                -> where(function ($q) use ($street_dir_suffix, $street_dir_suffix_alt) {
                    $q -> where('StreetDirSuffix', $street_dir_suffix)
                        -> orWhere('StreetDirSuffix', $street_dir_suffix_alt);
                    $q -> where('StreetDirPrefix', $street_dir_suffix)
                        -> orWhere('StreetDirPrefix', $street_dir_suffix_alt);
                })
                -> orderBy('MLSListDate', 'DESC')
                -> get() -> toArray();

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

        // If not found in database search search bright mls
        if (count($bright_db_search) == 0) {

            ///// BRIGHT SEARCH /////
            $rets = new \PHRETS\Session(Config::get('rets.rets.rets_config'));
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';

            // get property results from brightmls
            $query = '(StateOrProvince=|'.$state.'),(PostalCode='.$zip.'),(StreetNumber='.$street_number.'),(StreetName='.$street_name.'*)' . $unit_bright_dmql . $street_dir_suffix_bright_dmql;

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
                $results['results_tax_id'] = $tax_record_search['ListingTaxID'];;
            }
        }


        // if only brightmls results
        if ($bright_db_search && !is_array($tax_record_search)) {
            $property_details = $bright_db_search[0];
        } else

        // if only tax record results
        if (!$bright_db_search && is_array($tax_record_search)) {
            $property_details = $tax_record_search;
        } else

        // if both results
        if ($bright_db_search && is_array($tax_record_search)) {

            // keep bright results, replace a few and add rest from tax records
            $property_details = $bright_db_search[0];
            $property_details['Owner1'] = '';
            $property_details['Owner2'] = '';
            if($property_details['Owner1'] != '') {
                $property_details['Owner1'] = $tax_record_search['Owner1'] ?? null;
                $property_details['Owner2'] = $tax_record_search['Owner2'] ?? null;
            }
            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'] ?? null;
            $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'] ?? null;
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
