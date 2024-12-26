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

    public function toArray($notifiable)
    {
        return [
            'type' => 'course_notice',
            'course_id' => $this->courseNotice->course_id,
            'notice_id' => $this->courseNotice->id,
            'title' => $this->courseNotice->title,
            'content' => $this->courseNotice->content,
            'priority' => $this->courseNotice->priority,
            'is_important' => $this->courseNotice->is_important,
        ];
    }
} 