<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            Auth::logout();

            return redirect()->back()
                ->with('error', 'You do not have Admin or Super Admin access');
        }


        return $next($request);
    }
}
