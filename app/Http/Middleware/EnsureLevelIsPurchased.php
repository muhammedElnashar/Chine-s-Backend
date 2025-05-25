<?php

namespace App\Http\Middleware;

use App\Models\Level;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLevelIsPurchased
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        $levelId = $request->route('id'); // تأكد من اسم الـ route parameter عندك
        $user = $request->user();

        $level = Level::findOrFail($levelId);

        if ($level->is_free || ($user && $user->hasPurchasedLevel($level->id))) {
            return $next($request);
        }
        return response()->json([
            'status' => false,
            'message' => 'You need to purchase this level to access its content.',
        ], 403);
    }

}
