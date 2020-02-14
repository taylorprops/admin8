<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\ResourceItems;
use App\Models\DocManagement\Zips;
use App\Models\Common\GlobalFunctionsController;

class AddTransactionController extends Controller
{
    public function add_listing() {
        $states = Zips::ActiveStates();
        return view('/agents/doc_management/transactions/add_listing', compact('states'));
    }

    public function add_contract() {
        return view('/agents/doc_management/transactions/add_contract');
    }

    public function update_county_select(Request $request) {
        $counties = Zips::select('county') -> where('state', $request -> state) -> groupBy('county') -> orderBy('county') -> get() -> toJson();
        return $counties;
    }
}
