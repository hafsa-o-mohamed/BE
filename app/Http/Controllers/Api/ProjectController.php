<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,completed,on_hold',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $project = Project::create($validated);
        return response()->json($project, Response::HTTP_CREATED);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        return response()->json($project);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:active,completed,on_hold',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
