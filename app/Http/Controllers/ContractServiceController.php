<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Service;
use App\Models\ContractService;
use Illuminate\Http\Request;

class ContractServiceController extends Controller
{
    public function store(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'frequency' => 'required|in:monthly,yearly,quarterly,daily,biannually'
        ]);

        ContractService::create([
            'contract_id' => $contract->id,
            'service_id' => $validated['service_id'],
            'frequency' => $validated['frequency']
        ]);

        return redirect()->back()->with('success', 'Service added successfully');
    }
}