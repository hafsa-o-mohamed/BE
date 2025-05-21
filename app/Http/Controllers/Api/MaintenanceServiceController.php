<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceService;
use Illuminate\Http\Request;

class MaintenanceServiceController extends Controller
{
    public function index()
    {
        $services = MaintenanceService::select('id', 'name', 'image_url as imageUrl')
            ->get();

        return response()->json($services);
    }

    public function show($id)   
    {
        $service = MaintenanceService::find($id);
        return response()->json($service);
    }
}
