<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->latest()->paginate(10);
        return view('admin.services.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.services.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories',
            'description' => 'required|string',
            'icon_name' => 'required|string',
            'is_featured' => 'boolean'
        ]);

        try {
            ServiceCategory::create($validated);
            return redirect()
                ->route('admin.service-categories.index')
                ->with('success', 'Service category created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create service category: ' . $e->getMessage());
        }
    }

    public function edit(ServiceCategory $category)
    {
        return view('admin.services.categories.edit', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $category->id,
            'description' => 'required|string',
            'icon_name' => 'required|string',
            'is_featured' => 'boolean'
        ]);

        try {
            $category->update($validated);
            return redirect()
                ->route('admin.service-categories.index')
                ->with('success', 'Service category updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update service category: ' . $e->getMessage());
        }
    }

    public function destroy(ServiceCategory $category)
    {
        try {
            if ($category->services()->exists()) {
                return back()->with('error', 'Cannot delete category with associated services');
            }
            
            $category->delete();
            return back()->with('success', 'Service category deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete service category: ' . $e->getMessage());
        }
    }
} 