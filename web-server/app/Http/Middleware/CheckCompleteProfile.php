<?php

namespace App\Http\Middleware;

use Closure;

class CheckCompleteProfile
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
        if (! $request->user()->isCompleted) {
            return response()->json(['errors'=>"you mus first complete profile"])->setStatusCode(401);

        }

        return $next($request);
    }
}
