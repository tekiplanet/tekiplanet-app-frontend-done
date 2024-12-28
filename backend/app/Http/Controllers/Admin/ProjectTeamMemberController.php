<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTeamMember;
use Illuminate\Http\Request;

class ProjectTeamMemberController extends Controller
{
    public function store(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'professional_id' => 'required|exists:professionals,id',
                'role' => 'required|string',
                'status' => 'required|in:active,inactive',
                'joined_at' => 'required|date',
                'left_at' => 'nullable|date|after:joined_at'
            ]);

            $teamMember = $project->teamMembers()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Team member added successfully',
                'data' => $teamMember
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
                'role' => 'required|string',
                'status' => 'required|in:active,inactive',
                'joined_at' => 'required|date',
                'left_at' => 'nullable|date|after:joined_at'
            ]);

            $member->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Team member updated successfully',
                'data' => $member
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
            $member->delete();

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