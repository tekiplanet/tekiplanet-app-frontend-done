<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hustle;
use App\Models\HustleApplication;
use Illuminate\Http\Request;

class HustleApplicationController extends Controller
{
    public function index(Hustle $hustle)
    {
        $applications = $hustle->applications()
            ->with('professional')
            ->latest()
            ->paginate(10);

        return view('admin.hustles.applications.index', compact('hustle', 'applications'));
    }

    public function show(Hustle $hustle, HustleApplication $application)
    {
        $application->load('professional');
        return view('admin.hustles.applications.show', compact('hustle', 'application'));
    }

    public function updateStatus(Request $request, Hustle $hustle, HustleApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,withdrawn'
        ]);

        $application->update($validated);

        if ($validated['status'] === 'approved') {
            $hustle->update([
                'status' => 'approved',
                'assigned_professional_id' => $application->professional_id
            ]);

            // Reject other applications
            $hustle->applications()
                ->where('id', '!=', $application->id)
                ->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Application status updated successfully.');
    }
} 