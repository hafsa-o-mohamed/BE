<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Project;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::with('project')->latest()->paginate(10);
        return view('dashboard.buildings.index', compact('buildings'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('dashboard.buildings.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'building_name' => 'required|string|max:255',
            'number_of_floors' => 'required|integer|min:1',
            'number_of_apartments' => 'required|integer|min:1',
        ]);

        Building::create($validated);
        return redirect()->route('buildings.index')->with('success', 'Building created successfully');
    }

    public function getByProject(Project $project)
    {
        return response()->json($project->buildings);
    }

    public function edit(Building $building)
    {
        $projects = Project::all();
        return view('dashboard.buildings.edit', compact('building', 'projects'));
    }

    public function destroy(Building $building)
    {
        try {
            $building->delete();
            return redirect()->route('buildings.index')->with('success', 'Building deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('buildings.index')->with('error', 'Failed to delete building');
        }
    }

    public function show(Building $building)
    {
        $building = $building->load([
            'waterBills' => function ($query) {
                $query->where('current_balance', '<', 0)
                      ->orderBy('created_at', 'desc');
            },
            'electricityBills' => function ($query) {
                $query->where('current_balance', '<', 0)
                      ->orderBy('created_at', 'desc');
            },
        ]);
    
        return view('dashboard.buildings.show', compact('building'));
    }
}
