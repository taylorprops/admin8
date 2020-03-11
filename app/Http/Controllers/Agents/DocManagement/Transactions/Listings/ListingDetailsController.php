<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Listings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Transactions\Listings;


class ListingDetailsController extends Controller
{
    public function listing(Request $request) {
        $Listing_ID = $request -> id;
        // if agent logged in filter by agent_id
        $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
        return view('/agents/doc_management/transactions/listings/listing_details', compact('listing'));

    }
}
