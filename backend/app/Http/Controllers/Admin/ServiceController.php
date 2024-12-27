<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('long_description', 'like', "%{$search}%");
            })
            ->when(request('category'), function($query, $category) {
                $query->where('category_id', $category);
            })
            ->latest()
            ->paginate(10);

        $categories = ServiceCategory::all();
        return view('admin.services.index', compact('services', 'categories'));
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
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'icon_name' => 'required|string',
            'is_featured' => 'boolean'
        ]);

        try {
            Service::create($validated);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Service created successfully',
                    'redirect' => route('admin.services.index')
                ]);
            }
            
            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Failed to create service: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Failed to create service: ' . $e->getMessage())
                ->withInput();
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
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'icon_name' => 'required|string',
            'is_featured' => 'boolean'
        ]);

        try {
            $service->update($validated);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Service updated successfully',
                    'redirect' => route('admin.services.index')
                ]);
            }
            
            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Failed to update service: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Failed to update service: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted successfully');
    }

    public function toggleFeatured(Service $service)
    {
        $service->update([
            'is_featured' => !$service->is_featured
        ]);

        return response()->json([
            'success' => true,
            'featured' => $service->is_featured
        ]);
    }
} 