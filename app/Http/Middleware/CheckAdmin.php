<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->isAdmin()) {
            return $next($request);
        }

        // لو مش أدمن، نرجّع خطأ 403
        return response()->json(['message' => 'عذراً، هذه الصلاحية للمدير فقط!'], 403);
    }
}