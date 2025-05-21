<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('dashboard.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,accountant,owner'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User role updated successfully');
    }
}
