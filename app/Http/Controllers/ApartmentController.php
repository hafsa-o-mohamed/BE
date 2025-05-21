<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\ApartmentOwner;
use App\Models\Bill;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function index()
    {
        $apartments = Apartment::with(['building', 'owner'])->latest()->paginate(10);
        return view('dashboard.apartments.index', compact('apartments'));
    }

    public function create()
    {
        $buildings = Building::all();
        $owners = ApartmentOwner::all();
        return view('dashboard.apartments.create', compact('buildings', 'owners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_number' => 'required|integer',
            'apartment_number' => 'required|string|max:255',
            'owner_id' => 'nullable|exists:apartment_owners,id',
        ]);

        Apartment::create($validated);
        return redirect()->route('apartments.index')->with('success', 'Apartment created successfully');
    }

    public function getByBuilding(Building $building)
    {
        return response()->json($building->apartments);
    }

    public function destroy($id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartment->delete();

        return redirect()->route('apartments.index')
            ->with('success', 'Apartment deleted successfully');
    }

    public function show(Apartment $apartment)
    {
        // Load the apartment with its relationships
        $apartment->load([
            'building',
            'owner',
            'owner.bills' => function($query) {
                $query->latest();
            },
            'owner.serviceRequests' => function($query) {
                $query->with('service')->latest();
            }
        ]);

        // Calculate totals
        $waterBills = $apartment->owner->bills->where('bill_type', 'water');
        $electricityBills = $apartment->owner->bills->where('bill_type', 'electricity');
        $serviceRequests = $apartment->owner->serviceRequests;

        $totals = [
            'water' => $waterBills->sum('due_amount'),
            'electricity' => $electricityBills->sum('due_amount'),
            'services' => $serviceRequests->sum('due_price'),
        ];

        $totals['total'] = $totals['water'] + $totals['electricity'] + $totals['services'];

        return view('dashboard.apartments.show', compact('apartment', 'waterBills', 'electricityBills', 'serviceRequests', 'totals'));
    }
}