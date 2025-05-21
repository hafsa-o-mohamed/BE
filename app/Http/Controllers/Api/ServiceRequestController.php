<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use App\Models\Apartment;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner->id;  // Get the ApartmentOwner ID through the relationship

        // Get count from service_requests table
        $Services = ServiceRequest::with(['service'])
            ->where('owner_id', $ownerId)
            ->where('payment_status', 'unpaid')
            ->get();
        
    return response()->json($Services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'service_id' => 'required|exists:services,id',
            'request_date' => 'required|date',
        ]);

        $ownerId = $request->user()->owner->id;

        $serviceRequest = ServiceRequest::create([
            'owner_id' => $ownerId,
            'apartment_id' => $validated['apartment_id'],
            'service_id' => $validated['service_id'],
            'request_date' => $validated['request_date'],
            'status' => 'Pending',
            'due_price' => 0,
        ]);

        return response()->json([
            'message' => 'Request created',
            'request' => $serviceRequest->load(['owner', 'apartment', 'service'])
        ], 201);
    }

    public function update(Request $request, $request_id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Completed',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request_id);
        $serviceRequest->update($validated);

        return response()->json([
            'message' => 'Request updated',
            'request' => $serviceRequest->load(['owner', 'apartment', 'service'])
        ]);
    }

    public function getContractServices(Request $request)
    {
        $user = $request->user();
        $contract = $user->contracts()->latest()->first();

        if (!$contract) {
            return response()->json(['message' => 'No active contract found'], 404);
        }

        $services = $contract->services()->with([
            'serviceRequests' => function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            }
        ])->get();

        return response()->json([
            'contract' => $contract,
            'services' => $services
        ]);
    }

    
    public function unpaidServices(Request $request)
    {
        $unpaidServices = ServiceRequest::with(['service', 'apartment', 'owner'])
            ->whereHas('owner', function($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->where('status', 'completed')
            ->where('payment_status', 'unpaid')
            ->get();

        return response()->json($unpaidServices);
    }
} 

