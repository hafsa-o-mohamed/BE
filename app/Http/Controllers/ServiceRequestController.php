<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceService;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        // Get all services for the filter dropdown
        $services = \App\Models\MaintenanceService::all();

        // Build the query with filters
        $serviceRequests = ServiceRequest::with(['service', 'apartment', 'owner', 'user'])
            ->when(request('service'), function ($query, $service) {
                return $query->where('service_id', $service);
            })
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.service-requests.index', compact('serviceRequests', 'services'));
    }

    public function duePayments()
    {
        $duePayments = ServiceRequest::with(['service', 'apartment', 'owner', 'user'])
            ->where('status', 'completed')
            ->get();
        return view('dashboard.services.duepayments', compact('duePayments'));
    }

    
}