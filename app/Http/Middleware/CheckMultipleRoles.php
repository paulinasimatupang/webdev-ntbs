<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth; // Import Auth

use Closure;

class CheckMultipleRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Anda harus login terlebih dahulu.');
        }
    
        $user = Auth::user();
        
        // Ganti dengan logika pemeriksaan role yang sesuai
        $userRoles = $user->roles; // Misalnya, jika role disimpan dalam atribut `roles`
    
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }
    
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
