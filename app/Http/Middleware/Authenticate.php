<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Don't require certain routes to be authenticated
     */
    protected $safe = [
        '/',
        'auth/login',
        'auth/register',
        'password/email',
    ];

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // any clockwork request should pass through
        if(strpos($request->path, 'clockwork') !== false) {
            return $next($request);
        }

        // Only authenticate on unsafe pages
        if ($this->auth->guest () && ! in_array ( $request->path (), $this->safe )) {
            if ($request->ajax ()) {
                return response ( 'Unauthorized.', 401 );
            } else {
                return redirect ()->guest ( '/' )->withErrors ( [
                    'access' => 'Please log-in.'
                ] );
            }
        }

        return $next($request);
    }
}
