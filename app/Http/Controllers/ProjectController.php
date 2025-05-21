<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        
        if ($projects->isEmpty()) {
            return view('dashboard.projects.index', compact('projects'))->with('info', 'No projects found.');
        }
        
        return view('dashboard.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('dashboard.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Project::create($validated);
        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}
