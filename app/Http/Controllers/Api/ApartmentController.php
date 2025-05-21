<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApartmentController extends Controller
{
    /**
     * Display a listing of apartments.
     */
    public function index(): JsonResponse
    {
        $apartments = Apartment::paginate(10);

        return response()->json($apartments);
    }

    /**
     * Store a newly created apartment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'price' => 'required|numeric',
            'rooms' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $apartment = Apartment::create($validated);
        return response()->json($apartment, 201);
    }

    /**
     * Display the specified apartment.
     */
    public function show(Apartment $apartment): JsonResponse
    {
        return response()->json($apartment);
    }

    /**
     * Update the specified apartment.
     */
    public function update(Request $request, Apartment $apartment): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'rooms' => 'sometimes|integer',
            'description' => 'nullable|string',
        ]);

        $apartment->update($validated);
        return response()->json($apartment);
    }

    /**
     * Remove the specified apartment.
     */
    public function destroy(Apartment $apartment): JsonResponse
    {
        $apartment->delete();
        return response()->json(null, 204);
    }
}
