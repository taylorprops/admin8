<?php

namespace App\Http\Controllers\DocManagement\Commission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Commission\Commission;
use App\Models\Commission\CommissionChecksInQueue;
use App\Models\Employees\Agents;
use App\Models\Resources\LocationData;

class CommissionController extends Controller
{

    public function commission(Request $request) {

        // pending - where checks in and checks out = 0 - where checks in more than checks out
        $agents = Agents::select('id', 'first_name', 'last_name') -> where('active', 'yes') -> orderBy('last_name') -> get();
        $states = LocationData::AllStates();

        return view('/doc_management/commission/commission', compact('agents', 'states'));

    }

    public function get_checks(Request $request) {

        $checks = CommissionChecksInQueue::whereNull('Commission_ID') -> get();

        return view('/doc_management/commission/get_checks_html', compact('checks'));

    }

}
