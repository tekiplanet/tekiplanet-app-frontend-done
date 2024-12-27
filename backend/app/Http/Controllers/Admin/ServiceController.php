<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255|unique:services',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'icon_name' => 'required|string',
            'is_featured' => 'boolean'
        ]);

        try {
            Service::create($validated);
            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create service: ' . $e->getMessage());
        }
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::all();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'icon_name' => 'required|string',
            'is_featured' => 'boolean'
        ]);

        try {
            $service->update($validated);
            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update service: ' . $e->getMessage());
        }
    }

    public function destroy(Service $service)
    {
        try {
            $service->delete();
            return back()->with('success', 'Service deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }
} 