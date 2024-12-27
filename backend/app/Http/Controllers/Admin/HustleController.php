<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hustle;
use App\Models\ProfessionalCategory;
use Illuminate\Http\Request;

class HustleController extends Controller
{
    public function index(Request $request)
    {
        $hustles = Hustle::with(['category', 'assignedProfessional', 'applications'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->latest()
            ->paginate(10);

        $categories = ProfessionalCategory::all();

        return view('admin.hustles.index', compact('hustles', 'categories'));
    }

    public function create()
    {
        $categories = ProfessionalCategory::all();
        return view('admin.hustles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:professional_categories,id',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'requirements' => 'nullable|string',
        ]);

        $hustle = Hustle::create($validated);

        return redirect()->route('admin.hustles.show', $hustle)
            ->with('success', 'Hustle created successfully.');
    }

    public function show(Hustle $hustle)
    {
        $hustle->load(['category', 'assignedProfessional', 'applications.professional', 'messages']);
        return view('admin.hustles.show', compact('hustle'));
    }

    public function edit(Hustle $hustle)
    {
        $categories = ProfessionalCategory::all();
        return view('admin.hustles.edit', compact('hustle', 'categories'));
    }

    public function update(Request $request, Hustle $hustle)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:professional_categories,id',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'requirements' => 'nullable|string',
            'status' => 'required|in:open,approved,in_progress,completed,cancelled'
        ]);

        $hustle->update($validated);

        return redirect()->route('admin.hustles.show', $hustle)
            ->with('success', 'Hustle updated successfully.');
    }

    public function destroy(Hustle $hustle)
    {
        $hustle->delete();
        return redirect()->route('admin.hustles.index')
            ->with('success', 'Hustle deleted successfully.');
    }
} 