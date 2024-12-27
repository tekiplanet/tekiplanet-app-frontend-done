<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hustle;
use App\Models\ProfessionalCategory;
use App\Models\Professional;
use App\Services\NotificationService;
use App\Mail\HustleCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:professional_categories,id',
                'budget' => 'required|numeric|min:0',
                'deadline' => 'required|date|after:today',
                'requirements' => 'nullable|string',
            ]);

            $hustle = Hustle::create($validated);

            // Get all professionals in this category
            $professionals = Professional::where('category_id', $hustle->category_id)
                ->with('user')
                ->get();

            // Prepare notification data
            $notificationData = [
                'type' => 'new_hustle',
                'title' => 'New Hustle Available',
                'message' => "A new hustle '{$hustle->title}' has been posted in your category.",
                'icon' => 'briefcase',
                'action_url' => '/dashboard/hustles/' . $hustle->id,
                'extra_data' => [
                    'hustle_id' => $hustle->id,
                    'category_id' => $hustle->category_id,
                ]
            ];

            // Get notification service
            $notificationService = app(NotificationService::class);

            foreach ($professionals as $professional) {
                // Send notification
                $notificationService->send($notificationData, $professional->user);

                // Send email
                Mail::to($professional->user->email)
                    ->queue(new HustleCreated($hustle, $professional));
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hustle created successfully',
                    'hustle' => $hustle,
                    'redirect' => route('admin.hustles.show', $hustle)
                ]);
            }

            return redirect()->route('admin.hustles.show', $hustle)
                ->with('success', 'Hustle created successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to create hustle: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create hustle: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to create hustle: ' . $e->getMessage()]);
        }
    }

    public function show(Hustle $hustle)
    {
        $hustle->load([
            'category',
            'assignedProfessional.user',
            'applications.professional.user',
            'applications.professional.category',
            'messages'
        ]);
        
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