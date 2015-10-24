<?php

namespace App\Http\Middleware;

use Closure;

class VerifyOauthApi
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
        // check if a request param is one of our registered providers
        if (in_array($request->get('api'), ['github'], true)) {
            return $next($request);
        }
        return redirect()->back();
    }
}
