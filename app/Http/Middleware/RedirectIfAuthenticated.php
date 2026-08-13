<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($request->is('admin') || $request->is('admin/*')) {
                    return redirect()->route(
                        $user && method_exists($user, 'isAdmin') && $user->isAdmin()
                            ? 'admin.dashboard'
                            : 'portal'
                    );
                }

                if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
