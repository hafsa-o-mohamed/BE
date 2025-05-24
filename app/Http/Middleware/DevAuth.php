<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $devToken = $request->cookie('dev_access_token');
        
        // Check if the token matches our secret
        if ($devToken !== hash('sha256', 'SuperSecretPassword123!')) {
            return redirect()->route('dev.login');
        }

        return $next($request);
    }
} 