<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::select('id', 'service_name as title', 'description', 'image_url as imageUrl')
            ->get()
            ->map(function ($service) {
                // Ensure imageUrl is not null
                $service->imageUrl = $service->imageUrl ?? 'https://via.placeholder.com/300x200?text=Service';
                return $service;
            });

        return response()->json($services);
    }

    public function show($id)   
    {
        $service = Service::find($id);
        return response()->json($service);
    }
}
