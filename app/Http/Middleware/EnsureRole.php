<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:tenant') or ->middleware('role:landlord')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            // Send them to their correct dashboard instead
            $target = Auth::user()->isLandlord()
                ? 'landlord.dashboard'
                : 'tenant.dashboard';

            return redirect()->route($target);
        }

        return $next($request);
    }
}