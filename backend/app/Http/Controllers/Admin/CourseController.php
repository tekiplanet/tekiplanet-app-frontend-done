<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $categories = CourseCategory::orderBy('name')->get();
        $courses = Course::query()
            ->with(['instructor', 'category'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $categories = CourseCategory::orderBy('name')->get();
        $instructors = Instructor::orderBy('first_name')->get();
        return view('admin.courses.create', compact('categories', 'instructors'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Course creation request:', $request->all());
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|uuid|exists:course_categories,id',
                'instructor_id' => 'required|uuid|exists:instructors,id',
                'level' => 'required|in:beginner,intermediate,advanced',
                'price' => 'required|numeric|min:0',
                'duration_hours' => 'required|numeric|min:1|max:24',
                'image_url' => 'nullable|url',
                'prerequisites' => 'nullable|array',
                'learning_outcomes' => 'nullable|array',
                'status' => 'required|in:draft,active,archived',
            ]);

            $course = Course::create([
                ...$validated,
                'rating' => 0,
                'total_reviews' => 0,
                'total_students' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'course' => $course
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 