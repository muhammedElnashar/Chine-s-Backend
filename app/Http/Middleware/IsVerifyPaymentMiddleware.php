<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsVerifyPaymentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $courseId = $request->route('id');
        $user = auth()->user();

        if (!$user || !$user->hasPurchased($courseId)) {
            return response()->json([
                'status' => false,
                'message' => 'You need to purchase this course to access its content.',
            ], 403);
        }

        return $next($request);
    }

}
