<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Listings\Listings;
use Illuminate\Http\Request;

class DashboardAgentController extends Controller
{
    public function dashboard_agent(Request $request)
    {
        $Agent_ID = auth()->user()->user_id;

        $resource_items = new ResourceItems();

        $include_under_contract = 'yes';
        $listings = Listings::where('Agent_ID', $Agent_ID)->whereIn('Status', ResourceItems::GetActiveListingStatuses($include_under_contract, 'no', 'no'))->orderBy('Status')->get();
        $contracts = Contracts::where('Agent_ID', $Agent_ID)->whereIn('Status', ResourceItems::GetActiveContractStatuses())->orderBy('Status')->get();

        return view('/dashboard/agent/dashboard', compact('resource_items', 'listings', 'contracts'));
    }
}
