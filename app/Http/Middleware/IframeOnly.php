<?php

namespace App\Http\Middleware;

use Closure;

class IframeOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {       
        $referrer = $request->server('HTTP_REFERER', null);

        if(is_null($referrer)) {
            return redirect()->to('home');
        }

        return $next($request);
    }
}
