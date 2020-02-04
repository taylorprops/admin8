<?php

namespace App\Http\Middleware;

use Closure;

class Agent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   if(auth() -> user()) {
            if(auth() -> user() -> group == 'agent' || auth() -> user() -> group == 'admin'){
                return $next($request);
            }
        }

        return response() -> view('/auth/login', [], 404);
    }
}
