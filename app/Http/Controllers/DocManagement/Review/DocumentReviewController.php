<?php

namespace App\Http\Controllers\DocManagement\Review;

use App\Http\Controllers\Controller;
use App\Models\Admin\Resources\ResourceItemsAdmin;
// use File;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Resources\ResourceItems;
// use App\Models\CRM\CRMContacts;

use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
// use Illuminate\Support\Facades\Mail;
// use App\Models\Resources\LocationData;
// use Illuminate\Support\Facades\Storage;
// use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;
// use App\Models\DocManagement\Create\Fields\FieldInputs;
// use App\Models\DocManagement\Create\Upload\UploadPages;
// use App\Models\DocManagement\Checklists\Checklists;
// use App\Models\DocManagement\Checklists\ChecklistsItems;
// use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;
// use App\Models\DocManagement\Transactions\EditFiles\UserFields;
// use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
// use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
// use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;
// use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Members\Members;
// use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
// use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\Employees\Agents;
// use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\User;
use Config;
use Illuminate\Http\Request;

// use App\Models\BrightMLS\AgentRoster;
// use App\Mail\DocManagement\Emails\Documents;
// use App\Mail\DefaultEmail;
// use Illuminate\Support\Facades\DB;

class DocumentReviewController extends Controller
{
    public function document_review(Request $request)
    {
        $Contract_ID = $request->Contract_ID ?? null;

        $cancel_request_ids = Upload::PendingReleases();
        $cancel_requests = Contracts::whereIn('Contract_ID', $cancel_request_ids)->get();

        $listing_checklist_items = TransactionChecklistItemsDocs::where('doc_status', 'pending')->where('Listing_ID', '>', '0')->groupBy('Listing_ID')->get();
        $contract_checklist_items = TransactionChecklistItemsDocs::where('doc_status', 'pending')->where('Contract_ID', '>', '0')->whereNotIn('Contract_ID', $cancel_request_ids)->groupBy('Contract_ID')->get();
        $referral_checklist_items = TransactionChecklistItemsDocs::where('doc_status', 'pending')->where('Referral_ID', '>', '0')->groupBy('Referral_ID')->get();

        $listing_ids = $listing_checklist_items->pluck('Listing_ID');
        $contract_ids = $contract_checklist_items->pluck('Contract_ID');
        $referral_ids = $referral_checklist_items->pluck('Referral_ID');

        $listings = Listings::whereIn('Listing_ID', $listing_ids)->get();
        $contracts = Contracts::whereIn('Contract_ID', $contract_ids)->get();
        $referrals = Referrals::whereIn('Referral_ID', $referral_ids)->get();

        $checklist_item_docs = new TransactionChecklistItemsDocs();
        $checklist_item_notes = new TransactionChecklistItemsNotes();

        $listing_checklist_item_notes_ids = $checklist_item_notes->where('note_status', 'unread')->where('Listing_ID', '>', '0')->where('Agent_ID', '>', '0')->whereNotIn('Listing_ID', $listing_checklist_items->pluck('Listing_ID'))->pluck('Listing_ID');

        $contract_checklist_item_notes_ids = $checklist_item_notes->where('note_status', 'unread')->where('Contract_ID', '>', '0')->where('Agent_ID', '>', '0')->whereNotIn('Contract_ID', $contract_checklist_items->pluck('Contract_ID'))->pluck('Contract_ID');

        $referral_checklist_item_notes_ids = $checklist_item_notes->where('note_status', 'unread')->where('Referral_ID', '>', '0')->where('Agent_ID', '>', '0')->whereNotIn('Referral_ID', $referral_checklist_items->pluck('Referral_ID'))->pluck('Referral_ID');

        $listings_with_notes = Listings::whereIn('Listing_ID', $listing_checklist_item_notes_ids)->get();
        $contracts_with_notes = Contracts::whereIn('Contract_ID', $contract_checklist_item_notes_ids)->get();
        $referrals_with_notes = Referrals::whereIn('Referral_ID', $referral_checklist_item_notes_ids)->get();

        return view('/doc_management/review/document_review', compact('Contract_ID', 'listing_checklist_items', 'contract_checklist_items', 'referral_checklist_items', 'listings', 'contracts', 'referrals', 'checklist_items', 'members', 'checklist_item_docs', 'checklist_item_notes', 'listings_with_notes', 'contracts_with_notes', 'referrals_with_notes', 'cancel_requests'));
    }

    public function get_checklist(Request $request)
    {
        $transaction_type = $request->type;
        $id = $request->id;

        $checklist_types = ['listing', 'both'];
        $field = 'Listing_ID';
        $property = Listings::find($id);
        if ($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
            $field = 'Contract_ID';
            $property = Contracts::find($id);
        } elseif ($transaction_type == 'referral') {
            $checklist_types = ['referral'];
            $field = 'Referral_ID';
            $property = Referrals::find($id);
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups')->whereIn('resource_form_group_type', $checklist_types)->orderBy('resource_order')->get();

        $transaction_checklist = TransactionChecklists::where($field, $id)->first();
        $transaction_checklist_id = $transaction_checklist->id;

        $transaction_checklist_items_model = new TransactionChecklistItems();
        $checklist_items = $transaction_checklist_items_model->where('checklist_id', $transaction_checklist_id)->orderBy('checklist_item_order')->get();

        $files = new Upload();

        $transaction_checklist_item_notes = new TransactionChecklistItemsNotes();

        $resource_items = new ResourceItems();
        $form_groups = $resource_items->where('resource_type', 'form_groups')->orderBy('resource_order')->get();

        // used in checklist review modals
        $rejected_reasons = ResourceItemsAdmin::where('resource_type', 'rejected_reason')->orderBy('resource_order')->get();

        $agent_details = Agents::find($property->Agent_ID);

        return view('/doc_management/review/get_checklist_html', compact('property', 'transaction_type', 'transaction_checklist_id', 'checklist_groups', 'checklist_items', 'transaction_checklist_id', 'files', 'transaction_checklist_items_model', 'transaction_checklist_item_notes', 'form_groups', 'resource_items', 'rejected_reasons', 'agent_details'));
    }

    public function get_documents(Request $request)
    {
        $checklist_item_id = $request->checklist_item_id;
        $checklist_item_name = $request->checklist_item_name;

        $checklist_item = TransactionChecklistItems::where('id', $checklist_item_id)->first();

        $checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id)->get();

        $checklist_item_images_model = new TransactionDocumentsImages();

        $transaction_documents_model = new TransactionDocuments();

        return view('/doc_management/review/get_documents_html', compact('checklist_item', 'checklist_item_id', 'checklist_item_name', 'checklist_item_documents', 'checklist_item_images_model', 'transaction_documents_model'));
    }

    public function get_details(Request $request)
    {
        $transaction_type = $request->type;
        $id = $request->id;

        if ($transaction_type == 'listing') {
            $property = Listings::find($id);
        } elseif ($transaction_type == 'contract') {
            $property = Contracts::find($id);
        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($id);
        }

        $address = ucwords(strtolower($property->FullStreetAddress)).'<br>'.ucwords(strtolower($property->City)).', '.$property->StateOrProvince.' '.$property->PostalCode;

        if ($transaction_type == 'listing') {
            $members = Members::where('Listing_ID', $id)->get();
        } else {
            $members = Members::where('Contract_ID', $id)->get();
        }

        $resource_items = new ResourceItems();

        $sale_rent = 'For Sale';
        $for_sale = true;
        if ($property->SaleRent == 'rental') {
            $sale_rent = 'Rental';
            $for_sale = false;
        } elseif ($property->SaleRent == 'both' && $transaction_type == 'listing') {
            $sale_rent = 'For Sale And Rent';
        }

        $earnest_held_by = 'Taylor/Anne Arundel Properties';
        if ($property->EarnestHeldBy == 'other_company') {
            $earnest_held_by = $property->Listing_ID > 0 ? $property->BuyerOfficeName : $property->ListOfficeName;
        } elseif ($property->EarnestHeldBy == 'heritage_title') {
            $earnest_held_by = 'Heritage Title';
        } elseif ($property->EarnestHeldBy == 'title') {
            $earnest_held_by = $property->TitleCompany;
        } elseif ($property->EarnestHeldBy == 'builder') {
            $earnest_held_by = 'Builder';
        }

        $title_company = $property->TitleCompany;
        if ($property->UsingHeritage == 'yes') {
            $title_company = 'Heritage Title';
        }

        $agent_details = Agents::find($property->Agent_ID);
        $co_agent_details = Agents::find($property->CoAgent_ID);

        $cancel_pending_status_id = $resource_items->GetResourceID('Cancel Pending', 'contract_status');
        $canceled_status_id = $resource_items->GetResourceID('Canceled', 'contract_status');

        return view('/doc_management/review/get_details_html', compact('transaction_type', 'id', 'members', 'property', 'address', 'sale_rent', 'for_sale', 'resource_items', 'agent_details', 'co_agent_details', 'earnest_held_by', 'title_company', 'cancel_pending_status_id', 'canceled_status_id'));
    }

    public function get_notes(Request $request)
    {
        $checklist_item_id = $request->checklist_item_id;
        $Agent_ID = $request->Agent_ID;

        $transaction_checklist_item_notes = TransactionChecklistItemsNotes::where('checklist_item_id', $checklist_item_id)->orderBy('created_at', 'DESC')->get();

        $users = User::get();

        $agent = $users->where('user_id', $Agent_ID)->where('group', 'agent')->first();

        return view('/doc_management/review/get_notes_html', compact('checklist_item_id', 'transaction_checklist_item_notes', 'users', 'agent'));
    }

    public function delete_note(Request $request)
    {
        $note_id = $request->note_id;
        $note = TransactionChecklistItemsNotes::find($note_id)->delete();
    }
}
