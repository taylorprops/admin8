<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Listings;

use App\Http\Controllers\Controller;
use App\Models\CRM\CRMContacts;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Listings\Checklists\ListingChecklistItems;
use App\Models\DocManagement\Transactions\Listings\Checklists\ListingChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Listings\Checklists\ListingChecklists;
use App\Models\DocManagement\Transactions\Listings\Documents\ListingDocuments;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
use App\Models\Employees\Agents;
use App\Models\Employees\Teams;
use App\Models\Resources\LocationData;
use Config;
use Illuminate\Http\Request;

class ListingDetailsController extends Controller {
    // End Checklist Tab

    public function add_member_html(Request $request) {
        $contact_types = ResourceItems::where('resource_type', 'contact_type') -> get();
        $states = LocationData::AllStates();
        return view('/agents/doc_management/transactions/listings/details/data/add_member_html', compact('contact_types', 'states'));
    }

    public function delete_member(Request $request) {

        if ($member = Members::find($request -> id) -> delete()) {

            $this -> update_sellers($request -> Listing_ID);

            return response() -> json([
                'status' => 'ok',
            ]);

        }

    }

// End Documents Tab

    // Checklist Tab
    public function get_checklist(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $checklist = ListingChecklists::where('Listing_ID', $Listing_ID) -> first();
        $items = ListingChecklistItems::where('Listing_ID', $Listing_ID) -> orderBy('checklist_item_order') -> get();
        $checklist_items = new ChecklistsItems();
        $checklist_docs = new ListingChecklistItemsDocs();

        return view('/agents/doc_management/transactions/listings/details/data/get_checklist', compact('checklist', 'items', 'checklist_items', 'checklist_docs'));
    }

// TABS
    // Details Tab
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

// End Members Tab

    // Documents Tab
    public function get_documents(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $documents = ListingDocuments::where('Listing_ID', $Listing_ID) -> get();

        return view('/agents/doc_management/transactions/listings/details/data/get_documents', compact('documents'));
    }

// End Details Tab

    // Members Tab
    public function get_members(Request $request) {
        $listing = Listings::find($request -> Listing_ID);
        $members = Members::where('Listing_ID', $request -> Listing_ID) -> get();
        $resource_items = new ResourceItems();
        $contact_types = $resource_items -> where('resource_type', 'contact_type') -> get();
        $states = LocationData::AllStates();
        $contacts = CRMContacts::where('Agent_ID', $listing -> Agent_ID) -> get();
        return view('/agents/doc_management/transactions/listings/details/data/get_members', compact('members', 'contact_types', 'resource_items', 'states', 'contacts'));
    }

    // Listing Details
    public function listing_details(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $resource_items = new ResourceItems();
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', $resource_items -> SellerResourceId()) -> get();

        if ($listing -> ExpirationDate != '' && $listing -> ExpirationDate != '0000-00-00') {
            return view('/agents/doc_management/transactions/listings/details/listing_details', compact('listing', 'sellers'));
        } else {
            return redirect('/agents/doc_management/transactions/listings/listing_required_details/' . $Listing_ID);
        }

    }

    // Listing Details Header
    public function listing_details_header(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        $resource_items = new ResourceItems();
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', $resource_items -> SellerResourceId()) -> get();
        $statuses = $resource_items -> where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();
        return view('/agents/doc_management/transactions/listings/details/listing_details_header', compact('listing', 'sellers', 'resource_items', 'statuses'));
    }

    // TEMP get all listings
    public function listings_all(Request $request) {
        $listings = Listings::where('Agent_ID', auth() -> user() -> user_id) -> get();
        return view('/agents/doc_management/transactions/listings/listings_all', compact('listings'));
    }

    public function mls_search(Request $request) {

        $listing_details = Listings::find($request -> Listing_ID);
        $mls_search_details = bright_mls_search($request -> ListingId);
        $mls_search_details = (object)$mls_search_details;

        // only if mls search produced results
        if (isset($mls_search_details -> ListingId)) {

            return response() -> json([
                'status' => 'ok',
                'county_match' => 'yes',
                'address' => $mls_search_details -> FullStreetAddress,
                'city' => $mls_search_details -> City,
                'state' => $mls_search_details -> StateOrProvince,
                'zip' => $mls_search_details -> PostalCode,
                'picture_url' => $mls_search_details -> ListPictureURL,
                'list_company' => $mls_search_details -> ListOfficeName,
            ]);
        }

        return response() -> json([
            'status' => 'not found',
        ]);
    }

    public function save_details(Request $request) {
        $listing = Listings::find($request -> Listing_ID);

        // mls needs to be verified. if not MLS_Verified needs to be set to no
        $listing -> MLS_Verified = 'no';

        if (bright_mls_search($request -> ListingId)) {
            $listing -> MLS_Verified = 'yes';
        }

        $data = $request -> all();

        $FullStreetAddress = $data['StreetNumber'] . ' ' . $data['StreetName'] . ' ' . $data['StreetSuffix'];

        if ($data['StreetDirSuffix']) {
            $FullStreetAddress .= ' ' . $data['StreetDirSuffix'];
        }

        if ($data['UnitNumber']) {
            $FullStreetAddress .= ' ' . $data['UnitNumber'];
        }

        $data['FullStreetAddress'] = $FullStreetAddress;

        foreach ($data as $col => $val) {

            if ($col != 'Listing_ID' && !stristr($col, '_submit')) {

                if ($col == 'ListPrice') {
                    $val = preg_replace('/[\$,]+/', '', $val);
                }

                $listing -> $col = $val;
            }

        }

        $listing -> save();
        return response() -> json([
            'status' => 'ok',
        ]);
    }

    public function save_member(Request $request) {

        if ($request -> id && $request -> id != 'undefined') {
            $member = Members::find($request -> id);
        } else {
            $member = new Members();
        }

        $data = $request -> all();

        foreach ($data as $col => $val) {

            if ($col != 'id') {
                $member -> $col = $val ?? null;
            }

        }

        $member -> save();

        $this -> update_sellers($request -> Listing_ID);

        return response() -> json([
            'status' => 'ok',
        ]);
    }

    public function save_mls_search(Request $request) {

        $listing_details = Listings::find($request -> Listing_ID);
        $mls_search_details = bright_mls_search($request -> ListingId);
        $mls_search_details = (object)$mls_search_details;
        $resource_items = new ResourceItems();
        $checklist = ListingChecklists::where('Listing_ID', $request -> Listing_ID) -> where('Agent_ID', $listing_details -> Agent_ID) -> first();
        $checklist_id = $checklist -> id;

        // set values
        $property_type_val = $mls_search_details -> PropertyType;
        $sale_rent = '';
        if($property_type_val) {
            if(stristr($property_type_val, 'lease')) {
                $sale_rent = 'rental';
                $property_type_val = str_replace(' Lease', '', $property_type_val);
            } else {
                $sale_rent = 'sale';
            }
        }
        $property_type_id = $resource_items -> GetResourceID($property_type_val, 'checklist_property_types');

        $property_sub_type = $mls_search_details -> SaleType;
        if($property_sub_type) {
            $end = strpos($property_sub_type, ',');
            if(!$end) {
                $end = strlen($property_sub_type);
            }
            $property_sub_type = trim(substr($property_sub_type, 0, $end));

            if(preg_match('/(hud|reo)/i', $property_sub_type)) {
                $property_sub_type = 'REO/Bank/HUD Owned';
            } else if(preg_match('/foreclosure/i', $property_sub_type)) {
                $property_sub_type = 'Foreclosure';
            } else if(preg_match('/auction/i', $property_sub_type)) {
                $property_sub_type = 'Auction';
            } else if(preg_match('/(short|third)/i', $property_sub_type)) {
                $property_sub_type = 'Short Sale';
            } else if(preg_match('/standard/i', $property_sub_type)) {
                $property_sub_type = 'Standard';
            } else {
                $property_sub_type = '';
            }
            // if no results check new construction
            if($property_sub_type == '') {
                if($mls_search_details -> NewConstructionYN == 'Y') {
                    $property_sub_type = 'New Construction';
                }
            }
        }
        $property_sub_type_id = $resource_items -> GetResourceID($property_sub_type, 'checklist_property_sub_types');

        $hoa_condo = 'none';
        $condo = $mls_search_details -> CondoYN ?? null;
        if($condo && $condo == 'Y') {
            $hoa_condo = 'condo';
        }
        $hoa = $mls_search_details -> AssociationYN ?? null;
        if($hoa && $hoa == 'Y') {
            if($mls_search_details -> AssociationFee > 0) {
                $hoa_condo = 'hoa';
            }
        }

        if($mls_search_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> StateOrProvince, 'checklist_locations');
        }

        ListingChecklists::CreateListingChecklist($checklist_id, $request -> Listing_ID, $listing_details -> Agent_ID, 'seller', 'listing', $property_type_id, $property_sub_type_id, $sale_rent, $mls_search_details -> StateOrProvince, $location_id);

        // get cols and vals for mls search
        foreach ($mls_search_details as $col => $val) {

            // if listing_details col matches then update it if it doesn't match original value
            if (isset($listing_details -> $col)) {
                if ($listing_details -> $col != $val && $val != '') {

                    // if a name field only replace if blank
                    if (in_array($listing_details -> $col, config('global.vars.select_columns_bright_agents'))) {
                        if ($val == '') {
                            $listing_details -> $col = $val;
                        }

                    } else {

                        if($col == 'PropertyType') {

                            $listing_details -> $col = $property_type_id;

                        } else if ($col == 'PropertySubType') {

                            $listing_details -> $col = $property_sub_type_id;

                        } else if ($col == 'County') {

                            $listing_details -> $col = $location_id;

                        } else if($col == 'HoaCondoFees') {

                            $listing_details -> $col = $hoa_condo;

                        } else {

                            $listing_details -> $col = $val;

                        }

                    }

                }

            }

        }

        $listing_details -> MLS_Verified = 'yes';
        $listing_details -> save();

        return response() -> json([
            'status' => 'ok',
        ]);

    }

    public function update_sellers($Listing_ID) {
        $sellers = Members::where('Listing_ID', $Listing_ID) -> where('member_type_id', ResourceItems::SellerResourceId()) -> get() -> toArray();
        $seller_two_first = $seller_two_last = '';
        $seller_one_first = $sellers[0]['first_name'];
        $seller_one_last = $sellers[0]['last_name'];
        if ($sellers[1]) {
            $seller_two_first = $sellers[1]['first_name'];
            $seller_two_last = $sellers[1]['last_name'];
        }

        $listing = Listings::find($Listing_ID);
        $listing -> SellerOneFirstName = $seller_one_first;
        $listing -> SellerOneLastName = $seller_one_last;
        $listing -> SellerTwoFirstName = $seller_two_first;
        $listing -> SellerTwoLastName = $seller_two_last;
        $listing -> save();
    }

    public function update_status(Request $request) {
        $Listing_ID = $request -> Listing_ID;
        $Status = $request -> Status;
        $listing = Listings::find($Listing_ID);
        $listing -> Status = $Status;
        $listing -> save();
    }

}
