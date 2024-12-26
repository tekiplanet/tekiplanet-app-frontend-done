<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserCourseExam;
use App\Mail\ExamStatusUpdated;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class SendExamNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userExam;

    public function __construct(UserCourseExam $userExam)
    {
        $this->userExam = $userExam;
    }

    public function handle(NotificationService $notificationService)
    {
        try {
            // Load the courseExam relationship if not loaded
            if (!$this->userExam->relationLoaded('courseExam')) {
                $this->userExam->load('courseExam');
            }

            // Send email
            Mail::to($this->userExam->user->email)
                ->queue(new ExamStatusUpdated($this->userExam));

            // Create notification data
            $notificationData = [
                'type' => 'exam_update',
                'title' => 'Exam Status Update',
                'message' => "Your exam status for {$this->userExam->courseExam->title} has been updated to " . 
                            str_replace('_', ' ', ucfirst($this->userExam->status)),
                'icon' => 'clipboard-check',
                'action_url' => null,
                'extra_data' => [
                    'exam_id' => $this->userExam->course_exam_id,
                    'status' => $this->userExam->status,
                    'score' => $this->userExam->score,
                    'total_score' => $this->userExam->total_score
                ]
            ];

            // Send notification
            $notificationService->send($notificationData, $this->userExam->user);

        } catch (\Exception $e) {
            \Log::error('Error in SendExamNotification job: ' . $e->getMessage(), [
                'user_exam_id' => $this->userExam->id,
                'exam_id' => $this->userExam->course_exam_id,
                'user_id' => $this->userExam->user_id
            ]);
            throw $e;
        }
    }
} 