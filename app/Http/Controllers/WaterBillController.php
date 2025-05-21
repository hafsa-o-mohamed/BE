<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\WaterBill;
use App\Models\Apartment;
use App\Models\Bill;
use Illuminate\Http\Request;

class WaterBillController extends Controller
{
    public function index(Building $building)
    {
        $buildings = Building::all();
        $waterBills = WaterBill::orderBy('created_at', 'desc')->get();
        return view('dashboard.water.index', compact('buildings', 'waterBills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'subtracted_amount' => 'required|numeric',
        ]);

        $validated['default_balance'] = 500;

        // Get the latest water bill for this building in the current month
        $previousBill = WaterBill::where('building_id', $validated['building_id'])
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

        // Create the main water bill
        $waterBill = WaterBill::create($validated);

        // Get all apartments in this building that have owners
        $apartments = Apartment::where('building_id', $validated['building_id'])
            ->whereNotNull('owner_id')
            ->get();

        // Calculate amount per apartment
        $amountPerApartment = $validated['subtracted_amount'] / $apartments->count();

        // Create individual bills for each apartment
        foreach ($apartments as $apartment) {
            Bill::create([
                'owner_id' => $apartment->owner_id,
                'apartment_id' => $apartment->id,
                'bill_type' => 'water',
                'due_amount' => $amountPerApartment,
                'status' => 'pending',
                'description' => 'Water Bill for ' . now()->format('F Y'),
                'due_date' => now()->addDays(30),
                'reference_id' => $waterBill->id,
                'reference_type' => WaterBill::class
            ]);
        }

        return redirect()->back()->with('success', 'Water bill added and distributed successfully');
    }

    public function update(Request $request, WaterBill $waterBill)
    {
        $validated = $request->validate([
            'default_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'subtracted_amount' => 'required|numeric',
        ]);

        $waterBill->update($validated);

        return redirect()->back()->with('success', 'Water bill updated successfully');
    }

    public function destroy(WaterBill $waterBill)
    {
        $waterBill->delete();
        return redirect()->back()->with('success', 'Water bill deleted successfully');
    }

    public function getLastWaterBill(Request $request)
    {
        $buildingId = $request->input('building_id');
        $lastBill = WaterBill::where('building_id', $buildingId)
            ->latest()
            ->first();

        $defaultBalance = 500; // Hard-coded default balance

        return response()->json([
            'current_balance' => $lastBill ? $lastBill->current_balance : $defaultBalance,
            'is_first_bill' => !$lastBill // Add flag to indicate if this is the first bill
        ]);
    }

}