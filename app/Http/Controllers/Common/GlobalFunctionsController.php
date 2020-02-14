<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Zips;

class GlobalFunctionsController extends Controller
{
    public function get_location_details(Request $request) {
        $zip = $request -> zip;
        $location_details = Zips::select('city', 'state', 'county') -> where('zip', $zip) -> first();
        if($location_details) {
            return $location_details -> toJson();
        }
        return null;

    }
}
