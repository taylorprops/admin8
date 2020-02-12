<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddTransactionController extends Controller
{
    public function add_listing() {
        return view('/agents/doc_management/transactions/add_listing');
    }

    public function add_contract() {
        return view('/agents/doc_management/transactions/add_contract');
    }
}
