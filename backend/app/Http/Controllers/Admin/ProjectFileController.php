<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Mail\ProjectFileUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectFileController extends Controller
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
                'file' => 'required|file|max:10240', // 10MB max
                'description' => 'nullable|string|max:255'
            ]);

            $file = $request->file('file');
            $path = $file->store('project-files/' . $project->id, 'public');
            
            $projectFile = $project->files()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'description' => $validated['description'] ?? null,
                'uploaded_by' => auth()->id()
            ]);

            // Send notification
            $this->notificationService->send([
                'type' => 'project_file_uploaded',
                'title' => 'New Project File Uploaded',
                'message' => "A new file '{$projectFile->name}' has been uploaded to project '{$project->name}'",
                'icon' => 'document',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id,
                    'file_id' => $projectFile->id
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectFileUpdated($project, $projectFile, 'uploaded'));

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => $projectFile
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        try {
            $fileName = $file->name;

            // Delete the file from storage
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            $file->delete();

            // Send notification
            $this->notificationService->send([
                'type' => 'project_file_deleted',
                'title' => 'Project File Deleted',
                'message' => "File '{$fileName}' has been deleted from project '{$project->name}'",
                'icon' => 'trash',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectFileUpdated($project, $file, 'deleted'));

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }
} 