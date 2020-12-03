<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DocManagement\Create\Fields\Fields;

class TestController extends Controller
{
    public function test(Request $request) {

        $fields = Fields::get();

        foreach ($fields as $field) {

            if(!ctype_upper(substr($field -> field_name, 0, 1)) && $field -> field_name != '') {
                //echo $field -> field_name.' = '.$field -> field_name_display.'<br>';

                $field -> field_name_display = ucwords($field -> field_name_display);
                //$field -> field_name = str_replace(' ', '', ucwords($field -> field_name_display));
                $field -> save();
            }
        }

        //return view('/tests/test', compact(''));
    }
}
