<?php

namespace App\Http\Middleware;

use App\Admin;
use Closure;

class CheckUserHasoperatorRole
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

        if ( $request->user()->role!=Admin::ADMIN_ROLE) {

            if ($request->user()->role!=Admin::OPERATOR_ROLE)
            {
                $message['error']='عدم دسترسی';
                return redirect()->route('admin.dashboard')->with($message);
            }

        }

        return $next($request);
    }
}
