<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\Apartment;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['apartment.building', 'owner'])->paginate(15);
        $owners = \App\Models\ApartmentOwner::all();
        $buildings = Building::all();
        $contracts = \App\Models\Contract::all();
        
        return view('dashboard.bills.index', compact('bills', 'owners', 'buildings', 'contracts'));
    }

    public function create()
    {
        $users = \App\Models\User::all();
        $buildings = Building::all();
        $contracts = \App\Models\Contract::all();
        
        return view('bills.create', compact('users', 'buildings', 'contracts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'bill_type' => 'required|string',
            'due_amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,unpaid,overdue',
        ]);

        Bill::create($validated);

        return redirect()->route('bills.index')
            ->with('success', 'Bill created successfully.');
    }

    public function show(Bill $bill)
    {
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        return view('bills.edit', compact('bill'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'bill_type' => 'required|string',
            'due_amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,unpaid,overdue',
        ]);

        $bill->update($validated);

        return redirect()->route('bills.index')
            ->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted successfully.');
    }

    public function createContractBills()
    {
        try {
            // Get all active contracts with their buildings and apartments
            $contracts = \App\Models\Contract::with(['building.apartments.owner'])
                ->where('status', 'active')
                ->get();

            $createdBills = 0;
            $processedOwners = [];

            foreach ($contracts as $contract) {
                // Get unique owners from the building's apartments
                foreach ($contract->building->apartments as $apartment) {
                    if ($apartment->owner && !in_array($apartment->owner->id, $processedOwners)) {
                        // Create a new bill for each unique owner
                        Bill::create([
                            'owner_id' => $apartment->owner->id,
                            'building_id' => $contract->building_id,
                            'bill_type' => 'contract',
                            'due_amount' => $contract->yearly_price / 12, // Convert yearly price to monthly
                            'status' => 'pending',
                            'due_date' => now()->addDays(30), // Set due date to 30 days from now
                        ]);
                        $processedOwners[] = $apartment->owner->id;
                        $createdBills++;
                    }
                }
            }

            return redirect()->route('buildings.index')
                ->with('success', "Successfully created {$createdBills} bills");
        } catch (\Exception $e) {
            return redirect()->route('buildings.index')
                ->with('error', 'Error creating bills: ' . $e->getMessage());
        }
    }

    public function createBillFromModal(Request $request)
    {
        $validated = $request->validate([
            'bill_type' => 'required|in:contract,electricity,water',
            'target_type' => 'required|in:owner,building',
            'owner_id' => 'required_if:target_type,owner',
            'building_id' => 'required_if:target_type,building',
            'contract_type' => 'required_if:bill_type,contract',
            'amount' => 'required_unless:bill_type,contract|numeric|min:0',
        ]);

        try {
            if ($request->bill_type === 'contract') {
                if ($request->target_type === 'building') {
                    return $this->createContractBills($request->building_id);
                } else {
                    $contract = \App\Models\Contract::find($request->contract_type);
                    $monthlyAmount = $contract->yearly_price / 12;
                    
                    Bill::create([
                        'owner_id' => $request->owner_id,
                        'bill_type' => 'contract',
                        'due_amount' => $monthlyAmount,
                        'status' => 'pending',
                        'due_date' => now()->addDays(30),
                    ]);
                }
            } else {
                if ($request->target_type === 'owner') {
                    Bill::create([
                        'owner_id' => $request->owner_id,
                        'bill_type' => $request->bill_type,
                        'due_amount' => $request->amount,
                        'status' => 'pending',
                        'due_date' => now()->addDays(30),
                    ]);
                } else {
                    $building = Building::with('apartments.owner')->find($request->building_id);
                    
                    // Count the number of owners in the building
                    $ownerCount = $building->apartments->filter(function($apartment) {
                        return $apartment->owner !== null;
                    })->count();
                    
                    // Calculate amount per owner
                    $amountPerOwner = $request->amount / $ownerCount;
                    
                    foreach ($building->apartments as $apartment) {
                        if ($apartment->owner) {
                            Bill::create([
                                'owner_id' => $apartment->owner->id,
                                'bill_type' => $request->bill_type,
                                'due_amount' => $amountPerOwner,
                                'status' => 'pending',
                                'due_date' => now()->addDays(30),
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('bills.index')
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('bills.index')
                ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        // Start with a query for Bill, eager loading the apartment relationship
        $bills = Bill::with(['apartment.building', 'owner']);
    
        // Filter by owner_id if provided
        if ($request->filled('owner_id')) {
            $bills = $bills->where('owner_id', $request->owner_id);
        }
    
        // Filter by bill_type if provided
        if ($request->filled('bill_type')) {
            $bills = $bills->where('bill_type', $request->bill_type);
        }
    
        // Filter by status if provided
        if ($request->filled('status')) {
            $bills = $bills->where('status', $request->status);
        }
    
        // Filter by building_id using owner IDs from apartments
        if ($request->filled('building_id')) {
            $ownerIds = Apartment::where('building_id', $request->building_id)
                ->whereNotNull('owner_id')
                ->pluck('owner_id')
                ->unique();
            
            $bills = $bills->whereIn('owner_id', $ownerIds);
        }
    
        // Get the results with pagination
        $page = $request->get('page', 1);
        $perPage = 15;
        $paginatedBills = $bills->paginate($perPage);
    
        // Retrieve other data (owners, buildings, contracts) for the view
        $owners = \App\Models\ApartmentOwner::all();
        $buildings = \App\Models\Building::all();
        $contracts = \App\Models\Contract::all();
    
        // Return the view with paginated bills and other data
        return view('dashboard.bills.index', [
            'bills' => $paginatedBills,
            'owners' => $owners,
            'buildings' => $buildings,
            'contracts' => $contracts
        ]);
    }
    
}