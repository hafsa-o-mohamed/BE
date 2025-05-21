<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Apartment;
use App\Models\Building;
use App\Models\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with('building')
            ->latest()
            ->paginate(10);
            
        return view('dashboard.contracts.index', compact('contracts'));
    }



    public function create()
    {
        $buildings = Building::all(); // or any other query to get your buildings
        return view('dashboard.contracts.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'yearly_price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required',
            'contract_type' => 'nullable',
            'duration' => 'nullable',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.frequency' => 'required|in:monthly,yearly,quarterly,daily,biannually',
        ]);

        $contract = Contract::create([
            'building_id' => $validated['building_id'],
            'yearly_price' => $validated['yearly_price'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'contract_type' => $validated['contract_type'],
            'duration' => $validated['duration'],
        ]);

        // Attach services with frequencies if provided
        if (!empty($validated['services'])) {
            foreach ($validated['services'] as $service) {
                ContractService::create([
                    'contract_id' => $contract->id,
                    'service_id' => $service['service_id'],
                    'frequency' => $service['frequency']
                ]);
            }
        }

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Contract created successfully.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['building', 'contractServices.service']);    
        return view('dashboard.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $buildings = Building::with(['project'])->get();
        return view('dashboard.contracts.edit', compact('contract', 'buildings'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'yearly_price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,pending,terminated',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.frequency' => 'required|in:monthly,yearly,quarterly,daily,biannually',
        ]);

        $contract->update([
            'building_id' => $validated['building_id'],
            'yearly_price' => $validated['yearly_price'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
        ]);

        // Update services if provided
        if (isset($validated['services'])) {
            // Remove existing services
            ContractService::where('contract_id', $contract->id)->delete();
            
            // Create new services
            foreach ($validated['services'] as $service) {
                ContractService::create([
                    'contract_id' => $contract->id,
                    'service_id' => $service['service_id'],
                    'frequency' => $service['frequency']
                ]);
            }
        }

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }
} 