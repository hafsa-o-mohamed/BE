<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BuildingController extends Controller
{
    /**
     * Display a listing of buildings.
     */
    public function index()
    {
        $buildings = Building::all();
        return response()->json($buildings);
    }

    /**
     * Store a newly created building.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'type' => 'required|string|in:residential,commercial,industrial',
            'status' => 'required|string|in:active,inactive,under_construction',
            'floors' => 'required|integer|min:1',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'description' => 'nullable|string',
        ]);

        $building = Building::create($validated);
        return response()->json($building, Response::HTTP_CREATED);
    }

    /**
     * Display the specified building.
     */
    public function show(Building $building)
    {
        return response()->json($building);
    }

    /**
     * Update the specified building.
     */
    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:residential,commercial,industrial',
            'status' => 'sometimes|string|in:active,inactive,under_construction',
            'floors' => 'sometimes|integer|min:1',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'description' => 'nullable|string',
        ]);

        $building->update($validated);
        return response()->json($building);
    }

    /**
     * Remove the specified building.
     */
    public function destroy(Building $building)
    {
        $building->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
