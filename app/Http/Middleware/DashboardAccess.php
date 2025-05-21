<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class DashboardAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasAccessToDashboard()) {
            return redirect('/')->withErrors([
                'email' => 'You do not have access to the dashboard.',
            ]);
        }

        return $next($request);
    }
}