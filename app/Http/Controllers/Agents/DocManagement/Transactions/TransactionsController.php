<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function get_transactions(Request $request)
    {
        if (auth()->user()->group == 'agent') {
            $listings = Listings::where('Agent_ID', auth()->user()->user_id)->orderBy('Status')->get();
            $contracts = Contracts::where('Agent_ID', auth()->user()->user_id)->orderBy('Status')->get();
            $referrals = Referrals::where('Agent_ID', auth()->user()->user_id)->orderBy('Status')->get();
        } else {
            $listings = Listings::orderBy('Status')->get();
            $contracts = Contracts::orderBy('Status')->get();
            $referrals = Referrals::orderBy('Status')->get();
        }
        $resource_items = new ResourceItems();

        return view('/agents/doc_management/transactions/transactions_all', compact('listings', 'contracts', 'referrals', 'resource_items'));
    }
}
