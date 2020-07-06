<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;

use App\Models\DocManagement\Resources\ResourceItems;

class TransactionsController extends Controller
{
    public function get_transactions(Request $request) {
        $listings = Listings::where('Agent_ID', auth() -> user() -> user_id) -> orderBy('Status') -> get();
        $contracts = Contracts::where('Agent_ID', auth() -> user() -> user_id) -> orderBy('Status') -> get();
        $resource_items = new ResourceItems();

        return view('/agents/doc_management/transactions/transactions_all', compact('listings', 'contracts', 'resource_items'));
    }
}
