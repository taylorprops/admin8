<?php
namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Listings;
use App\Models\DocManagement\Zips;
use Illuminate\Http\Request;
use Config;

class AddTransactionController extends Controller {
    public function add_contract() {
        return view('/agents/doc_management/transactions/add_contract');
    }

    public function add_listing() {
        $states = Zips::ActiveStates();
        return view('/agents/doc_management/transactions/add_listing', compact('states'));
    }

    public function get_property_info(Request $request) {

        $street_number = $request -> street_number;
        $street_name = $request -> street_name;
        $street_suffix = '';
        $street_dir_suffix = '';

        // remove all suffixes and dir suffixes to get just street name. Save them for later
        $street_suffixes_array = array('ALLEY', 'AVENUE', 'BEND', 'BOULEVARD', 'BRANCH', 'CIRCLE', 'CIR', 'CORNER', 'COURSE', 'COURT', 'COVE', 'CRESCENT', 'CROSSING', 'DRIVE', 'DRIVEWAY', 'EXTENSION', 'GARDENS', 'GARTH', 'GATEWAY', 'GLEN', 'GROVE', 'HARBOR', 'HIGHWAY', 'HILL', 'HOLLOW', 'KNOLLS', 'LANDING', 'LANE', 'LOOP', 'MEWS', 'MILLS', 'NORTHWAY', 'PARKWAY', 'PASSAGE', 'PATH', 'PIKE', 'PLACE', 'RIDGE', 'ROAD', 'ROUTE', 'ROW', 'RUN', 'SQUARE', 'STREET', 'TERRACE', 'TRACE', 'TRAIL', 'TURN', 'VIEW', 'VISTA', 'WALK', 'WAY');

        $street_dir_suffixes_array = array('E', 'EAST', 'N', 'NE', 'NORTH', 'NORTHEAST', 'NORTHWEST', 'NW', 'S', 'SE', 'SOUTH', 'SOUTHEAST', 'SOUTHWEST', 'SW', 'W', 'WEST');

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
            }
        }
        $street_dir_suffix_sql = '';
        if($street_dir_suffix != '') {
            $street_dir_suffix_sql = ',((StreetDirSuffix=|'.$street_dir_suffix.')|(StreetDirSuffix=.EMPTY.))';
        }

        $unit = $request -> unit;
        $unit_sql = '';
        if($unit != '') {
            $unit_sql = ',(UnitNumber=*'.$unit.'*)';
        }
        $city = $request -> city;
        $state = $request -> state;
        $zip = $request -> zip;
        $county = $request -> county;

        ///// DATABASE SEARCH FOR PROPERTY /////
        $bright_db_search = Listings::select('AssociationFee', 'AssociationFeeFrequency', 'AssociationYN', 'BathroomsTotalInteger', 'BedroomsTotal', 'CloseDate', 'CondoYN', 'Latitude', 'ListingId', 'ListingSourceRecordKey', 'ListingTaxID', 'ListOfficeName', 'ListPictureURL', 'ListPrice', 'Longitude', 'LotSizeAcres', 'MajorChangeTimestamp', 'MLSListDate', 'MlsStatus', 'NewConstructionYN', 'PropertySubType', 'PropertyType', 'SubdivisionName', 'UnitBuildingType', 'YearBuilt', 'FullStreetAddress', 'StreetDirPrefix', 'StreetDirSuffix', 'StreetName', 'StreetSuffix', 'StreetSuffixModifier', 'UnitNumber', 'City', 'County', 'StreetNumber', 'PostalCode', 'StateOrProvince') -> where('StateOrProvince', $state) -> where('PostalCode', $zip) -> where('StreetNumber', $street_number) -> where('StreetName', 'LIKE', $street_name . '%') -> where('UnitNumber', 'LIKE', '%' . $unit . '%') -> where('StreetDirSuffix', 'LIKE', $street_dir_suffix . '%') -> get() -> toArray();

        ///// END DATABASE SEARCH FOR PROPERTY /////

        // If not found in database search search bright mls
        if (count($bright_db_search) == 0) {

            ///// BRIGHT SEARCH /////
            $rets = new \PHRETS\Session(Config::get('rets.rets.rets_config'));
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';

            // get property results from brightmls
            $query = '(StateOrProvince=|'.$state.'),(PostalCode='.$zip.'),(StreetNumber='.$street_number.'),(StreetName='.$street_name.'*)'.$unit_sql.$street_dir_suffix_sql;

            $bright_db_search = $rets -> Search(
            $resource,
            $class,
            $query,
                [
                'Count' => 0,
                'Select' => 'AssociationFee, AssociationFeeFrequency, AssociationYN, BathroomsTotalInteger, BedroomsTotal, CloseDate, CondoYN, Latitude, ListingId, ListingSourceRecordKey, ListingTaxID, ListOfficeName, ListPictureURL, ListPrice, Longitude, LotSizeAcres, MajorChangeTimestamp, MLSListDate, MlsStatus, NewConstructionYN, PropertySubType, PropertyType, SubdivisionName, UnitBuildingType, YearBuilt, FullStreetAddress, StreetDirPrefix, StreetDirSuffix, StreetName, StreetSuffix, StreetSuffixModifier, UnitNumber, City, County, StreetNumber, PostalCode, StateOrProvince'
                ]
            );

            $bright_db_search = $bright_db_search -> toArray();

            $rets -> disconnect();

        }

        ///// END BRIGHT SEARCH /////

        // get only most recent result
        if (count($bright_db_search) == 0) {
            $bright_db_search = null;
        } else {
            $bright_db_search = end($bright_db_search);
        }

        // get tax id to search for more property details
        $tax_id = '';
        if (isset($bright_db_search['ListingTaxID'])) {
            $tax_id = $bright_db_search['ListingTaxID'];
        }

        // search tax records by tax id if exists, otherwise use address
        $tax_record_search = '';
        if ($state == 'MD') {
            $tax_record_search = app('App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController') -> tax_records($street_number, $street_name, $unit, $zip, $tax_id, $state);
        }

        $property_details = null;
        // if only brightmls results
        if (!$bright_db_search && is_array($tax_record_search)) {
            $property_details = $bright_db_search;
        } else

        // if only tax record results
        if ($bright_db_search && !is_array($tax_record_search)) {
            $property_details = $tax_record_search;
        } else

        // if both results
        if ($bright_db_search && is_array($tax_record_search)) {
            // keep bright results, replace a few and add rest from tax records
            $property_details = $bright_db_search;
            $property_details['Owner1'] = $tax_record_search['Owner1'];
            $property_details['Owner2'] = $tax_record_search['Owner2'];
            $property_details['Longitude'] = $tax_record_search['Longitude'];
            $property_details['Latitude'] = $tax_record_search['Latitude'];
            $property_details['DeedReference1'] = $tax_record_search['DeedReference1'];
            $property_details['Deed Reference2'] = $tax_record_search['Deed Reference2'];
            $property_details['TownCode'] = $tax_record_search['TownCode'];
            $property_details['Subdivision Code'] = $tax_record_search['Subdivision Code'];
            $property_details['Map'] = $tax_record_search['Map'];
            $property_details['Grid'] = $tax_record_search['Grid'];
            $property_details['Parcel'] = $tax_record_search['Parcel'];
            $property_details['ZoningCode'] = $tax_record_search['ZoningCode'];
            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'];
            $property_details['UtilitiesWater'] = $tax_record_search['UtilitiesWater'];
            $property_details['UtilitiesSewage'] = $tax_record_search['UtilitiesSewage'];
            $property_details['District'] = $tax_record_search['District'];
            $property_details['LegalDescription1'] = $tax_record_search['LegalDescription1'];
            $property_details['LegalDescription2'] = $tax_record_search['LegalDescription2'];
            $property_details['LegalDescription3'] = $tax_record_search['LegalDescription3'];
            $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'];
        }

        return $property_details;

    }

    public function update_county_select(Request $request) {
        $counties = Zips::select('county') -> where('state', $request -> state) -> groupBy('county') -> orderBy('county') -> get() -> toJson();
        return $counties;
    }
}
