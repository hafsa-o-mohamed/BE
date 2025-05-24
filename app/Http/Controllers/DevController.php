<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;

class DevController extends Controller
{
    /**
     * Show the dev login page
     */
    public function login()
    {
        // If already authenticated, redirect to dev dashboard
        $devToken = request()->cookie('dev_access_token');
        if ($devToken === hash('sha256', 'SuperSecretPassword123!')) {
            return redirect()->route('dev.index');
        }

        return view('dev.login');
    }

    /**
     * Authenticate dev access
     */
    public function authenticate(Request $request)
    {
        $password = $request->input('password');
        
        if ($password === 'SuperSecretPassword123!') {
            $token = hash('sha256', 'SuperSecretPassword123!');
            $cookie = Cookie::make('dev_access_token', $token, 60 * 24 * 7); // 7 days
            
            return redirect()->route('dev.index')->withCookie($cookie);
        }
        
        return back()->withErrors(['password' => 'Invalid password']);
    }

    /**
     * Show the dev dashboard
     */
    public function index()
    {
        $serverTime = Carbon::now()->format('Y-m-d H:i:s T');
        
        return view('dev.index', compact('serverTime'));
    }

    /**
     * Logout from dev area
     */
    public function logout()
    {
        $cookie = Cookie::forget('dev_access_token');
        
        return redirect()->route('dev.login')->withCookie($cookie);
    }
} 