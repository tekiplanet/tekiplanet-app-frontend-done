<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;

class ProjectFileController extends Controller
{
    public function store(Request $request, Project $project)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240' // 10MB max
            ]);

            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            
            // Generate a unique name for storage
            $uniqueName = uniqid() . '-' . $fileName;
            
            // Store the file
            $path = $file->storeAs('project-files', $uniqueName, 'public');

            // Create file record
            $projectFile = $project->files()->create([
                'name' => $fileName,
                'file_path' => $path,
                'uploaded_by' => auth('admin')->id(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $projectFile
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
            $file->delete();
            
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