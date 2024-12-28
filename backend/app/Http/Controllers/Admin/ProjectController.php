<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Mail\ProjectStatusUpdated;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $projects = Project::with(['businessProfile', 'stages'])
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('client_name', 'like', "%{$search}%")
                      ->orWhereHas('businessProfile', function($q) use ($search) {
                          $q->where('business_name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $project->load([
            'businessProfile',
            'stages',
            'teamMembers.user',
            'files',
            'invoices'
        ]);

        return view('admin.projects.show', compact('project'));
    }

    public function updateStatus(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
                'notes' => 'nullable|string'
            ]);

            $oldStatus = $project->status;
            $project->update($validated);

            // Send notification
            $this->notificationService->send([
                'type' => 'project_status_updated',
                'title' => 'Project Status Updated',
                'message' => "Your project '{$project->name}' status has been updated to " . ucfirst($validated['status']),
                'icon' => 'briefcase',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectStatusUpdated($project, $oldStatus, $validated['notes'] ?? null));

            return response()->json([
                'success' => true,
                'message' => 'Project status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project status: ' . $e->getMessage()
            ], 500);
        }
    }
} 