<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Details;

use App\Http\Controllers\Controller;
use App\Mail\DefaultEmail;
use App\Mail\DocManagement\Emails\Documents;
use App\Models\Admin\Resources\ResourceItemsAdmin;
use App\Models\BrightMLS\AgentRoster;
use App\Models\CRM\CRMContacts;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Create\Fields\FieldInputs;
use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\DocManagement\Create\Upload\UploadPages;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;
use App\Models\DocManagement\Transactions\EditFiles\UserFields;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;
use App\Models\Employees\Agents;
use App\Models\Employees\Teams;
use App\Models\Resources\LocationData;
use App\User;
use Config;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TransactionsDetailsController extends Controller {


    // Transaction Details
    public function transaction_details(Request $request) {

        $transaction_type = strtolower($request -> type);
        $id = $request -> id;

        if ($transaction_type == 'listing') {
            $property = Listings::find($id);

            // if not all required details submitted require them
            if ($property -> ExpirationDate == '' || $property -> ExpirationDate == '0000-00-00') {
                return redirect('/agents/doc_management/transactions/add/transaction_required_details_listing/' . $id . '/listing');
            }

        } elseif ($transaction_type == 'contract') {
            $property = Contracts::find($id);

            // if not all required details submitted require them
            if ($property -> ContractDate == '' || $property -> ContractDate == '0000-00-00') {
                return redirect('/agents/doc_management/transactions/add/transaction_required_details_contract/' . $id . '/contract');
            }

        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($id);
        }

        // check if earnest and title questions are complete before allowing adding docs to the checklist
        $questions_confirmed = 'yes';

        if ($transaction_type == 'contract') {

            if ($property -> EarnestAmount == '' || $property -> EarnestHeldBy == '') {
                $questions_confirmed = 'no';
            }

            if ($property -> UsingHeritage == '' || ($property -> UsingHeritage == 'no' && $property -> TitleCompany == '')) {
                $questions_confirmed = 'no';
            }

        }

        return view('/agents/doc_management/transactions/details/transaction_details', compact('property', 'transaction_type', 'questions_confirmed'));

    }

    // Transaction Details Header
    public function transaction_details_header(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        if ($transaction_type == 'listing') {
            $property = Listings::find($Listing_ID);
        } elseif ($transaction_type == 'contract') {
            $property = Contracts::find($Contract_ID);
        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        }

        $resource_items = new ResourceItems();

        if ($transaction_type != 'referral') {
            $members = Members::where('Contract_ID', $Contract_ID) -> get();
            if ($transaction_type == 'listing') {
                $members = Members::where('Listing_ID', $Listing_ID) -> get();
            }
            $buyers = $members -> where('member_type_id', $resource_items -> BuyerResourceId());
            $sellers = $members -> where('member_type_id', $resource_items -> SellerResourceId());

            // get active contracts
            /* $status = ResourceItems::GetResourceID('Active', 'contract_status');
            $active_contracts_count = Contracts::where('Listing_ID', $Listing_ID) -> where('Status', $status) -> count(); */

        } else {
            $buyers = null;
            $sellers = null;
        }

        //$statuses = $resource_items -> where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/transaction_details_header', compact('transaction_type', 'property', 'buyers', 'sellers', 'resource_items'));
    }


    // TABS


    // Details Tab

    public function get_details(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = strtolower($request -> transaction_type);

        $list_agent = '';
        $property = Listings::find($Listing_ID);

        if ($transaction_type == 'contract') {
            $property = Contracts::find($Contract_ID);
            $list_agent = $property -> ListAgentFirstName . ' ' . $property -> ListAgentLastName;

            if ($property -> Listing_ID > 0) {
                $listing = Listings::find($property -> Listing_ID);
                $list_agent = $listing -> ListAgentFirstName . ' ' . $listing -> ListAgentLastName;
            }

        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        }

        $agents = Agents::where('active', 'yes') -> orderBy('last_name') -> get();
        $teams = Teams::where('active', 'yes') -> orderBy('team_name') -> get();
        $street_suffixes = config('global.vars.street_suffixes');
        $street_dir_suffixes = config('global.vars.street_dir_suffixes');
        $states_active = config('global.vars.active_states');
        $states = LocationData::AllStates();

        $property_state = $property -> StateOrProvince;
        $counties = LocationData::CountiesByState($property_state);
        $trans_coords = TransactionCoordinators::where('active', 'yes') -> orderBy('last_name') -> get();

        $has_listing = false;

        if ($transaction_type == 'contract' && $property -> Listing_ID > 0) {
            $has_listing = true;
        }

        return view('/agents/doc_management/transactions/details/data/get_details', compact('transaction_type', 'property', 'list_agent', 'agents', 'teams', 'street_suffixes', 'street_dir_suffixes', 'states_active', 'states', 'counties', 'trans_coords', 'has_listing'));
    }

    public function mls_search(Request $request) {

        //$listing_details = Listings::find($request -> Listing_ID);
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

    public function save_mls_search(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $MLS_ID = $request -> ListingId;
        $transaction_type = strtolower($request -> transaction_type);

        $represent = ($Listing_ID > 0 ? 'seller' : 'buyer');

        if ($transaction_type == 'listing') {
            $property_details = Listings::find($Listing_ID);
        } else {
            $property_details = Contracts::find($Contract_ID);
        }

        $mls_search_details = bright_mls_search($MLS_ID);
        $mls_search_details = (object)$mls_search_details;

        $resource_items = new ResourceItems();

        $checklist = TransactionChecklists::where('Agent_ID', $property_details -> Agent_ID);

        if ($transaction_type == 'listing') {
            $checklist = $checklist -> where('Listing_ID', $Listing_ID) -> first();
        } else {
            $checklist = $checklist -> where('Contract_ID', $Contract_ID) -> first();
        }

        $checklist_id = $checklist -> id;

        // set values
        $property_type_val = $mls_search_details -> PropertyType;
        $sale_rent = '';

        if ($property_type_val) {

            if (stristr($property_type_val, 'lease')) {
                $sale_rent = 'rental';
                $property_type_val = str_replace(' Lease', '', $property_type_val);
            } else {
                $sale_rent = 'sale';
            }

        }

        $property_type_id = $resource_items -> GetResourceID($property_type_val, 'checklist_property_types');

        $property_sub_type = $mls_search_details -> SaleType;

        if ($property_sub_type) {
            $end = strpos($property_sub_type, ',');

            if (!$end) {
                $end = strlen($property_sub_type);
            }

            $property_sub_type = trim(substr($property_sub_type, 0, $end));

            if (preg_match('/(hud|reo)/i', $property_sub_type)) {
                $property_sub_type = 'REO/Bank/HUD Owned';
            } elseif (preg_match('/foreclosure/i', $property_sub_type)) {
                $property_sub_type = 'Foreclosure';
            } elseif (preg_match('/auction/i', $property_sub_type)) {
                $property_sub_type = 'Auction';
            } elseif (preg_match('/(short|third)/i', $property_sub_type)) {
                $property_sub_type = 'Short Sale';
            } elseif (preg_match('/standard/i', $property_sub_type)) {
                $property_sub_type = 'Standard';
            } else {
                $property_sub_type = '';
            }

            // if no results check new construction
            if ($property_sub_type == '') {
                if ($mls_search_details -> NewConstructionYN == 'Y') {
                    $property_sub_type = 'New Construction';
                }

            }

        }

        $property_sub_type_id = $resource_items -> GetResourceID($property_sub_type, 'checklist_property_sub_types');

        $hoa_condo = 'none';
        $condo = $mls_search_details -> CondoYN ?? null;
        if ($condo && $condo == 'Y') {
            $hoa_condo = 'condo';
        }

        $hoa = $mls_search_details -> AssociationYN ?? null;
        if ($hoa && $hoa == 'Y') {
            if ($mls_search_details -> AssociationFee > 0) {
                $hoa_condo = 'hoa';
            }

        }

        if ($mls_search_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> StateOrProvince, 'checklist_locations');
        }

        $year_built = $mls_search_details -> YearBuilt;

        TransactionChecklists::CreateTransactionChecklist($checklist_id, $Listing_ID, $Contract_ID, '', $property_details -> Agent_ID, $represent, $transaction_type, $property_type_id, $property_sub_type_id, $sale_rent, $mls_search_details -> StateOrProvince, $location_id, $hoa_condo, $year_built);

        $property_details -> ListingId = $request -> ListingId;

        // get cols and vals for mls search
        foreach ($mls_search_details as $col => $val) {

            // if property_details col matches then update it if it doesn't match original value
            if (isset($property_details -> $col)) {
                if ($property_details -> $col != $val && $val != '') {
                    // if a name field only replace if blank
                    if (in_array($property_details -> $col, config('global.vars.select_columns_bright_agents'))) {
                        if ($val == '') {
                            $property_details -> $col = $val;
                        }
                    } else {
                        if ($col == 'PropertyType') {
                            $property_details -> $col = $property_type_id;
                        } elseif ($col == 'PropertySubType') {
                            $property_details -> $col = $property_sub_type_id;
                        } elseif ($col == 'County') {
                            $property_details -> $col = $location_id;
                        } elseif ($col == 'HoaCondoFees') {
                            $property_details -> $col = $hoa_condo;
                        } else {
                            $property_details -> $col = $val;
                        }
                    }
                }
            }
        }

        $property_details -> MLS_Verified = 'yes';
        $property_details -> save();

        return response() -> json([
            'status' => 'ok',
        ]);

    }

    public function save_details(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = strtolower($request -> transaction_type);
        $has_listing = false;

        if ($transaction_type == 'listing') {
            $property = Listings::find($Listing_ID);
        } elseif ($transaction_type == 'contract') {
            $property = Contracts::find($Contract_ID);

            if ($property -> Listing_ID > 0) {
                $has_listing = true;
                $property_listing = Listings::find($property -> Listing_ID);
            }

        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        }

        if ($transaction_type != 'referral') {

            // mls needs to be verified. if not MLS_Verified needs to be set to no
            $property -> MLS_Verified = 'no';

            if ($has_listing) {
                $property_listing -> MLS_Verified = 'no';
            }

            if (bright_mls_search($request -> ListingId)) {
                $property -> MLS_Verified = 'yes';

                if ($has_listing) {
                    $property_listing -> MLS_Verified = 'yes';
                }

            }

        }

        $FullStreetAddress = $request -> StreetNumber . ' ' . $request -> StreetName . ' ' . $request -> StreetSuffix;

        if ($request -> StreetDirSuffix) {
            $FullStreetAddress .= ' ' . $request -> StreetDirSuffix;
        }

        if ($request -> UnitNumber) {
            $FullStreetAddress .= ' ' . $request -> UnitNumber;
        }

        $request -> FullStreetAddress = $FullStreetAddress;

        foreach ($request -> all() as $col => $val) {
            $ignore_cols = ['Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type'];
            if (!in_array($col, $ignore_cols) && !stristr($col, '_submit')) {
                if (preg_match('/\$/', $val)) {
                    $val = preg_replace('/[\$,]+/', '', $val);
                }
                $property -> $col = $val;
                if ($has_listing) {
                    if (!in_array($col, Contracts::ContractColumnsNotInListings())) {
                        $property_listing -> $col = $val;
                    }
                }
            }
        }

        $property -> save();
        if ($has_listing) {
            $property_listing -> save();
        }

        return response() -> json([
            'status' => 'ok',
        ]);
    }

    public function save_required_fields(Request $request) {

        $Contract_ID = $request -> Contract_ID;
        $property = Contracts::find($Contract_ID);
        $property -> UsingHeritage = $request -> required_fields_using_heritage;
        $property -> TitleCompany = $request -> required_fields_title_company;
        $property -> EarnestAmount = preg_replace('/[\$,]+/', '', $request -> required_fields_earnest_amount);
        $property -> EarnestHeldBy = $request -> required_fields_earnest_held_by;
        $property -> save();

        return true;

    }

    // End Details Tab


    // Members Tab

    public function get_members(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;

        $members = Members::where('Listing_ID', $Listing_ID) -> get();
        $transaction_type = 'listing';

        if ($Contract_ID > 0) {
            $members = Members::where('Contract_ID', $Contract_ID) -> get();
            $transaction_type = 'contract';
        }

        $resource_items = new ResourceItems();

        $checklist_types = ['listing', 'both'];

        if ($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $contact_types = $resource_items -> where('resource_type', 'contact_type') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        $states = LocationData::AllStates();
        $contacts = CRMContacts::where('Agent_ID', $Agent_ID) -> get();

        return view('/agents/doc_management/transactions/details/data/get_members', compact('members', 'contact_types', 'resource_items', 'states', 'contacts'));

    }

    public function add_member_html(Request $request) {
        $transaction_type = $request -> transaction_type;

        $checklist_types = ['listing', 'both'];

        if ($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $contact_types = ResourceItems::where('resource_type', 'contact_type') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        $states = LocationData::AllStates();

        return view('/agents/doc_management/transactions/details/data/add_member_html', compact('contact_types', 'states'));

    }

    public function delete_member(Request $request) {

        if ($member = Members::find($request -> id) -> delete()) {

            if ($request -> transaction_type == 'listing') {
                $this -> update_transaction_members($request -> Listing_ID, 'listing');
            } else {
                $this -> update_transaction_members($request -> Contract_ID, 'contract');
            }

            return response() -> json([
                'status' => 'ok',
            ]);

        }

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

        if ($request -> transaction_type == 'listing') {
            $this -> update_transaction_members($request -> Listing_ID, 'listing');
        } else {
            $this -> update_transaction_members($request -> Contract_ID, 'contract');
        }

        return response() -> json([
            'status' => 'ok',
        ]);
    }

    public function update_transaction_members($id, $type) {

        $field = 'Listing_ID';

        if ($type == 'contract') {
            $field = 'Contract_ID';
        }

        $property = Listings::find($id);

        if ($type == 'contract') {
            $property = Contracts::find($id);
        }

        $sellers = Members::where($field, $id) -> where('member_type_id', ResourceItems::SellerResourceId());

        if ($sellers -> count() > 0) {
            $seller_two_first = $seller_two_last = '';
            $seller_one_first = $sellers -> first() -> first_name;
            $seller_one_last = $sellers -> first() -> last_name;

            if ($sellers -> take(1) -> first()) {
                $seller_two_first = $sellers -> take(1) -> first() -> first_name;
                $seller_two_last = $sellers -> take(1) -> first() -> last_name;
            }

            $property -> SellerOneFirstName = $seller_one_first;
            $property -> SellerOneLastName = $seller_one_last;
            $property -> SellerTwoFirstName = $seller_two_first;
            $property -> SellerTwoLastName = $seller_two_last;
        }

        $buyers = Members::where($field, $id) -> where('member_type_id', ResourceItems::BuyerResourceId());

        if ($buyers -> count() > 0) {
            $buyer_two_first = $buyer_two_last = '';
            $buyer_one_first = $buyers -> first() -> first_name;
            $buyer_one_last = $buyers -> first() -> last_name;

            if ($buyers -> take(1) -> first()) {
                $buyer_two_first = $buyers -> take(1) -> first() -> first_name;
                $buyer_two_last = $buyers -> take(1) -> first() -> last_name;
            }

            $property -> BuyerOneFirstName = $buyer_one_first;
            $property -> BuyerOneLastName = $buyer_one_last;
            $property -> BuyerTwoFirstName = $buyer_two_first;
            $property -> BuyerTwoLastName = $buyer_two_last;
        }

        $buyer_agent = Members::where($field, $id) -> where('member_type_id', ResourceItems::BuyerAgentResourceId()) -> first();

        if ($buyer_agent) {
            $property -> BuyerAgentFirstName = $buyer_agent -> first_name;
            $property -> BuyerAgentLastName = $buyer_agent -> last_name;
            $property -> BuyerOfficeName = $buyer_agent -> company;
            $property -> BuyerAgentEmail = $buyer_agent -> email;
            $property -> BuyerAgentPreferredPhone = $buyer_agent -> cell_phone;
        }

        $list_agent = Members::where($field, $id) -> where('member_type_id', ResourceItems::ListingAgentResourceId()) -> first();

        if ($list_agent) {
            $property -> ListAgentFirstName = $list_agent -> first_name;
            $property -> ListAgentLastName = $list_agent -> last_name;
            $property -> ListOfficeName = $list_agent -> company;
            $property -> ListAgentEmail = $list_agent -> email;
            $property -> ListAgentPreferredPhone = $list_agent -> cell_phone;
        }

        $property -> save();

    }

    // End Members Tab


    // Documents Tab

    public function get_documents(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;
        $contracts = [];

        if ($transaction_type == 'listing') {

            $property = Listings::find($Listing_ID);
            $field = 'Listing_ID';
            $id = $Listing_ID;
            $member_type_id = ResourceItems::SellerResourceId();
            $active_status_id = ResourceItems::GetResourceID('Active', 'contract_status');
            $contracts = Contracts::where('Listing_ID', $Listing_ID) -> where('Status', $active_status_id) -> pluck('Contract_ID');

            if (count($contracts) > 0) {
                $Contract_ID = $contracts[0];
            }

        } elseif ($transaction_type == 'contract') {

            $property = Contracts::find($Contract_ID);
            $field = 'Contract_ID';
            $id = $Contract_ID;
            $member_type_id = ResourceItems::BuyerResourceId();

        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }

        $members = null;

        if ($transaction_type != 'referral') {
            $member_type_id = Members::GetMemberTypeID('Buyer');

            if ($Listing_ID > 0) {
                $member_type_id = Members::GetMemberTypeID('Seller');
            }

            $members = Members::where($field, $id) -> where('member_type_id', $member_type_id) -> get();
        }

        // if our listing and contract include listing folders with contract
        if (($property -> Contract_ID > 0 && $property -> Listing_ID > 0) || count($contracts) > 0) {

            $folders = TransactionDocumentsFolders::where('Agent_ID', $Agent_ID) -> where(function ($query) use ($Listing_ID, $Contract_ID) {
                $query -> where('Contract_ID', $Contract_ID) -> orWhere('Listing_ID', $Listing_ID);
            })
            -> orderBy('order') -> get();

            $documents = TransactionDocuments::where('Agent_ID', $Agent_ID) -> where(function ($query) use ($Listing_ID, $Contract_ID) {
                $query -> where('Contract_ID', $Contract_ID) -> orWhere('Listing_ID', $Listing_ID);
            })
            -> orderBy('order') -> orderBy('created_at', 'DESC') -> get();

        } else {

            $folders = TransactionDocumentsFolders::where($field, $id) -> where('Agent_ID', $Agent_ID) -> orderBy('order') -> get();
            $documents = TransactionDocuments::where($field, $id) -> where('Agent_ID', $Agent_ID) -> orderBy('order') -> orderBy('created_at', 'DESC') -> get();

        }

        $transaction_checklist = TransactionChecklists::where($field, $id) -> first();
        $checklist_id = $transaction_checklist -> id;

        $checklist_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> get();

        $checklist_items_required = $checklist_items -> where('checklist_item_required', 'yes') -> sortBy('checklist_item_order');
        $checklist_items_if_applicable = $checklist_items -> where('checklist_item_required', 'no') -> sortBy('checklist_item_order');

        $available_files = new Upload();

        $resource_items = new ResourceItems();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> where('resource_association', 'yes') -> orderBy('resource_order') -> get();
        $form_categories = $resource_items -> where('resource_type', 'form_categories') -> orderBy('resource_order') -> get();

        $property_email = $property -> PropertyEmail;

        return view('/agents/doc_management/transactions/details/data/get_documents', compact('transaction_type', 'property', 'Agent_ID', 'Listing_ID', 'Contract_ID', 'members', 'checklist_id', 'documents', 'folders', 'checklist_items_required', 'checklist_items_if_applicable', 'available_files', 'resource_items', 'form_groups', 'form_categories', 'property_email'));
    }

    public function add_folder(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $folder_name = $request -> folder;

        if ($transaction_type == 'listing') {
            $order = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID);
        } elseif ($transaction_type == 'contract') {
            $order = TransactionDocumentsFolders::where('Contract_ID', $Contract_ID);
        } elseif ($transaction_type == 'referral') {
            $order = TransactionDocumentsFolders::where('Referral_ID', $Referral_ID);
        }

        $order = $order -> where('Agent_ID', $Agent_ID) -> where('folder_name', '!=', 'Trash') -> max('order');

        $order += 1;
        $folder = new TransactionDocumentsFolders();
        $folder -> folder_name = $folder_name;
        $folder -> order = $order;
        $folder -> Listing_ID = $Listing_ID ?? 0;
        $folder -> Contract_ID = $Contract_ID ?? 0;
        $folder -> Referral_ID = $Referral_ID ?? 0;
        $folder -> Agent_ID = $Agent_ID;
        $folder -> save();
    }

    public function delete_folder(Request $request) {

        $folder_id = $request -> folder_id;
        $transaction_type = strtolower($request -> transaction_type);
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        if ($transaction_type == 'listing') {
            $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('folder_name', 'Trash') -> first();
        } elseif ($transaction_type == 'contract') {
            $trash_folder = TransactionDocumentsFolders::where('Contract_ID', $Contract_ID) -> where('folder_name', 'Trash') -> first();
        } elseif ($transaction_type == 'referral') {
            $trash_folder = TransactionDocumentsFolders::where('Referral_ID', $Referral_ID) -> where('folder_name', 'Trash') -> first();
        }

        $move_documents_to_trash = TransactionDocuments::where('folder', $folder_id) -> update(['folder' => $trash_folder -> id]);
        $delete_folder = TransactionDocumentsFolders::where('id', $folder_id) -> delete();
    }

    public function duplicate_document(Request $request) {

        $document_id = $request -> document_id;
        $file_type = $request -> file_type;
        // get document details
        $document = TransactionDocuments::where('id', $document_id) -> first();

        $orig_upload_id = $document -> file_id;
        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $document -> Agent_ID;

        // copy to documents
        $document_copy = $document -> replicate();
        $document_copy -> save();
        $new_document_id = $document_copy -> id;

        // copy to documents images
        $document_images = TransactionDocumentsImages::where('document_id', $document_id) -> get();

        foreach ($document_images as $document_image) {
            $document_images_copy = $document_image -> replicate();
            $document_images_copy -> document_id = $new_document_id;
            $document_images_copy -> save();
        }

        $upload = TransactionUpload::where('file_id', $orig_upload_id) -> first();

        // create new upload
        $upload_copy = $upload -> replicate();
        $upload_copy -> Transaction_Docs_ID = $new_document_id;
        $upload_copy -> file_name_display = $upload -> file_name_display;
        $upload_copy -> Agent_ID = $Agent_ID;
        $upload_copy -> Listing_ID = $Listing_ID;
        $upload_copy -> Contract_ID = $Contract_ID;
        $upload_copy -> Referral_ID = $Referral_ID;
        $upload_copy -> save();
        $new_upload_id = $upload_copy -> file_id;

        if ($transaction_type == 'contract') {
            $path = 'contracts/' . $Contract_ID;
        } elseif ($transaction_type == 'listing') {
            $path = 'listings/' . $Listing_ID;
        } elseif ($transaction_type == 'referral') {
            $path = 'referrals/' . $Referral_ID;
        }

        $orig_uploads_path = 'doc_management/transactions/' . $path . '/' . $orig_upload_id . '_' . $file_type;
        $new_uploads_path = 'doc_management/transactions/' . $path . '/' . $new_upload_id . '_' . $file_type;

        // copy original file
        File::copyDirectory(Storage::disk('public') -> path($orig_uploads_path), Storage::disk('public') -> path($new_uploads_path));
        // add file_location to upload

        $upload_copy -> file_location = '/storage/' . $new_uploads_path . '/' . $upload -> file_name;
        $upload_copy -> save();

        // add file location to doc images
        $document_images = TransactionDocumentsImages::where('document_id', $new_document_id) -> get();

        foreach ($document_images as $document_image) {
            $new_file_location = str_replace($orig_upload_id . '_' . $file_type, $new_upload_id . '_' . $file_type, $document_image -> file_location);
            $document_image -> file_location = $new_file_location;
            $document_image -> save();
        }

        // add other details to docs
        $document_copy -> file_location = '/storage/' . $new_uploads_path . '/' . $upload -> file_name;
        $document_copy -> file_location_converted = '/storage/' . $new_uploads_path . '/converted/' . $upload -> file_name;
        $document_copy -> file_name_display = $upload -> file_name_display . '-COPY';
        $document_copy -> file_id = $new_upload_id;
        //$document_copy -> order = $document_copy -> order + 1;
        $document_copy -> assigned = 'no';
        $document_copy -> save();

        $new_document_id = $document_copy -> id;

        // update uploads with new doc id
        $upload_copy -> Transaction_Docs_ID = $new_document_id;
        $upload_copy -> save();

        // copy all pages, images, fields and field values
        $data_sets = [TransactionUploadImages::where('file_id', $orig_upload_id) -> get(), TransactionUploadPages::where('file_id', $orig_upload_id) -> get()];

        foreach ($data_sets as $data_set) {

            foreach ($data_set as $row) {
                $copy = $row -> replicate();
                $copy -> file_id = $new_upload_id;
                $path = str_replace('/' . $orig_upload_id . '/', '/' . $new_upload_id . '_' . $file_type . '/', $row -> file_location);
                $copy -> file_location = $path;
                $copy -> save();
            }

        }

        $user_fields = UserFields::where('file_id', $orig_upload_id) -> get();

        foreach ($user_fields as $user_field) {
            $copy = $user_field -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> save();
        }

        $user_fields_inputs = UserFieldsInputs::where('file_id', $orig_upload_id) -> get();

        foreach ($user_fields_inputs as $user_fields_input) {
            $copy = $user_fields_input -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> save();
        }

        // add input values
        $field_input_values = UserFieldsValues::where('file_id', $orig_upload_id) -> get();

        foreach ($field_input_values as $field_input_value) {
            $copy = $field_input_value -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> file_type = $file_type;
            $copy -> Agent_ID = $Agent_ID;
            $copy -> Listing_ID = $Listing_ID;
            $copy -> Contract_ID = $Contract_ID;
            $copy -> Referral_ID = $Referral_ID;
            $copy -> save();
        }

    }

    public function email_get_documents(Request $request) {

        $docs_type = $request -> docs_type;

        $filenames = [];
        $file_locations = [];

        if ($docs_type != '') {

            $file = $this -> merge_documents($request);

            // when multiple docs are emailed
            if ($docs_type == 'merged') {
                $file_locations[] = str_replace('/storage/', '', $file['file_location']);
                $filenames[] = $file['filename'];
            } elseif ($docs_type == 'single') {
                foreach ($file['single_documents'] as $doc) {
                    $file_locations[] = str_replace('/storage/', '', $doc -> file_location_converted);
                    $filenames[] = $doc -> file_name_display;
                }

            }

        } else {
            // when a single doc is emailed
            $doc = TransactionDocuments::where('id', $request -> document_ids) -> first();
            $file_locations[] = str_replace('/storage/', '', $doc -> file_location_converted);
            $filenames[] = $doc -> file_name_display;

        }

        return compact('file_locations', 'filenames');

    }

    public function get_split_document_html(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $checklist_id = $request -> checklist_id;
        $document_id = $request -> document_id;
        $document = TransactionDocuments::where('id', $document_id) -> first();
        $file_id = $document -> file_id;
        $file_type = $request -> file_type;
        $file_name = $request -> file_name;

        $document_images = TransactionUploadImages::where('file_id', $file_id) -> orderBy('page_number') -> get();

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_modal = new TransactionChecklistItems();
        $checklist_items = $transaction_checklist_items_modal -> where('checklist_id', $checklist_id) -> get();

        $transaction_checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_id', $checklist_id) -> get();

        $checklist_types = ['listing', 'both'];

        if ($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/get_split_document_html', compact('document_id', 'file_id', 'file_type', 'file_name', 'document', 'document_images', 'checklist_items', 'checklist_groups', 'transaction_checklist_item_documents', 'checklist_items_model', 'transaction_checklist_items_modal'));
    }

    public function merge_documents(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $folder_id = $request -> folder_id;
        $type = $request -> type;
        $docs_type = $request -> docs_type;

        if ($transaction_type == 'listing') {
            $property = Listings::where('Listing_ID', $Listing_ID) -> first();
        } elseif ($transaction_type == 'contract') {
            $property = Contracts::where('Contract_ID', $Contract_ID) -> first();
        } elseif ($transaction_type == 'referral') {
            $property = Referrals::where('Referral_ID', $Referral_ID) -> first();
        }

        // create filename for merged docs
        $filename = sanitize($property -> FullStreetAddress) . '_' . date('YmdHis') . '.pdf';

        $document_ids = explode(',', $request -> document_ids);
        $documents = [];

        foreach ($document_ids as $document_id) {

            if ($type == 'filled') {
                $documents[] = TransactionDocuments::where('id', $document_id) -> pluck('file_location_converted') -> first();
                $single_documents[] = TransactionDocuments::select('file_location_converted', 'file_name_display') -> where('id', $document_id) -> first();
            } elseif ($type == 'blank') {
                $documents[] = TransactionDocuments::where('id', $document_id) -> pluck('file_location') -> first();
            }

        }

        $docs_array = array_map(array($this, 'get_path'), $documents);
        $docs = implode(' ', $docs_array);

        $tmp = Storage::disk('public') -> path('tmp');
        exec('pdftk ' . $docs . ' cat output ' . $tmp . '/' . $filename);

        $file_location = '/storage/tmp/' . $filename;
        return compact('file_location', 'filename', 'single_documents');

    }

    public function move_documents_to_folder(Request $request) {

        $folder_id = $request -> folder_id;
        $document_ids = explode(',', $request -> document_ids);
        $update_folder = TransactionDocuments::whereIn('id', $document_ids) -> update(['folder' => $folder_id]);

    }

    public function move_documents_to_trash(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        if ($transaction_type == 'listing') {
            $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID);
        } elseif ($transaction_type == 'contract') {
            $trash_folder = TransactionDocumentsFolders::where('Contract_ID', $Contract_ID);
        } elseif ($transaction_type == 'referral') {
            $trash_folder = TransactionDocumentsFolders::where('Referral_ID', $Referral_ID);
        }

        $trash_folder = $trash_folder -> where('folder_name', 'Trash') -> first();

        $document_ids = explode(',', $request -> document_ids);
        $update_folder = TransactionDocuments::whereIn('id', $document_ids) -> update(['folder' => $trash_folder -> id]);
    }

    public function reorder_documents(Request $request) {
        $data = json_decode($request['data'], true);
        $data = $data['document'];

        foreach ($data as $item) {
            $document_id = $item['document_id'];
            $folder = $item['folder_id'];
            $document_order = $item['document_index'];
            $reorder = TransactionDocuments::where('id', $document_id) -> first();
            $reorder -> order = $document_order;
            $reorder -> folder = $folder;
            $reorder -> save();
        }

        return true;

    }

    public function save_add_template_documents(Request $request) {
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $folder = $request -> folder;

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();

        $files = json_decode($request['files'], true);

        foreach ($files as $file) {

            $file_id = $file['file_id'];
            $add_documents = new TransactionDocuments();
            $add_documents -> Agent_ID = $Agent_ID;

            if ($transaction_type == 'contract') {
                $add_documents -> Contract_ID = $Contract_ID;
            } elseif ($transaction_type == 'listing') {
                $add_documents -> Listing_ID = $Listing_ID;
            } elseif ($transaction_type == 'referral') {
                $add_documents -> Referral_ID = $Referral_ID;
            }

            $add_documents -> folder = $folder;
            $add_documents -> order = $file['order'];
            $add_documents -> orig_file_id = $file_id;
            $add_documents -> file_type = 'system';
            $add_documents -> file_name = $file['file_name'];
            $add_documents -> file_name_display = $file['file_name_display'];
            $add_documents -> pages_total = $file['pages_total'];
            $add_documents -> file_location = $file['file_location'];
            $add_documents -> transaction_type = $transaction_type;
            $add_documents -> save();

            $new_document_id = $add_documents -> id;

            $upload = Upload::where('file_id', $file_id) -> first();

            // create new upload
            $upload_copy = $upload -> replicate();
            $upload_copy -> orig_file_id = $file_id;
            $upload_copy -> file_type = 'system';
            $upload_copy -> Transaction_Docs_ID = $new_document_id;
            $upload_copy -> file_name_display = $upload -> file_name_display;
            $upload_copy -> Agent_ID = $Agent_ID;
            $upload_copy -> Listing_ID = $Listing_ID;
            $upload_copy -> Contract_ID = $Contract_ID;
            $upload_copy -> Referral_ID = $Referral_ID;
            $upload_new = $upload_copy -> toArray();
            $upload_new = TransactionUpload::create($upload_new);

            $new_file_id = $upload_new -> file_id;

            // update file_id in docs
            $add_documents -> file_id = $new_file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path . '/storage/app/public/';

            if ($transaction_type == 'listing') {
                $path = 'listings/' . $Listing_ID;
            } elseif ($transaction_type == 'contract') {
                $path = 'contracts/' . $Contract_ID;
            } else {
                $path = 'referrals/' . $Referral_ID;
            }

            $files_path = 'doc_management/transactions/' . $path . '/' . $new_file_id;

            $copy_from = $storage_path . 'doc_management/uploads/' . $file_id . '/*';
            $copy_to = $storage_path . $files_path . '_system';
            Storage::disk('public') -> makeDirectory($files_path . '_system/converted');
            Storage::disk('public') -> makeDirectory($files_path . '_system/converted_images');
            Storage::disk('public') -> makeDirectory($files_path . '_system/layers');
            Storage::disk('public') -> makeDirectory($files_path . '_system/combined');

            $copy = exec('cp -rp ' . $copy_from . ' ' . $copy_to);
            $copy_converted = exec('cp ' . $storage_path . $files_path . '_system/' . $file['file_name'] . ' ' . $copy_to . '/converted/' . $file['file_name']);

            $filename = $file['file_name'];
            $image_filename = str_replace('.pdf', '.jpg', $file['file_name']);
            $source = $copy_to . '/converted/' . $filename;
            $destination = $copy_to . '/converted_images';
            $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $new_document_id);

            $add_documents -> file_location = '/storage/' . $files_path . '_system/' . $filename;
            $add_documents -> file_location_converted = '/storage/' . $files_path . '_system/converted/' . $filename;
            $add_documents -> save();

            $upload_images = UploadImages::where('file_id', $file_id) -> get();
            $upload_pages = UploadPages::where('file_id', $file_id) -> get();

            foreach ($upload_images as $upload_image) {
                $copy = $upload_image -> replicate();
                $copy -> file_id = $new_file_id;
                $new_path = str_replace('/uploads/' . $file_id . '/', '/transactions/' . $path . '/' . $new_file_id . '_system/', $upload_image -> file_location);
                $copy -> file_location = $new_path;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> Contract_ID = $Contract_ID;
                $copy -> Referral_ID = $Referral_ID;
                $new = $copy -> toArray();
                TransactionUploadImages::create($new);
            }

            foreach ($upload_pages as $upload_page) {
                $copy = $upload_page -> replicate();
                $copy -> file_id = $new_file_id;
                $new_path = str_replace('/uploads/' . $file_id . '/', '/transactions/' . $path . '/' . $new_file_id . '_user/', $upload_page -> file_location);
                $copy -> file_location = $new_path;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> Contract_ID = $Contract_ID;
                $copy -> Referral_ID = $Referral_ID;
                $new = $copy -> toArray();
                TransactionUploadPages::create($new);
            }

            $fields = Fields::where('file_id', $file_id) -> get();
            $field_inputs = FieldInputs::where('file_id', $file_id) -> get();

            foreach ($fields as $field) {
                $copy = $field -> replicate();
                $copy -> file_id = $new_file_id;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> Contract_ID = $Contract_ID;
                $copy -> Referral_ID = $Referral_ID;
                $copy -> file_type = 'system';
                $copy -> field_inputs = 'yes';
                $new = $copy -> toArray();
                UserFields::create($new);
            }

            foreach ($field_inputs as $field_input) {
                $copy = $field_input -> replicate();
                $copy -> file_id = $new_file_id;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> Contract_ID = $Contract_ID;
                $copy -> Referral_ID = $Referral_ID;
                $copy -> file_type = 'system';
                $new = $copy -> toArray();
                UserFieldsInputs::create($new);
            }

        }

    }

    public function save_assign_documents_to_checklist(Request $request) {

        $checklist_items = json_decode($request['checklist_items']);
        $checklist_id = $request -> checklist_id;
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        foreach ($checklist_items as $checklist_item) {

            $checklist_item_id = $checklist_item -> checklist_item_id;
            $document_ids = collect($checklist_item -> document_ids);

            foreach ($document_ids as $document_id) {

                $add_checklist_item_doc = new TransactionChecklistItemsDocs();
                $add_checklist_item_doc -> document_id = $document_id;
                $add_checklist_item_doc -> checklist_id = $checklist_id;
                $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
                $add_checklist_item_doc -> Agent_ID = $Agent_ID;

                if ($transaction_type == 'listing') {
                    $add_checklist_item_doc -> Listing_ID = $Listing_ID;
                } elseif ($transaction_type == 'contract') {
                    $add_checklist_item_doc -> Contract_ID = $Contract_ID;
                } elseif ($transaction_type == 'referral') {
                    $add_checklist_item_doc -> Referral_ID = $Referral_ID;
                }

                $add_checklist_item_doc -> save();

                $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
                $update_checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> update(['checklist_item_status' => 'not_reviewed']);

            }

        }

    }

    public function save_rename_document(Request $request) {

        $new_name = $request -> new_name;
        $document_id = $request -> document_id;
        $document = TransactionDocuments::where('id', $document_id) -> first();

        $file_name = sanitize(str_replace('.pdf', '', $new_name)) . '.pdf';
        $file_name_display = str_replace('.pdf', '', $new_name) . '.pdf';
        $file_location = str_replace($document -> file_name, $file_name, $document -> file_location);
        $file_location_converted = str_replace($document -> file_name, $file_name, $document -> file_location_converted);

        File::move($this -> get_path($document -> file_location), $this -> get_path($file_location));
        File::move($this -> get_path($document -> file_location_converted), $this -> get_path($file_location_converted));

        $transaction_upload = TransactionUpload::where('Transaction_Docs_ID', $document_id)
            -> update([
                'file_name_display' => $new_name,
                'file_name_display' => $file_name_display,
                'file_location' => $file_location,
                'file_name' => $file_name,
            ]);

        $transaction_document = TransactionDocuments::where('id', $document_id) -> update(['file_name_display' => $new_name]);

        $document -> file_name = $file_name;
        $document -> file_name_display = $file_name_display;
        $document -> file_location = $file_location;
        $document -> file_location_converted = $file_location_converted;
        $document -> save();

        return true;
    }

    public function save_split_document(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;

        $folder_id = $request -> folder_id;
        $document_name = $request -> document_name;
        if (preg_match('/^[0-9]*$/', $document_name) && $document_name > 0) {
            $document_name = Upload::GetFormName($document_name);
        }

        $document_name = preg_replace('/\.pdf/', '', $document_name);

        $image_ids = explode(',', $request -> image_ids);
        $pages_total = count($image_ids);
        $file_type = $request -> file_type;
        $file_id = $request -> file_id;
        $checklist_item_id = $request -> checklist_item_id;
        $checklist_id = $request -> checklist_id;

        $document_images = TransactionUploadImages::whereIn('id', $image_ids) -> get();

        $document_image_files = [];
        $document_page_files = [];
        $page_numbers = [];

        foreach ($document_images as $document_image) {

            $doc_file_id = $document_image -> file_id;
            $doc_page_number = $document_image -> page_number;
            $page_numbers[] = $doc_page_number;

            $pages = [];
            $images = [];

            $document_page = TransactionUploadPages::where('file_id', $doc_file_id) -> where('page_number', $doc_page_number) -> first();
            $pages = ['file_id' => $document_page -> file_id, 'file_location' => $document_page -> file_location];
            $images = ['file_id' => $document_image -> file_id, 'file_location' => $document_image -> file_location, 'page_number' => $doc_page_number];

            array_push($document_page_files, $pages);
            array_push($document_image_files, $images);

        }

        // if manually saving to documents
        if ($document_name) {
            $file_name = sanitize($document_name) . '.pdf';
            $file_name_display = $document_name . '.pdf';

        // if adding to checklist item
        // assign to checklist item
        } else {
            $checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> first();
            $checklist_form_id = $checklist_item -> checklist_form_id;
            $file_name_display = Upload::GetFormName($checklist_form_id);
            $file_name = sanitize($file_name_display) . '.pdf';
        }

        // add to docs_transaction_docs
        $add_document = new TransactionDocuments();
        $add_document -> file_type = 'user';
        $add_document -> Agent_ID = $Agent_ID;
        $add_document -> Listing_ID = $Listing_ID;
        $add_document -> Contract_ID = $Contract_ID;
        $add_document -> Referral_ID = $Referral_ID;
        $add_document -> transaction_type = $transaction_type;
        $add_document -> folder = $folder_id;
        $add_document -> file_name = $file_name;
        $add_document -> file_name_display = $file_name_display;
        $add_document -> pages_total = $pages_total;
        $add_document -> save();
        $Transaction_Docs_ID = $add_document -> id;

        // add to transaction uploads
        $upload = new TransactionUpload();
        $upload -> Transaction_Docs_ID = $Transaction_Docs_ID;
        $upload -> Agent_ID = $Agent_ID;
        $upload -> Listing_ID = $Listing_ID;
        $upload -> Contract_ID = $Contract_ID;
        $upload -> Referral_ID = $Referral_ID;
        $upload -> file_name = $file_name;
        $upload -> file_name_display = $file_name_display;
        $upload -> pages_total = $pages_total;
        $upload -> save();
        $new_file_id = $upload -> file_id;

        $add_document -> file_id = $new_file_id;
        $add_document -> save();

        if ($transaction_type == 'contract') {
            $path = 'contracts/' . $Contract_ID;
        } elseif ($transaction_type == 'listing') {
            $path = 'listings/' . $Listing_ID;
        } elseif ($transaction_type == 'referral') {
            $path = 'referral/' . $Referral_ID;
        }

        $files_path = 'doc_management/transactions/' . $path . '/' . $new_file_id . '_user';

        Storage::disk('public') -> makeDirectory($files_path . '/images');
        Storage::disk('public') -> makeDirectory($files_path . '/pages');

        // copy images and pages and create merged file
        // copy images
        $page_number = 1;

        foreach ($document_image_files as $image_file) {
            $image_file_name = basename($image_file['file_location']);
            $old_file_loc = Storage::disk('public') -> path('doc_management/transactions/' . $path . '/' . $document_image_files[0]['file_id'] . '_' . $file_type . '/images/' . $image_file_name);
            $new_file_loc = Storage::disk('public') -> path($files_path . '/images/' . $image_file_name);
            exec('cp ' . $old_file_loc . ' ' . $new_file_loc);

            $upload_images = new TransactionUploadImages();
            $upload_images -> file_id = $new_file_id;
            $upload_images -> Agent_ID = $Agent_ID;
            $upload_images -> Listing_ID = $Listing_ID;
            $upload_images -> Contract_ID = $Contract_ID;
            $upload_images -> Referral_ID = $Referral_ID;
            $upload_images -> file_name = $file_name;
            $upload_images -> file_location = '/storage/' . $files_path . '_user/images/' . $image_file_name;
            $upload_images -> pages_total = count($document_image_files);
            $upload_images -> page_number = $page_number;
            $upload_images -> save();

            // copy from docs_transaction_fields ** update new page for each
            $add_user_fields = UserFields::where('file_id', $image_file['file_id']) -> where('page', $image_file['page_number']) -> get();
            $field_ids = [];

            foreach ($add_user_fields as $add_user_field) {
                $field_ids[] = $add_user_field -> field_id;
                $add_user_fields_copy = $add_user_field -> replicate();
                $add_user_fields_copy -> page = $page_number;
                $add_user_fields_copy -> file_type = 'user';
                $add_user_fields_copy -> file_id = $new_file_id;
                $add_user_fields_copy -> save();
            }

            $user_fields_inputs = UserFieldsInputs::where('file_id', $image_file['file_id']) -> get();

            foreach ($user_fields_inputs as $user_fields_input) {
                $add_user_fields_input_copy = $user_fields_input -> replicate();
                $add_user_fields_input_copy -> file_id = $new_file_id;
                $add_user_fields_input_copy -> file_type = 'user';
                $add_user_fields_input_copy -> save();
            }

            // copy from docs_transaction_fields_inputs_values
            $add_user_field_values = UserFieldsValues::whereIn('input_id', $field_ids) -> get();

            foreach ($add_user_field_values as $add_user_field_value) {
                $add_user_field_values_copy = $add_user_field_value -> replicate();
                $add_user_field_values_copy -> file_type = 'user';
                $add_user_field_values_copy -> file_id = $new_file_id;
                $add_user_field_values_copy -> save();
            }

            $page_number += 1;

        }

        // copy pages
        $page_number = 1;

        foreach ($document_page_files as $page_file) {
            $page_file_name = basename($page_file['file_location']);
            $old_file_loc = Storage::disk('public') -> path('doc_management/transactions/' . $path . '/' . $document_page_files[0]['file_id'] . '_' . $file_type . '/pages/' . $page_file_name);
            $new_file_loc = Storage::disk('public') -> path($files_path . '/pages/' . $page_file_name);
            exec('cp ' . $old_file_loc . ' ' . $new_file_loc);

            $upload_pages = new TransactionUploadPages();
            $upload_pages -> file_id = $new_file_id;
            $upload_pages -> Agent_ID = $Agent_ID;
            $upload_pages -> Listing_ID = $Listing_ID;
            $upload_pages -> Contract_ID = $Contract_ID;
            $upload_pages -> Referral_ID = $Referral_ID;
            $upload_pages -> file_name = $file_name;
            $upload_pages -> file_location = '/storage/' . $files_path . '/pages/' . $page_file_name;
            $upload_pages -> pages_total = count($document_page_files);
            $upload_pages -> page_number = $page_number;
            $upload_pages -> save();
            $page_number += 1;
        }

        //merge pages into main file and move to converted
        $main_file_location = $files_path . '/' . $file_name;
        $converted_file_location = $files_path . '/converted/' . $file_name;

        $base_path = base_path();
        exec('mkdir ' . $base_path . '/storage/app/public/' . $files_path . '/converted');
        exec('mkdir ' . $base_path . '/storage/app/public/' . $files_path . '/converted_images');

        // merge all pages and add to main directory and converted directory
        $pages = Storage::disk('public') -> path($files_path . '/pages');
        exec('pdftk ' . $pages . '/*.pdf cat output ' . $base_path . '/storage/app/public/' . $main_file_location);

        //exec('cd '.$base_path.'/storage/app/public/ && cp '.$main_file_location.' '.$converted_file_location);
        // get split pages, merge and add to converted
        $old_converted_location = Storage::disk('public') -> path('doc_management/transactions/' . $path . '/' . $file_id . '_' . $file_type . '/converted');
        $new_converted_location = Storage::disk('public') -> path($files_path . '/converted');
        $new_converted_images_location = Storage::disk('public') -> path($files_path . '/converted_images');

        exec('pdftk ' . $old_converted_location . '/*.pdf cat ' . implode(' ', $page_numbers) . ' output ' . $new_converted_location . '/' . $file_name);

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $image_filename = str_replace('.pdf', '.jpg', $file_name);
        $source = $new_converted_location . '/' . $file_name;
        $destination = $new_converted_images_location;
        $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $Transaction_Docs_ID);

        // update file locations in docs_transaction and docs uploads
        $add_document -> file_location = '/storage/' . $main_file_location;
        $add_document -> file_location_converted = '/storage/' . $converted_file_location;
        $add_document -> save();

        $upload -> file_location = '/storage/' . $main_file_location;
        $upload -> save();

        // add to checklist
        if ($checklist_id > 0) {
            $document_id = $Transaction_Docs_ID;

            $add_checklist_item_doc = new TransactionChecklistItemsDocs();
            $add_checklist_item_doc -> document_id = $document_id;
            $add_checklist_item_doc -> checklist_id = $checklist_id;
            $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
            $add_checklist_item_doc -> Agent_ID = $Agent_ID;
            if ($transaction_type == 'listing') {
                $add_checklist_item_doc -> Listing_ID = $Listing_ID;
            } elseif ($transaction_type == 'contract') {
                $add_checklist_item_doc -> Contract_ID = $Contract_ID;
            } elseif ($transaction_type == 'referral') {
                $add_checklist_item_doc -> Referral_ID = $Referral_ID;
            }

            $add_checklist_item_doc -> save();

            $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
            $update_checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> update(['checklist_item_status' => 'not_reviewed']);

        }

    }

    public function upload_documents(Request $request) {

        $file = $request -> file('file');
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = strtolower($request -> transaction_type);
        $folder = $request -> folder;

        if ($file) {

            $ext = $file -> getClientOriginalExtension();
            $file_name = $file -> getClientOriginalName();

            $file_name_remove_numbers = preg_replace('/[0-9-_\s\.]+\.' . $ext . '/', '.' . $ext, $file_name);
            $file_name_remove_numbers = preg_replace('/^[0-9-_\s\.]+/', '', $file_name_remove_numbers);
            $file_name_display = preg_replace('/-/', ' ', $file_name_remove_numbers);
            $file_name_no_ext = str_replace('.' . $ext, '', $file_name_remove_numbers);
            $clean_file_name = sanitize($file_name_no_ext);
            $new_file_name = $clean_file_name . '.' . $ext;

            // convert to pdf if image
            if ($ext != 'pdf') {
                $new_file_name = date('YmdHis') . '_' . $clean_file_name . '.pdf';
                $file_name_display = $file_name_no_ext . '.pdf';
                $create_images = exec('convert -quality 100 -density 300 -page letter ' . $file . ' /tmp/' . $new_file_name, $output, $return);
                $file = '/tmp/' . $new_file_name;
            }

            $pages_total = exec('pdftk ' . $file . ' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            // add to Documents
            $add_documents = new TransactionDocuments();
            $add_documents -> file_type = 'user';
            $add_documents -> Agent_ID = $Agent_ID;
            $add_documents -> Listing_ID = $Listing_ID;
            $add_documents -> Contract_ID = $Contract_ID;
            $add_documents -> Referral_ID = $Referral_ID;
            $add_documents -> transaction_type = $transaction_type;
            $add_documents -> folder = $folder;
            $add_documents -> file_name = $new_file_name;
            $add_documents -> file_name_display = $file_name_display;
            $add_documents -> pages_total = $pages_total;
            $add_documents -> order = 0;
            $add_documents -> save();
            $Transaction_Docs_ID = $add_documents -> id;

            // add original file to uploads
            $upload = new TransactionUpload();
            $upload -> Transaction_Docs_ID = $Transaction_Docs_ID;
            $upload -> Agent_ID = $Agent_ID;
            $upload -> Listing_ID = $Listing_ID;
            $upload -> Contract_ID = $Contract_ID;
            $upload -> Referral_ID = $Referral_ID;
            $upload -> file_name = $new_file_name;
            $upload -> file_name_display = $file_name_display;
            $upload -> file_type = 'user';
            $upload -> pages_total = $pages_total;
            $upload -> save();
            $file_id = $upload -> file_id;

            $add_documents -> file_id = $file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path . '/storage/app/public';

            $path = 'contracts/' . $Contract_ID;

            if ($transaction_type == 'listing') {
                $path = 'listings/' . $Listing_ID;
            } elseif ($transaction_type == 'referral') {
                $path = 'referrals/' . $Referral_ID;
            }

            $storage_dir = 'doc_management/transactions/' . $path . '/' . $file_id . '_user';
            $storage_public_path = '/storage/' . $storage_dir;
            $file_location = $storage_public_path . '/' . $new_file_name;

            if (!Storage::disk('public') -> put($storage_dir . '/' . $new_file_name, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Uploaded']);
                return ($fail);
            }

            Storage::disk('public') -> makeDirectory($storage_dir . '/converted');

            // flatten
            $file_in = Storage::disk('public') -> path($storage_dir . '/' . $new_file_name);
            $file_out = Storage::disk('public') -> path($storage_dir . '/temp_' . $new_file_name);
            exec('pdftk ' . $file_in . ' output ' . $file_out . ' flatten');
            exec('rm ' . $file_in . ' && mv ' . $file_out . ' ' . $file_in);

            // add to converted folder
            exec('cp ' . Storage::disk('public') -> path($storage_dir . '/' . $new_file_name) . ' ' . Storage::disk('public') -> path($storage_dir . '/converted/' . $new_file_name));

            Storage::disk('public') -> makeDirectory($storage_dir . '/converted_images');
            $checklist_item_docs_model = new TransactionChecklistItemsDocs();
            $source = $storage_path . '/' . $storage_dir . '/converted/' . $new_file_name;
            $image_file_name = str_replace('.pdf', '.jpg', $new_file_name);
            $destination = $storage_path . '/' . $storage_dir . '/converted_images';

            $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_file_name, $Transaction_Docs_ID);

            $storage_full_path = $storage_path . '/doc_management/transactions/' . $path . '/' . $file_id . '_user';
            chmod($storage_full_path . '/' . $new_file_name, 0775);

            // update directory path in database
            $upload -> file_location = $file_location;
            $upload -> save();

            // create directories
            $storage_dir_pages = $storage_dir . '/pages';
            Storage::disk('public') -> makeDirectory($storage_dir_pages);
            $storage_dir_images = $storage_dir . '/images';
            Storage::disk('public') -> makeDirectory($storage_dir_images);

            // split pdf into pages and images
            $input_file = $storage_full_path . '/' . $new_file_name;
            $output_files = $storage_path . '/' . $storage_dir_pages . '/page_%02d.pdf';
            $new_image_name = str_replace($ext, 'jpg', $new_file_name);
            $output_images = $storage_path . '/' . $storage_dir_images . '/' . $new_image_name;

            // add individual pages to pages directory
            $create_pages = exec('pdftk ' . $input_file . ' burst output ' . $output_files . ' flatten', $output, $return);
            // remove data file
            exec('rm ' . $storage_path . '/' . $storage_dir_pages . '/doc_data.txt');

            // add individual images to images directory
            $create_images = exec('convert -density 300 -quality 100 ' . $input_file . ' -background white -alpha remove -strip ' . $output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files('public/' . $storage_dir . '/images');
            $images_public_path = $storage_public_path . '/images';

            foreach ($saved_images_directory as $saved_image) {
                // get just file_name
                $images_file_name = basename($saved_image);
                $page_number = preg_match('/([0-9]+)\.jpg/', $images_file_name, $matches);
                $page_number = count($matches) > 1 ? $matches[1] + 1 : 1;

                // add images to database
                $upload_images = new TransactionUploadImages();
                $upload_images -> file_id = $file_id;
                $upload_images -> Agent_ID = $Agent_ID;
                $upload_images -> Listing_ID = $Listing_ID;
                $upload_images -> Contract_ID = $Contract_ID;
                $upload_images -> Referral_ID = $Referral_ID;
                $upload_images -> file_name = $images_file_name;
                $upload_images -> file_location = $images_public_path . '/' . $images_file_name;
                $upload_images -> pages_total = $pages_total;
                $upload_images -> page_number = $page_number;
                $upload_images -> save();

            }

            $saved_pages_directory = Storage::files('public/' . $storage_dir . '/pages');
            $pages_public_path = $storage_public_path . '/pages';

            $page_number = 1;

            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                $upload_pages = new TransactionUploadPages();
                $upload_pages -> Agent_ID = $Agent_ID;
                $upload_pages -> Listing_ID = $Listing_ID;
                $upload_pages -> Contract_ID = $Contract_ID;
                $upload_pages -> Referral_ID = $Referral_ID;
                $upload_pages -> file_id = $file_id;
                $upload_pages -> file_name = $pages_file_name;
                $upload_pages -> file_location = $pages_public_path . '/' . $pages_file_name;
                $upload_pages -> pages_total = $pages_total;
                $upload_pages -> page_number = $page_number;
                $upload_pages -> save();

                $page_number += 1;

            }

            $add_documents -> file_location = $file_location;
            $add_documents -> file_location_converted = $storage_public_path . '/converted/' . $new_file_name;
            $add_documents -> save();

        }

    }

    // TODO:: what is this?
    public function copy_file($path, $newpath) {
        $location = $this -> applyPathPrefix($path);
        $destination = $this -> applyPathPrefix($newpath);
        $this -> ensureDirectory(dirname($destination));
        return copy($location, $destination);
    }

    // End Documents Tab


    // Checklist Tab

    public function get_checklist(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        if ($transaction_type == 'listing') {
            $property = Listings::where('Listing_ID', $Listing_ID) -> first();
            $field = 'Listing_ID';
            $id = $Listing_ID;
        } elseif ($transaction_type == 'contract') {
            $property = Contracts::where('Contract_ID', $Contract_ID) -> first();
            $field = 'Contract_ID';
            $id = $Contract_ID;
        } elseif ($transaction_type == 'referral') {
            $property = Referrals::where('Referral_ID', $Referral_ID) -> first();
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_model = new TransactionChecklistItems();
        $transaction_checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $transaction_checklist_item_notes_model = new TransactionChecklistItemsNotes();
        $users = User::get();

        $agent = Agents::find($Agent_ID);

        $transaction_checklist = TransactionChecklists::where($field, $id) -> first();
        $transaction_checklist_id = $transaction_checklist -> id;
        $original_checklist_id = $transaction_checklist -> checklist_id;
        $transaction_checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $transaction_checklist_year_built = $transaction_checklist -> year_built;

        $checklist = Checklists::where('id', $original_checklist_id) -> first();

        $checklist_types = ['listing', 'both'];

        if ($checklist -> checklist_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($checklist -> checklist_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $transaction_checklist_items = $transaction_checklist_items_model -> where('checklist_id', $transaction_checklist_id) -> orderBy('checklist_item_order') -> get();

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        $rejected_reasons = ResourceItemsAdmin::where('resource_type', 'rejected_reason') -> orderBy('resource_order') -> get();

        $trash_folder = TransactionDocumentsFolders::where($field, $id) -> where('folder_name', 'Trash') -> first();
        $documents_model = new TransactionDocuments();
        $documents_available = $documents_model -> where($field, $id) -> where('Agent_ID', $Agent_ID) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('order') -> get();
        $documents_checklist = $documents_model -> where($field, $id) -> where('Agent_ID', $Agent_ID) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('order') -> get();
        $folders = TransactionDocumentsFolders::where($field, $id) -> where('Agent_ID', $Agent_ID) -> where('folder_name', '!=', 'Trash') -> orderBy('order') -> get();

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $files = new Upload();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/get_checklist', compact('property', 'Listing_ID', 'Contract_ID', 'transaction_type', 'checklist_items_model', 'transaction_checklist', 'transaction_checklist_id', 'transaction_checklist_items', 'transaction_checklist_item_docs_model', 'transaction_checklist_item_notes_model', 'transaction_checklist_items_model', 'checklist_groups', 'documents_model', 'users', 'documents_available', 'documents_checklist', 'folders', 'resource_items', 'property_types', 'property_sub_types', 'checklist', 'transaction_checklist_hoa_condo', 'transaction_checklist_year_built', 'rejected_reasons', 'files', 'form_groups', 'agent'));
    }

    public function add_document_to_checklist_item(Request $request) {

        $document_id = $request -> document_id;
        $checklist_id = $request -> checklist_id;
        $checklist_item_id = $request -> checklist_item_id;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        $add_checklist_item_doc = new TransactionChecklistItemsDocs();
        $add_checklist_item_doc -> document_id = $document_id;
        $add_checklist_item_doc -> checklist_id = $checklist_id;
        $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
        $add_checklist_item_doc -> Agent_ID = $Agent_ID;

        if ($transaction_type == 'listing') {
            $add_checklist_item_doc -> Listing_ID = $Listing_ID;
        } elseif ($transaction_type == 'contract') {
            $add_checklist_item_doc -> Contract_ID = $Contract_ID;
        } elseif ($transaction_type == 'referral') {
            $add_checklist_item_doc -> Referral_ID = $Referral_ID;
        }

        $add_checklist_item_doc -> save();

        $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
        $update_checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> update(['checklist_item_status' => 'not_reviewed']);

    }

    public function add_document_to_checklist_item_html(Request $request) {

        $transaction_type = strtolower($request -> transaction_type);
        $checklist_id = $request -> checklist_id;
        $document_ids = $request -> document_ids;

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_modal = new TransactionChecklistItems();

        $checklist_items = $transaction_checklist_items_modal -> where('checklist_id', $checklist_id) -> orderBy('checklist_item_order') -> get();
        $transaction_checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_id', $checklist_id) -> get();
        $documents = TransactionDocuments::whereIn('id', $document_ids) -> orderBy('order') -> get();

        $checklist_types = ['listing', 'both'];

        if ($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/add_document_to_checklist_item_html', compact('checklist_id', 'documents', 'transaction_checklist_item_documents', 'checklist_items_model', 'transaction_checklist_items_modal', 'checklist_items', 'checklist_groups'));
    }

    public function add_notes_to_checklist_item(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $add_notes = new TransactionChecklistItemsNotes();
        $add_notes -> checklist_id = $request -> checklist_id;
        $add_notes -> checklist_item_id = $request -> checklist_item_id;
        $add_notes -> checklist_item_doc_id = $request -> checklist_item_doc_id ?? null;

        if ($transaction_type == 'listing') {
            $add_notes -> Listing_ID = $Listing_ID;
        } elseif ($transaction_type == 'contract') {
            $add_notes -> Contract_ID = $Contract_ID;
        } elseif ($transaction_type == 'referral') {
            $add_notes -> Referral_ID = $Referral_ID;
        }

        $Agent_ID = 0;

        if (auth() -> user() -> group == 'agent') {
            $Agent_ID = $request -> Agent_ID;
        }

        $add_notes -> Agent_ID = $Agent_ID;
        $add_notes -> note_user_id = auth() -> user() -> id;
        $add_notes -> note_status = 'unread';
        $add_notes -> notes = $request -> notes;
        $add_notes -> save();
    }

    public function change_checklist(Request $request) {

        $checklist_id = $request -> checklist_id;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $transaction_type = strtolower($request -> transaction_type);
        $Agent_ID = $request -> Agent_ID;

        $transaction_checklist = TransactionChecklists::where('id', $checklist_id) -> first();
        $original_checklist_id = $transaction_checklist -> checklist_id;

        $checklist = Checklists::where('id', $original_checklist_id) -> first();
        $checklist_state = $checklist -> checklist_state;
        $checklist_location_id = $checklist -> checklist_location_id;
        $checklist_sale_rent = $transaction_checklist -> sale_rent;
        $checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $checklist_year_built = $transaction_checklist -> year_built;

        $checklist_property_type_id = ResourceItems::GetResourceID($request -> property_type, 'checklist_property_types');
        $checklist_property_sub_type_id = ResourceItems::GetResourceID($request -> property_sub_type, 'checklist_property_sub_types');

        $checklist_represent = 'seller';
        $checklist_type = 'listing';

        if ($transaction_type == 'contract') {
            $checklist_represent = 'buyer';
            $checklist_type = 'contract';
        }

        TransactionChecklists::CreateTransactionChecklist($checklist_id, $Listing_ID, $Contract_ID, '', $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        return true;
    }

    public function get_email_checklist_html(Request $request) {

        $checklist_id = $request -> checklist_id;
        $transaction_type = $request -> transaction_type;

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_model = new TransactionChecklistItems();

        $transaction_checklist_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> orderBy('checklist_item_order') -> get();

        $transaction_checklist_item_notes = new TransactionChecklistItemsNotes();

        $checklist_types = ['listing', 'both'];
        if ($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/get_email_checklist_html', compact('transaction_checklist_items', 'checklist_groups', 'checklist_items_model', 'transaction_checklist_items_model', 'transaction_checklist_item_notes'));

    }

    public function mark_note_read(Request $request) {
        $mark_read = TransactionChecklistItemsNotes::where('id', $request -> note_id) -> update(['note_status' => 'read']);
    }

    public function mark_required(Request $request) {
        $checklist_item_id = $request -> checklist_item_id;
        $required = $request -> required;

        $mark_required = TransactionChecklistItems::find($checklist_item_id) -> update(['checklist_item_required' => $required]);

        return true;
    }

    public function remove_checklist_item(Request $request) {

        // remove from items, item_docs and item_notes. then mark all transaction_docs unassigned
        $checklist_item_id = $request -> checklist_item_id;
        $delete_item = TransactionChecklistItems::where('id', $checklist_item_id) -> delete();
        $delete_item_notes = TransactionChecklistItemsNotes::where('checklist_item_id', $checklist_item_id) -> delete();

        $delete_item_docs = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id);
        $delete_item_doc_ids = $delete_item_docs -> pluck('document_id');

        $unassign = TransactionDocuments::whereIn('id', $delete_item_doc_ids) -> update(['assigned' => 'no', 'checklist_item_id' => null]);

        $delete_item_docs -> delete();

        return true;

    }

    public function remove_document_from_checklist_item(Request $request) {
        $document_id = $request -> document_id;
        $checklist_item_doc = TransactionChecklistItemsDocs::where('document_id', $document_id) -> delete();
        $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'no', 'checklist_item_id' => '0']);
    }

    public function save_add_checklist_item(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $checklist_id = $request -> checklist_id;
        $checklist_form_id = $request -> checklist_form_id;
        $add_checklist_item_name = $request -> add_checklist_item_name;
        $add_checklist_item_group_id = $request -> add_checklist_item_group_id;

        // get checklist item order
        $checklist_item_order = TransactionChecklistItems::where('checklist_id', $checklist_id) -> where('checklist_item_group_id', $add_checklist_item_group_id) -> max('checklist_item_order');
        $checklist_item_order += 1;

        $new_checklist_item = new TransactionChecklistItems();

        $new_checklist_item -> checklist_id = $checklist_id;
        $new_checklist_item -> Listing_ID = $Listing_ID;
        $new_checklist_item -> Contract_ID = $Contract_ID;
        $new_checklist_item -> Referral_ID = $Referral_ID;
        $new_checklist_item -> Agent_ID = $Agent_ID;
        $new_checklist_item -> checklist_form_id = $checklist_form_id;
        $new_checklist_item -> checklist_item_added_name = $add_checklist_item_name;
        $new_checklist_item -> checklist_item_required = 'yes';
        $new_checklist_item -> checklist_item_group_id = $add_checklist_item_group_id;
        $new_checklist_item -> checklist_item_order = $checklist_item_order;

        $new_checklist_item -> save();

    }

    public function set_checklist_item_review_status(Request $request) {

        $Agent_ID = $request -> Agent_ID ?? 0;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $checklist_item_id = $request -> checklist_item_id;
        $action = $request -> action;
        $note = $request -> note ?? null;

        if ($note) {
            $note = '<div><span class="text-danger"><i class="fad fa-exclamation-circle mr-2"></i> Checklist Item Rejected</span><br>' . $note . '</div>';
        }

        $checklist_item = TransactionChecklistItems::find($checklist_item_id) -> update(['checklist_item_status' => $action]);

        // update docs status
        $doc_status = 'viewed';

        if ($action == 'not_reviewed') {
            $doc_status = 'pending';
        }

        $docs = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id) -> update(['doc_status' => $doc_status]);

        if ($action == 'rejected') {
            // add rejection reason to notes
            $add_notes = new TransactionChecklistItemsNotes();
            $Agent_ID = 0;

            if (auth() -> user() -> group == 'agent') {
                $Agent_ID = $request -> Agent_ID;
            }

            $add_notes -> Agent_ID = $Agent_ID;

            if ($transaction_type == 'listing') {
                $add_notes -> Listing_ID = $Listing_ID;
            } elseif ($transaction_type == 'contract') {
                $add_notes -> Contract_ID = $Contract_ID;
            } elseif ($transaction_type == 'referral') {
                $add_notes -> Referral_ID = $Referral_ID;
            }

            $add_notes -> checklist_item_id = $checklist_item_id;
            $add_notes -> notes = $note;
            $add_notes -> note_user_id = auth() -> user() -> id;
            $add_notes -> save();
        }

        return response() -> json([
            'result' => 'success',
        ]);

    }

    // End Checklist Tab


    // Contracts tab

    public function get_contracts(Request $request) {
        $Listing_ID = $request -> Listing_ID ?? 0;
        $contracts = Contracts::where('Listing_ID', $Listing_ID) -> orderBy('Contract_ID', 'DESC') -> get();
        $resource_items = new ResourceItems();

        return view('/agents/doc_management/transactions/details/data/get_contracts', compact('contracts', 'resource_items'));
    }

    // End Contracts Tab


    // accept contract
    public function accept_contract(Request $request) {

        $buyer_one_first = $request -> buyer_one_first;
        $buyer_one_last = $request -> buyer_one_last;
        $buyer_two_first = $request -> buyer_two_first;
        $buyer_two_last = $request -> buyer_two_last;

        $agent_first = $request -> agent_first;
        $agent_last = $request -> agent_last;
        $agent_email = $request -> agent_email;
        $agent_phone = $request -> agent_phone;
        $agent_mls_id = $request -> agent_mls_id;
        $agent_company = $request -> agent_company;
        $agent_street = $request -> agent_street;
        $agent_city = $request -> agent_city;
        $agent_state = $request -> agent_state;
        $agent_zip = $request -> agent_zip;

        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::find($Listing_ID);

        // update listing
        $listing -> BuyerAgentFirstName = $agent_first;
        $listing -> BuyerAgentLastName = $agent_last;
        $listing -> BuyerAgentEmail = $agent_email;
        $listing -> BuyerAgentPreferredPhone = $agent_phone;
        $listing -> BuyerAgentMlsId = $agent_mls_id;
        $listing -> BuyerOfficeName = $agent_company;

        $listing -> BuyerOneFirstName = $buyer_one_first;
        $listing -> BuyerOneLastName = $buyer_one_last;
        $listing -> BuyerTwoFirstName = $buyer_two_first;
        $listing -> BuyerTwoLastName = $buyer_two_last;
        $listing -> Status = ResourceItems::GetResourceID('Under Contract', 'listing_status');
        /* $listing -> ContractDate = $request -> contract_date;
        $listing -> CloseDate = $request -> close_date;
        $listing -> ContractPrice = preg_replace('/[\$,]+/', '', $request -> contract_price); */
        $listing -> save();

        $using_heritage = $request -> using_heritage;
        $title_company = $request -> title_company;
        $earnest_amount = $request -> earnest_amount;
        $earnest_held_by = $request -> earnest_held_by;

        // new contract data
        $contract_data = $listing -> replicate();
        $contract_data -> Listing_ID = $Listing_ID;
        $contract_data -> BuyerAgentFirstName = $agent_first;
        $contract_data -> BuyerAgentLastName = $agent_last;
        $contract_data -> BuyerAgentEmail = $agent_email;
        $contract_data -> BuyerAgentPreferredPhone = $agent_phone;
        $contract_data -> BuyerAgentMlsId = $agent_mls_id;
        $contract_data -> BuyerOfficeName = $agent_company;

        $contract_data -> BuyerOneFirstName = $buyer_one_first;
        $contract_data -> BuyerOneLastName = $buyer_one_last;
        $contract_data -> BuyerTwoFirstName = $buyer_two_first;
        $contract_data -> BuyerTwoLastName = $buyer_two_last;
        $contract_data -> ContractDate = $request -> contract_date;
        $contract_data -> CloseDate = $request -> close_date;
        $contract_data -> ContractPrice = preg_replace('/[\$,]+/', '', $request -> contract_price);

        $contract_data -> EarnestAmount = preg_replace('/[\$,]+/', '', $earnest_amount);
        $contract_data -> EarnestHeldBy = $earnest_held_by;
        $contract_data -> UsingHeritage = $using_heritage;
        $contract_data -> TitleCompany = $title_company;

        $FullStreetAddress = $contract_data -> FullStreetAddress;

        $contract_data -> Status = ResourceItems::GetResourceID('Active', 'contract_status');

        $contract_data = collect($contract_data -> toArray()) -> except(['Contract_ID']);

        $contract_data = json_decode($contract_data, true);
        $new_contract = Contracts::create($contract_data);
        $Contract_ID = $new_contract -> Contract_ID;

        // add email address
        $new_transaction = Contracts::find($Contract_ID);

        $code = 'C' . $Contract_ID;
        $address = preg_replace(config('global.vars.bad_characters'), '', $FullStreetAddress);
        $email = $address . '_' . $code . '@' . config('global.vars.property_email');

        $new_transaction -> PropertyEmail = $email;
        $new_transaction -> save();

        // add Contract_ID to members already in members
        $import_members_from_listing = Members::where('Listing_ID', $Listing_ID) -> update(['Contract_ID' => $Contract_ID]);

        // add buyers and buyers agent to members
        $add_buyer_to_members = new Members();
        $add_buyer_to_members -> member_type_id = ResourceItems::BuyerResourceId();
        $add_buyer_to_members -> first_name = $buyer_one_first;
        $add_buyer_to_members -> last_name = $buyer_one_last;
        $add_buyer_to_members -> Contract_ID = $Contract_ID;
        $add_buyer_to_members -> Agent_ID = $listing -> Agent_ID;
        $add_buyer_to_members -> save();

        if ($buyer_two_first != '') {
            $add_buyer_to_members = new Members();
            $add_buyer_to_members -> member_type_id = ResourceItems::BuyerResourceId();
            $add_buyer_to_members -> first_name = $buyer_two_first;
            $add_buyer_to_members -> last_name = $buyer_two_last;
            $add_buyer_to_members -> Contract_ID = $Contract_ID;
            $add_buyer_to_members -> Agent_ID = $listing -> Agent_ID;
            $add_buyer_to_members -> save();
        }

        $add_buyer_agent_to_members = new Members();
        $add_buyer_agent_to_members -> member_type_id = ResourceItems::BuyerAgentResourceId();
        $add_buyer_agent_to_members -> first_name = $agent_first;
        $add_buyer_agent_to_members -> last_name = $agent_last;
        $add_buyer_agent_to_members -> cell_phone = $agent_phone;
        $add_buyer_agent_to_members -> email = $agent_email;
        $add_buyer_agent_to_members -> bright_mls_id = $agent_mls_id;
        $add_buyer_agent_to_members -> company = $agent_company;
        $add_buyer_agent_to_members -> address_office_street = $agent_street;
        $add_buyer_agent_to_members -> address_office_city = $agent_city;
        $add_buyer_agent_to_members -> address_office_state = $agent_state;
        $add_buyer_agent_to_members -> address_office_zip = $agent_zip;
        $add_buyer_agent_to_members -> Contract_ID = $Contract_ID;
        $add_buyer_agent_to_members -> Agent_ID = $listing -> Agent_ID;
        $add_buyer_agent_to_members -> save();

        // if using heritage add them to members

        // TODO: notify title if using them
        if ($using_heritage == 'yes') {
            $add_heritage_to_members = new Members();
            $add_heritage_to_members -> member_type_id = ResourceItems::TitleResourceId();
            $add_heritage_to_members -> company = 'Heritage Title';
            $add_heritage_to_members -> Contract_ID = $Contract_ID;
            $add_heritage_to_members -> Agent_ID = $listing -> Agent_ID;
            $add_heritage_to_members -> save();
        }
        // TODO: if earnest
        // if holding earnest
        // notify
        // add checklist
        $checklist_represent = 'buyer';

        if ($Listing_ID > 0) {
            $checklist_represent = 'seller';
        }

        $checklist_property_type_id = $listing -> PropertyType;
        $checklist_property_sub_type_id = $listing -> PropertySubType;
        $checklist_sale_rent = $listing -> SaleRent;
        $checklist_state = $listing -> StateOrProvince;
        $checklist_location_id = $listing -> Location_ID;
        $transaction_checklist = TransactionChecklists::where('Listing_ID', $Listing_ID) -> first();
        $checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $checklist_year_built = $listing -> YearBuilt;

        // create checklist
        TransactionChecklists::CreateTransactionChecklist('', $Listing_ID, $Contract_ID, '', $listing -> Agent_ID, 'seller', 'contract', $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        // add folders from listing
        $folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> update(['Contract_ID' => $Contract_ID]);

        return response() -> json([
            'Contract_ID' => $Contract_ID,
        ]);

    }

    public function get_path($url) {
        return Storage::disk('public') -> path(preg_replace('/^.*\/storage\//', '', $url));
    }

    // search bright mls agents
    public function search_bright_agents(Request $request) {

        $val = $request -> val;

        $agents = AgentRoster::where('MemberLastName', 'like', '%' . $val . '%')
            -> orWhere('MemberEmail', 'like', '%' . $val . '%')
            -> orWhere('MemberMlsId', 'like', '%' . $val . '%')
            -> orWhereRaw('CONCAT(MemberFirstName, " ", MemberLastName) like \'%' . $val . '%\'')
            -> orWhereRaw('CONCAT(MemberNickname, " ", MemberLastName) like \'%' . $val . '%\'')
            -> orderBy('MemberLastName')
            -> get();

        return compact('agents');
    }

    public function send_email(Request $request) {

        $type = $request -> type;
        $email = [];
        $from_address = $request -> from;
        $from_name = '';

        if (preg_match('/\<.*\>/', $from_address)) {
            preg_match('/(.*)[\s]*\<(.*)\>/', $from_address, $match);
            $from_name = $match[1];
            $from_address = $match[2];
        }

        $email['from'] = ['address' => $from_address, 'name' => $from_name];

        $email['subject'] = $request -> subject;
        $email['message'] = $request -> message;

        $email['tos_array'] = [];

        foreach (json_decode($request -> to_addresses) as $to_address) {
            $to = [];
            $to['type'] = $to_address -> type;
            $to['address'] = $to_address -> address;
            $email['tos_array'][] = $to;
        }

        $email['attachments'] = [];
        $attachment_size = 0;

        if ($request -> attachments) {

            foreach (json_decode($request -> attachments) as $attachment) {
                $file = [];
                $file['name'] = $attachment -> filename;
                $file['location'] = $attachment -> file_location;
                $email['attachments'][] = $file;
                $attachment_size += filesize(Storage::disk('public') -> path($attachment -> file_location));

            }

            $attachment_size = get_mb($attachment_size);
            if ($attachment_size > 20) {
                $fail = json_encode(['fail' => true, 'attachment_size' => $attachment_size]);
                return ($fail);
            }

        }

        $email['tos'] = [];
        $email['ccs'] = [];
        $email['bccs'] = [];

        foreach ($email['tos_array'] as $to) {

            $to_address = $to['address'];
            $to_name = '';

            if (preg_match('/\<.*\>/', $to['address'])) {
                preg_match('/(.*)[\s]*\<(.*)\>/', $to['address'], $match);
                $to_name = $match[1];
                $to_address = $match[2];
            }

            if ($to['type'] == 'to') {
                $email['tos'][] = ['name' => $to_name, 'email' => $to_address];
            } elseif ($to['type'] == 'cc') {
                $email['ccs'][] = ['name' => $to_name, 'email' => $to_address];
            } elseif ($to['type'] == 'bcc') {
                $email['bccs'][] = ['name' => $to_name, 'email' => $to_address];
            }

        }

        if ($type == 'documents') {
            $new_mail = new Documents($email);
        } else {
            $new_mail = new DefaultEmail($email);
        }

        //return ($new_mail) -> render();

        Mail::to($email['tos'])
            -> cc($email['ccs'])
            -> bcc($email['bccs'])
            -> send($new_mail);

    }

}
