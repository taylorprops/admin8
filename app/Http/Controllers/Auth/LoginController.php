<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Employees\Agents;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo(){

        if(auth() -> user() -> group == 'admin') {

            \Request::session() -> put('header_logo_src', '/images/logo/logo_tp.png');
            \Request::session() -> put('email_logo_src', '/images/emails/TP-flat-white.png');

        } else if(auth() -> user() -> group == 'agent') {

            $Agent_ID = auth() -> user() -> user_id;

            // get agent details and add to session
            $agent_details = Agents::whereId($Agent_ID) -> first();
            \Request::session() -> put('agent_details', $agent_details);

            // set logo for header logo and EMAILS by company and add to session
            \Request::session() -> put('header_logo_src', '/images/logo/logo_aap.png');
            \Request::session() -> put('email_logo_src', '/images/emails/AAP-flat-white.png');
            if (stristr($agent_details -> company, 'Taylor')) {
                \Request::session() -> put('header_logo_src', '/images/logo/logo_tp.png');
                \Request::session() -> put('email_logo_src', '/images/emails/TP-flat-white.png');
            }

        }

        // redirect to page requested or dashboard
        if($this -> previous_url != '' && stristr($this -> previous_url, $_SERVER['HTTP_HOST']) && stristr($this -> previous_url, 'login') === FALSE) {
            $this -> redirectTo = $this -> previous_url;
        } else {
            $this -> redirectTo = 'dashboard_'.auth() -> user() -> group;
        }

        return $this -> redirectTo;

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this -> previous_url = $request -> previous_url;
        $this -> middleware('guest') -> except(['logout', 'login']);
    }
}
