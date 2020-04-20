<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Auth;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/logout'
    ];

    public function handle($request, Closure $next)
    {

        if(basename(request() -> path()) != 'login') {
            if(auth() -> user() == null) {
                return redirect() -> route('login');
            }
        }
        return $next($request);
    }
}
