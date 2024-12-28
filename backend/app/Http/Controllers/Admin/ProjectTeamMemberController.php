<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTeamMember;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Mail\ProjectTeamMemberUpdated;
use Illuminate\Support\Facades\Mail;

class ProjectTeamMemberController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|string|max:255',
                'joined_at' => 'required|date',
                'left_at' => 'nullable|date|after:joined_at',
                'status' => 'required|in:active,inactive'
            ]);

            $member = $project->teamMembers()->create($validated);
            $user = User::find($validated['user_id']);

            // Send notification to business owner
            $this->notificationService->send([
                'type' => 'project_team_member_added',
                'title' => 'New Team Member Added',
                'message' => "{$user->name} has been added to project '{$project->name}' as {$member->role}",
                'icon' => 'user-plus',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id,
                    'member_id' => $member->id
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectTeamMemberUpdated($project, $member, $user, 'added'));

            return response()->json([
                'success' => true,
                'message' => 'Team member added successfully',
                'member' => $member->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add team member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Project $project, ProjectTeamMember $member)
    {
        try {
            $validated = $request->validate([
                'role' => 'required|string|max:255',
                'joined_at' => 'required|date',
                'left_at' => 'nullable|date|after:joined_at',
                'status' => 'required|in:active,inactive'
            ]);

            $oldRole = $member->role;
            $member->update($validated);

            // Send notification
            $this->notificationService->send([
                'type' => 'project_team_member_updated',
                'title' => 'Team Member Updated',
                'message' => "{$member->user->name}'s role has been updated from {$oldRole} to {$validated['role']}",
                'icon' => 'user-check',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id,
                    'member_id' => $member->id,
                    'old_role' => $oldRole,
                    'new_role' => $validated['role']
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectTeamMemberUpdated($project, $member, $member->user, 'updated'));

            return response()->json([
                'success' => true,
                'message' => 'Team member updated successfully',
                'member' => $member->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project, ProjectTeamMember $member)
    {
        try {
            $userName = $member->user->name;
            $userRole = $member->role;
            $member->delete();

            // Send notification
            $this->notificationService->send([
                'type' => 'project_team_member_removed',
                'title' => 'Team Member Removed',
                'message' => "{$userName} ({$userRole}) has been removed from project '{$project->name}'",
                'icon' => 'user-minus',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectTeamMemberUpdated($project, $member, $member->user, 'removed'));

            return response()->json([
                'success' => true,
                'message' => 'Team member removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove team member: ' . $e->getMessage()
            ], 500);
        }
    }
} 