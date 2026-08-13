<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                abort(403, 'Admin access required.');
            }

            if ($user) {
                return redirect()->route('portal');
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
