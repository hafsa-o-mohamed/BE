<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ElectricityBill;
use App\Models\Apartment;
use App\Models\Bill;
use Illuminate\Http\Request;

class ElectricityBillController extends Controller
{
    public function index(Building $building)
    {
        $buildings = Building::all();
        $electricityBills = ElectricityBill::orderBy('created_at', 'desc')->get();
        return view('dashboard.buildings.electricity.index', compact('buildings', 'electricityBills'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'subtracted_amount' => 'required|numeric',
        ]);

        $validated['default_balance'] = 1000;

        // Get the latest water bill for this building in the current month
        $previousBill = ElectricityBill::where('building_id', $validated['building_id'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()
            ->first();

        if ($previousBill) {
            // If there's a previous bill this month, subtract from its current balance
            $validated['current_balance'] = $previousBill->current_balance - $validated['subtracted_amount'];
        } else {
            // If no previous bill this month, subtract from default balance
            $validated['current_balance'] = $validated['default_balance'] - $validated['subtracted_amount'];
        }

        // Create the main electricity bill
        $electricityBill = ElectricityBill::create($validated);

        // Get all unique owners in this building
        $owners = Apartment::where('building_id', $validated['building_id'])
            ->whereNotNull('owner_id')
            ->distinct()
            ->pluck('owner_id');

        // Calculate amount per owner
        $amountPerOwner = $validated['subtracted_amount'] / $owners->count();

        // Create individual bills for each owner
        foreach ($owners as $ownerId) {
            Bill::create([
                'owner_id' => $ownerId,
                'bill_type' => 'electricity',
                'due_amount' => $amountPerOwner,
                'status' => 'pending',
                'description' => 'Electricity Bill for ' . now()->format('F Y'),
                'due_date' => now()->addDays(30),
                'reference_id' => $electricityBill->id,
                'reference_type' => ElectricityBill::class
            ]);
        }

        return redirect()->back()->with('success', 'Electricity bill added and distributed successfully');
    }



    public function update(Request $request, ElectricityBill $electricityBill)
    {
        $validated = $request->validate([
            'default_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'subtracted_amount' => 'required|numeric',
        ]);

        $electricityBill->update($validated);

        return redirect()->back()->with('success', 'Electricity bill updated successfully');
    }

    public function destroy(ElectricityBill $electricityBill)
    {
        $electricityBill->delete();
        return redirect()->back()->with('success', 'Electricity bill deleted successfully');
    }
    public function getLastElectricityBill(Request $request)
    {
        $buildingId = $request->input('building_id');

        $defaultBalance = 1000; // Hard-coded default balance

        return response()->json([
            'current_balance' => '100',
        ]);
    }

}