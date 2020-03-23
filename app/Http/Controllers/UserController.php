<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Employees\Agents;

class UserController extends Controller
{
    public function dashboard(Request $request) {
        if(Auth::check()) {
            if (auth() -> user() -> group == 'admin') {
                return view('/dashboard/admin/dashboard');
            } else if (auth() -> user() -> group == 'agent') {
                $agent_details = Agents::whereId(auth() -> user() -> user_id) -> first();
                $request -> session() -> put('agent_details', $agent_details);
                return view('/dashboard/agent/dashboard');
            }
        }

        return redirect('/');
    }
}
