<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth() -> user()) {
            $group = auth() -> user() -> group;
            if($group == 'admin'){
                return $next($request);
            }
            if($group == 'agent') {
                $redirect_url = 'dashboard';
            }
            return redirect($redirect_url) -> with('error','You do not have access');
        }

        return response() -> json([
            'dismiss' => __('Session expired due to inactivity'),
        ]);

    }
}
