<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\CourseNotice;

class CourseNoticeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $courseNotice;

    public function __construct(CourseNotice $courseNotice)
    {
        $this->courseNotice = $courseNotice;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
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
    }

    public function toBroadcast($notifiable)
    {
        return [
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
    }
} 