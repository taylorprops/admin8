<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Resources\ResourceItems;

class DashboardAgentController extends Controller
{
    public function dashboard_agent(Request $request) {

        $Agent_ID = auth() -> user() -> user_id;

        $resource_items = new ResourceItems();

        $listings = Listings::where('Agent_ID', $Agent_ID) -> whereIn('Status', ResourceItems::GetActiveListingStatuses()) -> orderBy('Status') -> get();
        $contracts = Contracts::where('Agent_ID', $Agent_ID) -> whereIn('Status', ResourceItems::GetActiveContractStatuses()) -> orderBy('Status') -> get();

        return view('/dashboard/agent/dashboard', compact('resource_items', 'listings', 'contracts'));
    }
}
