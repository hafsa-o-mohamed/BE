<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Suggestion;
use App\Models\WaterBill;
use App\Models\Building;
use App\Models\ElectricityBill;
use App\Models\ApartmentOwner;
use App\Models\Contract;
class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware to ensure only admins can access these endpoints
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return $next($request);
        });
    }

    public function getServiceRequests()
    {
        $requests = ServiceRequest::with(['owner.user', 'service'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'customerName' => $request->owner->user->name,
                    'service' => $request->service->name,
                    'status' => strtolower($request->status),
                    'date' => $request->request_date,
                    'amount' => $request->due_price,
                ];
            });

        return response()->json($requests);
    }

    public function updateServiceStatus(Request $request, $requestId)
    {
        $validated = $request->validate([
            'status' => 'required|in:requested,pending,completed,paid'
        ]);

        $serviceRequest = ServiceRequest::findOrFail($requestId);
        $serviceRequest->update([
            'status' => ucfirst($validated['status']),
            'payment_status' => $validated['status'] === 'paid' ? 'paid' : 'unpaid'
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'request' => $serviceRequest
        ]);
    }

    public function getServiceStats()
    {
        $stats = [
            'total' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', 'Pending')->count(),
            'completed' => ServiceRequest::where('status', 'Completed')->count(),
            'paid' => ServiceRequest::where('payment_status', 'paid')->count(),
            'revenue' => ServiceRequest::where('payment_status', 'paid')
                ->sum('due_price'),
        ];

        return response()->json($stats);
    }

    public function getProjectsWithBuildings()
    {
        try {
            $projects = Project::with(['buildings' => function ($query) {
                $query->with([
                    'waterBills' => function ($q) {
                        $q->latest()->limit(1);
                    },
                    'electricityBills' => function ($q) {
                        $q->latest()->limit(1);
                    }
                ]);
            }])->get();

            $formattedProjects = $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->project_name,
                    'address' => $project->address,
                    'buildings' => $project->buildings->map(function ($building) {
                        return [
                            'id' => $building->id,
                            'name' => $building->building_name,
                            'projectId' => $building->project_id,
                            'latestWaterBill' => $building->waterBills->first() ? [
                                'amount' => $building->waterBills->first()->current_balance,
                                'date' => $building->waterBills->first()->date
                            ] : null,
                            'latestElectricityBill' => $building->electricityBills->first() ? [
                                'amount' => $building->electricityBills->first()->current_balance,
                                'date' => $building->electricityBills->first()->date
                            ] : null
                        ];
                    })->values()->all()
                ];
            })->values()->all();

            return response()->json([
                'success' => true,
                'data' => $formattedProjects
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getProjectsWithBuildings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLatestServiceRequests()
    {
        $requests = ServiceRequest::with(['owner.user', 'service'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'customerName' => $request->owner->user->name,
                    'service' => $request->service->name,
                    'status' => strtolower($request->status),
                    'date' => $request->created_at->format('Y-m-d'),
                ];
            });

        return response()->json($requests);
    }

    public function getBuildingsWithNegativeBills()
    {
        $buildings = DB::table('buildings')
            ->join('water_bills', 'buildings.id', '=', 'water_bills.building_id')
            ->join('electricity_bills', 'buildings.id', '=', 'electricity_bills.building_id')
            ->where('water_bills.current_balance', '<', 0)
            ->orWhere('electricity_bills.current_balance', '<', 0)
            ->select(
                'buildings.id',
                'buildings.building_name',
                'water_bills.current_balance as water_balance',
                'electricity_bills.current_balance as electricity_balance'
            )
            ->orderBy('water_bills.created_at', 'desc')
            ->take(4)
            ->get();

        return response()->json($buildings);
    }

    public function getLatestProjects()
    {
        $projects = Project::latest()
            ->take(4)
            ->get(['id', 'project_name', 'address', 'created_at']);

        return response()->json($projects);
    }

    public function getUnresolvedItems()
    {
        $complaints = Suggestion::where('type', 'complaint')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->select([
                'id',
                'content as text',
                'created_at as date',
                'status'
            ])
            ->get();

        $suggestions = Suggestion::where('type', 'suggestion')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->select([
                'id',
                'content as text',
                'created_at as date',
                'status'
            ])
            ->get();

        return response()->json([
            'complaints' => $complaints,
            'suggestions' => $suggestions
        ]);
    }

    public function getBuildingWaterBills($id)
    {
        $waterBills = WaterBill::where('building_id', $id)->get();
        return response()->json($waterBills);
    }

    public function getBuildingElectricityBills($id)
    {
        $electricityBills = ElectricityBill::where('building_id', $id)->get();
        return response()->json($electricityBills);
    }   

    public function getBuilding($id)
    {
        $building = Building::find($id);
        return response()->json($building);
    }

    public function getBuildingOwners($id)
    {
        $owners = Apartment::where('building_id', $id)
            ->with('owner')
            ->get()
            ->map(function ($apartment) {
                return [ 
                    'apartment_id' => $apartment->id,
                    'apartment_number' => $apartment->apartment_number,
                    'owner_id' => $apartment->owner->id,
                    'owner_name' => $apartment->owner->name,
                ];
            });

        return response()->json($owners);
    }

 
    public function getBuildingContract($id)
    {
        // Find contract for the building
        $contract = Contract::where('building_id', $id)
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
                'frequency' => $contractService->frequency,
                'frequency_text' => $this->getFrequencyText($contractService->frequency)
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

    // Helper function to convert frequency to Arabic text
    private function getFrequencyText($frequency)
    {
        $frequencyMap = [
            'daily' => 'يومياً',
            'weekly' => 'أسبوعياً',
            'monthly' => 'شهرياً',
            'quarterly' => 'كل ثلاثة أشهر',
            'yearly' => 'سنوياً'
        ];

        return $frequencyMap[$frequency] ?? $frequency;
    }
}