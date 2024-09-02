<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next): Response
    {
        if(Auth::check())
        {
            /**
             * @var App\Models\User
             */
            $user = Auth::user();
            if($user->hasRole(['super-admin', 'admin'])){
                return $next($request); 
            }

            abort(403, "User does not have correct ROLE");
        }

        abort(401);
    }
}
