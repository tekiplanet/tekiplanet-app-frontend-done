<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\UserCourseExam;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Jobs\SendExamNotification;

class CourseExamParticipantController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request, Course $course, CourseExam $exam)
    {
        $participants = $exam->userExams()
            ->with('user')
            ->when($request->search, function($query, $search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->sort, function($query, $sort) {
                switch($sort) {
                    case 'oldest':
                        $query->oldest('started_at');
                        break;
                    case 'name':
                        $query->whereHas('user', function($q) {
                            $q->orderBy('name');
                        });
                        break;
                    case 'score':
                        $query->orderByRaw('COALESCE(score/total_score, 0) DESC');
                        break;
                    default:
                        $query->latest('started_at');
                }
            }, function($query) {
                $query->latest('started_at');
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.exams.participants.index', compact('course', 'exam', 'participants'));
    }

    public function bulkUpdate(Request $request, Course $course, CourseExam $exam)
    {
        try {
            $validated = $request->validate([
                'user_exams' => 'required|array',
                'user_exams.*' => 'exists:user_course_exams,id',
                'action' => 'required|in:status,score',
                'status' => 'required_if:action,status|in:not_started,in_progress,completed,missed',
                'score' => 'required_if:action,score|numeric|min:0',
                'total_score' => 'required_if:action,score|numeric|min:0'
            ]);

            $userExams = UserCourseExam::whereIn('id', $validated['user_exams'])->get();

            foreach ($userExams as $userExam) {
                if ($validated['action'] === 'status') {
                    $userExam->update(['status' => $validated['status']]);
                } else {
                    $userExam->update([
                        'score' => $validated['score'],
                        'total_score' => $validated['total_score']
                    ]);
                }

                // Queue notification and email
                dispatch(new SendExamNotification($userExam));
            }

            return response()->json([
                'success' => true,
                'message' => 'Participants updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating participants: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update participants'
            ], 500);
        }
    }

    public function update(Request $request, Course $course, CourseExam $exam, UserCourseExam $participant)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:not_started,in_progress,completed,missed',
                'score' => 'required|numeric|min:0',
                'total_score' => 'required|numeric|min:0'
            ]);

            $participant->update($validated);

            // Queue notification and email
            dispatch(new SendExamNotification($participant));

            return response()->json([
                'success' => true,
                'message' => 'Participant updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating participant: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update participant'
            ], 500);
        }
    }
} 