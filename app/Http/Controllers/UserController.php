<?php

namespace App\Http\Controllers;

use App\Models\Employees\Agents;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller {


    public function dashboard(Request $request) {

        if (Auth::check()) {

            if (auth() -> user() -> group == 'admin') {
                return view('/dashboard/admin/dashboard');
            } elseif (auth() -> user() -> group == 'agent') {
                $agent_details = Agents::whereId(auth() -> user() -> user_id) -> first();
                $request -> session() -> put('agent_details', $agent_details);
                return view('/dashboard/agent/dashboard');
            }

        }

        return redirect('/');
    }

}
