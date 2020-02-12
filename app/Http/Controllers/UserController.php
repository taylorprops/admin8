<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function dashboard() {
        if(Auth::check()) {
            if (auth() -> user() -> group == 'admin') {
                return view('/dashboard/admin/dashboard');
            } else if (auth() -> user() -> group == 'agent') {
                return view('/dashboard/agent/dashboard');
            }
        }

        return redirect('/');
    }
}
