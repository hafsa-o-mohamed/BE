<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contract;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts.
     */
    public function index()
    {
        $contracts = Contract::with(['project', 'building', 'services'])
            ->where('owner_id', auth()->id())
            ->paginate(10);

        return response()->json($contracts);
    }

    /**
     * Store a newly created contract.
     */
    public function store(Request $request)
    {
        // TODO: Add validation and contract creation
        return response()->json([
            'message' => 'Contract created',
            'data' => $request->all()
        ], 201);
    }

    /**
     * Display the specified contract.
     */
    public function show($id)
    {
        $contract = Contract::with(['project', 'building', 'services'])
            ->where('owner_id', auth()->id())
            ->findOrFail($id);

        return response()->json($contract);
    }

    /**
     * Update the specified contract.
     */
    public function update(Request $request, $id)
    {
        // TODO: Add validation and update logic
        return response()->json([
            'message' => 'Contract updated',
            'data' => ['id' => $id] + $request->all()
        ]);
    }

    /**
     * Remove the specified contract.
     */
    public function destroy($id)
    {
        // TODO: Add deletion logic
        return response()->json([
            'message' => 'Contract deleted',
            'data' => ['id' => $id]
        ]);
    }
}
