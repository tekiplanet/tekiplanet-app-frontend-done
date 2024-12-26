<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserCourseExam;

class ExamStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $userExam;

    public function __construct(UserCourseExam $userExam)
    {
        $this->userExam = $userExam->load(['user', 'courseExam']);
    }

    public function build()
    {
        return $this->subject('Exam Status Update')
                    ->view('emails.exam-status-updated');
    }
} 