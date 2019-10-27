<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CommonFields;

class FieldsController extends Controller
{
    public function get_common_fields(Request $request) {
        $fields = CommonFields::select('field_name') -> orderBy('field_name', 'ASC') -> get();
        return (string) $fields;
    }
}
