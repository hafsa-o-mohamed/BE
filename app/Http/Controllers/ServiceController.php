<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::paginate(5); // Show 10 items per page
        return view('dashboard.services.index', compact('services'));
    

    }

    public function create()
    {
        return view('dashboard.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|max:255',
            'description' => 'required'
        ]);

        Service::create($request->all());
        return redirect()->route('services.index')->with('success', 'Service created successfully');
    }

    public function edit(Service $service)
    {
        return view('dashboard.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'service_name' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['service_name', 'description']);
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->img_url && file_exists(public_path($service->img_url))) {
                unlink(public_path($service->img_url));
            }
            
            // Store the new image
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image_url'] = 'storage/' . $imagePath;
        }

        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Service updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('dashboard.services.index')->with('success', 'Service deleted successfully');
    }
}