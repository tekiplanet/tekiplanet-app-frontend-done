<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\BusinessProfile;
use App\Models\Professional;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for dashboard stats
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_businesses' => BusinessProfile::count(),
            'total_professionals' => Professional::count(),
        ];

        // Get recent users
        $recent_users = User::latest()->take(5)->get();

        // Get recent courses
        $recent_courses = Course::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_courses'));
    }
} 