<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnrollmentUpdated;
use App\Services\NotificationService;

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

            // Convert arrays to JSON strings before saving
            if (isset($validated['prerequisites'])) {
                $validated['prerequisites'] = json_encode($validated['prerequisites']);
            }
            if (isset($validated['learning_outcomes'])) {
                $validated['learning_outcomes'] = json_encode($validated['learning_outcomes']);
            }

            $category = CourseCategory::find($validated['category_id']);

            $course = Course::create([
                ...$validated,
                'category' => $category->name,
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

    /**
     * Display the specified course.
     */
    public function show(Course $course, Request $request)
    {
        $course->load([
            'instructor', 
            'category', 
            'modules.lessons', 
            'reviews'
        ]);

        $categories = CourseCategory::orderBy('name')->get();
        $instructors = Instructor::orderBy('first_name')->get();
        
        return view('admin.courses.show', compact(
            'course', 
            'categories', 
            'instructors'
        ));
    }

    public function edit(Course $course)
    {
        return response()->json([
            'success' => true,
            'course' => $course,
            'categories' => CourseCategory::orderBy('name')->get(),
            'instructors' => Instructor::orderBy('first_name')->get()
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        try {
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

            // Convert arrays to JSON strings before saving
            if (isset($validated['prerequisites'])) {
                $validated['prerequisites'] = json_encode($validated['prerequisites']);
            }
            if (isset($validated['learning_outcomes'])) {
                $validated['learning_outcomes'] = json_encode($validated['learning_outcomes']);
            }

            // Get category name
            $category = CourseCategory::find($validated['category_id']);
            $validated['category'] = $category->name;

            $course->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully',
                'course' => $course->fresh()
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
                'message' => 'Failed to update course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display course enrollments.
     */
    public function enrollments(Course $course, Request $request)
    {
        $enrollments = $course->enrollments()
            ->with('user')
            ->when($request->search, function($query, $search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where(function($subQuery) use ($search) {
                        $subQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->status, function($query, $status) {
                if ($status === 'completed') {
                    $query->where('status', 'completed');
                } else if ($status === 'active') {
                    $query->where('status', 'active');
                } else if ($status === 'pending') {
                    $query->where('status', 'pending');
                } else if ($status === 'dropped') {
                    $query->where('status', 'dropped');
                }
            })
            ->when($request->payment_status, function($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->orderBy($request->sort_by ?? 'enrolled_at', $request->sort_order ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.enrollments', compact('course', 'enrollments'));
    }

    /**
     * Update bulk enrollments.
     */
    public function bulkUpdateEnrollments(Course $course, Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'required|uuid|exists:enrollments,id',
            'action' => 'required|in:status,payment_status,progress',
            'value' => 'required|string'
        ]);

        $enrollments = $course->enrollments()
            ->whereIn('id', $request->enrollment_ids)
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            $oldValue = $enrollment->{$request->action};
            $enrollment->{$request->action} = $request->value;
            $enrollment->save();

            // Prepare notification data
            $notificationData = [
                'type' => 'enrollment_update',
                'title' => 'Course Enrollment Update',
                'message' => "Your enrollment status for {$course->title} has been updated.",
                'icon' => 'bell',
                'action_url' => route('student.courses.show', $course->id),
                'extra_data' => [
                    'course_id' => $course->id,
                    'field_updated' => $request->action,
                    'old_value' => $oldValue,
                    'new_value' => $request->value
                ]
            ];

            // Send notification
            app(NotificationService::class)->send($notificationData, $enrollment->user);

            // Send email
            Mail::to($enrollment->user)->send(new EnrollmentUpdated(
                $enrollment,
                $course,
                $request->action,
                $oldValue,
                $request->value
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Enrollments updated successfully'
        ]);
    }
} 