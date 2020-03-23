<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Listings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;

use App\Models\DocManagement\Transactions\Listings;
use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\Employees\Agents;
use App\Models\Employees\InHouse;
use App\Models\Employees\LoanOfficers;
use App\Models\Employees\Teams;
use App\Models\Resources\LocationData;
use App\Models\CRM\CRMContacts;


class ListingDetailsController extends Controller {


    // Get section content
    public function get_details(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $agents = Agents::where('active', 'yes') -> orderBy('last_name') -> get();
        $teams = Teams::where('active', 'yes') -> orderBy('team_name') -> get();
        $street_suffixes = config('global.vars.street_suffixes');
        $street_dir_suffixes = config('global.vars.street_dir_suffixes');
        $states = config('global.vars.active_states');
        $listing_state = $listing -> StateOrProvince;
        $counties = LocationData::CountiesByState($listing_state);
        $trans_coords = TransactionCoordinators::where('active', 'yes') -> orderBy('last_name') -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_details', compact('Listing_ID', 'listing', 'agents', 'teams', 'street_suffixes', 'street_dir_suffixes', 'states', 'counties', 'trans_coords'));
    }

    public function get_members(Request $request) {
        $listing = Listings::find($request -> Listing_ID);
        $members = Members::where('Listing_ID', $request -> Listing_ID) -> get();
        $resource_items = new ResourceItems();
        $contact_types = $resource_items -> where('resource_type', 'contact_type') -> get();
        $states = LocationData::AllStates();
        $contacts = CRMContacts::where('Agent_ID', $listing -> Agent_ID) -> get();
        return view('/agents/doc_management/transactions/listings/details/data/get_members', compact('members', 'contact_types', 'resource_items', 'states', 'contacts'));
    }

    // Save section content
    public function save_details(Request $request) {
        $listing = Listings::find($request -> Listing_ID);

        // mls needs to be verified. if not MLS_Verified needs to be set to no
        $listing -> MLS_Verified = 'no';
        if(bright_mls_search($request -> ListingId)) {
            $listing -> MLS_Verified = 'yes';
        }

        $data = $request -> all();
        foreach ($data as $col => $val) {
            if($col != 'Listing_ID' && !stristr($col, '_submit')) {
                if($col == 'ListPrice') {
                    $val = preg_replace('/[\$,]+/', '', $val);
                }
                $listing -> $col = $val;
            }
        }
        $listing -> save();
        return response() -> json([
            'status' => 'ok'
        ]);
    }

    public function save_members(Request $request) {

    }

    public function save_mls_search(Request $request) {

        $listing_details = Listings::find($request -> Listing_ID);
        $mls_search_details = bright_mls_search($request -> ListingId);
        $mls_search_details = (object) $mls_search_details;

        // get cols and vals for mls search
        foreach($mls_search_details as $col => $val) {
            // if listing_details col matches then update it if it doesn't match original value
            if(isset($listing_details -> $col)) {
                if($listing_details -> $col != $val && $val != '') {
                    // if a name field only replace if blank
                    if(in_array($listing_details -> $col, config('global.vars.select_columns_bright_agents'))) {
                        if($val == '') {
                            $listing_details -> $col = $val;
                        }
                    } else {
                        $listing_details -> $col = $val;
                    }
                }
            }
        }
        $listing_details -> MLS_Verified = 'yes';
        $listing_details -> save();

        return response() -> json([
            'status' => 'ok'
        ]);

    }

    public function mls_search(Request $request) {

        $listing_details = Listings::find($request -> Listing_ID);
        $mls_search_details = bright_mls_search($request -> ListingId);
        $mls_search_details = (object) $mls_search_details;

        // if county does not match a new checklist will have to be created
        $county_match = true;
        // only if mls search produced results
        if(isset($mls_search_details -> ListingId)) {
            // get cols and vals for mls search
            foreach($mls_search_details as $col => $val) {
                // if col is county make sure it matches. If not the checklist needs to be switched to a new one
                if($col == 'County') {
                    if($listing_details -> $col != $val) {
                        $county_match = false;
                    }
                }
            }

            if($county_match == false) {
                return response() -> json([
                    'status' => 'ok',
                    'county_match' => 'no',
                    'address' => $mls_search_details -> FullStreetAddress,
                    'city' => $mls_search_details -> City,
                    'state' => $mls_search_details -> StateOrProvince,
                    'zip' => $mls_search_details -> PostalCode,
                    'picture_url' => $mls_search_details -> ListPictureURL,
                    'list_company' => $mls_search_details -> ListOfficeName
                ]);
            }
            return response() -> json([
                'status' => 'ok',
                'county_match' => 'yes',
                'address' => $mls_search_details -> FullStreetAddress,
                'city' => $mls_search_details -> City,
                'state' => $mls_search_details -> StateOrProvince,
                'zip' => $mls_search_details -> PostalCode,
                'picture_url' => $mls_search_details -> ListPictureURL,
                'list_company' => $mls_search_details -> ListOfficeName
            ]);
        }
        return response() -> json([
            'status' => 'not found'
        ]);
    }

    function listings_all(Request $request) {
        $listings = Listings::where('Agent_ID', auth() -> user() -> user_id) -> get();
        return view('/agents/doc_management/transactions/listings/listings_all', compact('listings'));
    }

    public function get_details_header(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', ResourceItems::SellerResourceId()) -> get();
        return view('/agents/doc_management/transactions/listings/details/listing_details_header', compact('listing', 'sellers'));
    }

    public function listing(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $resource_items = new ResourceItems();
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', $resource_items -> SellerResourceId()) -> get();

        if($listing -> ExpirationDate != '' && $listing -> ExpirationDate != '0000-00-00') {
            return view('/agents/doc_management/transactions/listings/details/listing_details', compact('listing', 'sellers'));
        } else {
            return redirect('/agents/doc_management/transactions/listings/listing_required_details/' . $Listing_ID);
        }

    }

}
