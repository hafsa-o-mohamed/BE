<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\Apartment;
use Illuminate\Http\Request;

class ContractServicesController extends Controller
{
    public function getActiveContract(Request $request)
    {
        $user = $request->user();
        
        // Check if user is an apartment owner
        $apartmentOwner = $user->owner;
        
        if (!$apartmentOwner) {
            return response()->json([
                'message' => 'User is not an apartment owner'
            ], 403);
        }
        
        // Get apartment associated with the owner
        $apartment = Apartment::where('owner_id', $apartmentOwner->id)->first();
        
        if (!$apartment) {
            return response()->json([
                'message' => 'Apartment owner is not associated with any apartment'
            ], 403);
        }

        // Find contract for the building
        $contract = Contract::where('building_id', $apartment->building_id)
            ->with(['building', 'contractServices.service'])
            ->first();

        if (!$contract) {
            return response()->json([
                'message' => 'No active contract found for this building'
            ], 404);
        }

        // Get contract services with proper relationship
        $contractServices = $contract->contractServices->map(function ($contractService) {
            return [
                'id' => $contractService->id,
                'name' => $contractService->service->service_name,
                'description' => $contractService->service->description,
                'quantity' => $contractService->quantity,
                'frequency' => $contractService->frequency,          // Original frequency value
                'frequency_text' => $contractService->frequency_text // Arabic translation
            ];
        });

        return response()->json([
            'contract' => [
                'id' => $contract->id,
                'buildingName' => $contract->building->building_name,
                'startDate' => $contract->start_date,
                'endDate' => $contract->end_date,
                'services' => $contractServices
            ]
        ]);
    }
} 