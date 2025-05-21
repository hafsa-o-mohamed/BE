<?php

namespace App\Http\Controllers;

use App\Models\ApartmentOwner;
use App\Models\User;
use Illuminate\Http\Request;

class ApartmentOwnerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        // If email is provided, check if a user exists with this email
        $user = null;
        if ($validated['email']) {
            $user = User::where('email', $validated['email'])->first();
        }

        // Create apartment owner
        $owner = ApartmentOwner::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'user_id' => $user ? $user->id : null,
        ]);

        return redirect()->back()->with('success', 'Apartment owner added successfully');
    }

    public function index()
    {
        $owners = ApartmentOwner::all();
        return view('dashboard.owners.index', compact('owners'));
    }

    public function show($id)
    {
        $owner = ApartmentOwner::findOrFail($id);
        
        // Calculate totals
        $waterTotal = $owner->bills()
            ->where('bill_type', 'water')
            ->sum('due_amount');

        $electricityTotal = $owner->bills()
            ->where('bill_type', 'electricity')
            ->sum('due_amount');

        $serviceTotal = $owner->serviceRequests()
            ->sum('due_price');

        return view('dashboard.owners.show', compact(
            'owner',
            'waterTotal',
            'electricityTotal',
            'serviceTotal'
        ));
    }

    public function create()
    {
        return view('dashboard.owners.create');
    }

    public function edit($id)
    {
        $owner = ApartmentOwner::findOrFail($id);
        return view('dashboard.owners.edit', compact('owner'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $owner = ApartmentOwner::findOrFail($id);
        
        // If email is changed, update user_id if necessary
        if ($validated['email'] !== $owner->email) {
            $user = null;
            if ($validated['email']) {
                $user = User::where('email', $validated['email'])->first();
            }
            $validated['user_id'] = $user ? $user->id : null;
        }

        $owner->update($validated);

        return redirect()->route('owners.show', $owner->id)
            ->with('success', 'Apartment owner updated successfully');
    }

    public function destroy($id)
    {
        $owner = ApartmentOwner::findOrFail($id);
        $owner->delete();

        return redirect()->route('owners.index')
            ->with('success', 'Apartment owner deleted successfully');
    }
}