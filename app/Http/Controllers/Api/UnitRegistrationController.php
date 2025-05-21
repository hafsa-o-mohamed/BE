<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\Building;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ApartmentOwner;
class UnitRegistrationController extends Controller
{
    
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'building_name' => 'required|string|max:255',
            'floor_number' => 'required|integer|min:1',
            'apartment_number' => 'required|string|max:50',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Find or create the building
            $building = Building::firstOrCreate(
                [
                    'project_id' => $request->project_id,
                    'building_name' => $request->building_name
                ],
                [
                    'number_of_floors' => $request->floor_number,
                    'number_of_apartments' => 1
                ]
            );

            if (!$building->wasRecentlyCreated) {
                $building->number_of_apartments += 1;
                if ($building->number_of_floors < $request->floor_number) {
                    $building->number_of_floors = $request->floor_number;
                }
                $building->save();
            }

            // 2. Create the owner0
            $owner = ApartmentOwner::create([
                'name' => $request->owner_name,
                'phone' => $request->owner_phone,
                'user_id' => auth()->id()
            ]);

            // 3. Create the apartment with owner_id
            $apartment = Apartment::create([
                'building_id' => $building->id,
                'floor_number' => $request->floor_number,
                'apartment_number' => $request->apartment_number,
                'owner_id' => $owner->id,
                'status' => 'available'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Unit registered successfully',
                'data' => [
                    'building' => $building,
                    'owner' => $owner,
                    'apartment' => $apartment
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error registering unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
