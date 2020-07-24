<?php

namespace App\Http\Controllers\DocManagement\Review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// use File;
use Config;
// use App\User;
// use App\Models\CRM\CRMContacts;
// use App\Models\Employees\Teams;

// use App\Models\Employees\Agents;
// use Illuminate\Support\Facades\Mail;
// use App\Models\Resources\LocationData;
// use Illuminate\Support\Facades\Storage;
// use App\Models\DocManagement\Create\Fields\Fields;
// use App\Models\DocManagement\Create\Upload\Upload;
// use App\Models\DocManagement\Resources\ResourceItems;
// use App\Models\DocManagement\Create\Fields\FieldInputs;
// use App\Models\DocManagement\Create\Upload\UploadPages;
// use App\Models\DocManagement\Checklists\Checklists;
// use App\Models\DocManagement\Checklists\ChecklistsItems;
// use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
// use App\Models\DocManagement\Transactions\EditFiles\UserFields;
// use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
// use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
// use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;
// use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;
// use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
// use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
// use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;
// use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
// use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;
// use App\Models\Admin\Resources\ResourceItemsAdmin;
// use App\Models\BrightMLS\AgentRoster;
// use App\Mail\DocManagement\Emails\Documents;
// use App\Mail\DefaultEmail;
// use Illuminate\Support\Facades\DB;

class DocumentReviewController extends Controller
{
    public function document_review(Request $request) {

        $listing_checklist_items = TransactionChecklistItems::where('checklist_item_status', 'not_reviewed') -> where('Listing_ID', '>', '0') -> groupBy('Listing_ID') -> get();
        $contract_checklist_items = TransactionChecklistItems::where('checklist_item_status', 'not_reviewed') -> where('Contract_ID', '>', '0') -> groupBy('Contract_ID') -> get();
        $referral_checklist_items = TransactionChecklistItems::where('checklist_item_status', 'not_reviewed') -> where('Referral_ID', '>', '0') -> groupBy('Referral_ID') -> get();

        $listing_ids = $listing_checklist_items -> pluck('Listing_ID');
        $contract_ids = $contract_checklist_items -> pluck('Contract_ID');
        $referral_ids = $referral_checklist_items -> pluck('Referral_ID');

        $listings = Listings::whereIn('Listing_ID', $listing_ids) -> get();
        $contracts = Contracts::whereIn('Contract_ID', $contract_ids) -> get();
        $referrals = Referrals::whereIn('Referral_ID', $referral_ids) -> get();

        $checklist_items = new TransactionChecklistItems();

        $members = new Members();


        return view('/doc_management/review/document_review', compact('listing_checklist_items', 'contract_checklist_items', 'referral_checklist_items', 'listings', 'contracts', 'referrals', 'checklist_items', 'members'));

    }
}
