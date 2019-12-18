<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function dashboard() {

        if (Auth::user() -> group == 'admin') {
            return view('/dashboard/admin/dashboard');
        } else if (Auth::user() -> group == 'agent') {
            return view('/dashboard/agent/dashboard');
        }
    }
}
