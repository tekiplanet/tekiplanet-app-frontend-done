<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CourseNotice;
use App\Models\User;
use App\Models\UserCourseNotice;
use App\Notifications\CourseNoticeNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourseNoticeMail;
use Illuminate\Support\Str;
use App\Services\NotificationService;
use App\Jobs\SendCourseNoticeNotification;

class SendCourseNoticeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $courseNotice;
    protected $userIds;

    public function __construct(CourseNotice $courseNotice, array $userIds)
    {
        $this->courseNotice = $courseNotice;
        $this->userIds = $userIds;
    }

    public function handle(NotificationService $notificationService)
    {
        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            // Create user notice record
            UserCourseNotice::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'course_notice_id' => $this->courseNotice->id,
                'is_read' => false,
                'read_at' => null,
                'is_hidden' => false
            ]);

            // Prepare notification data
            $notificationData = [
                'type' => 'course_notice',
                'title' => $this->courseNotice->title,
                'message' => \Str::limit($this->courseNotice->content, 100),
                'course_id' => $this->courseNotice->course_id,
                'notice_id' => $this->courseNotice->id,
                'priority' => $this->courseNotice->priority,
                'is_important' => $this->courseNotice->is_important,
                'action_url' => "/dashboard/courses/{$this->courseNotice->course_id}",
                'icon' => 'bell'
            ];
            // Dispatch notification job
            $notificationJob = new SendCourseNoticeNotification($notificationData, $user);
            dispatch($notificationJob);

            // Send email
            Mail::to($user->email)->queue(new CourseNoticeMail($this->courseNotice));
        }
    }
} 