<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;

class ProjectStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $oldStatus;
    public $newStatus;
    public $notes;

    public function __construct(Project $project, string $oldStatus, string $newStatus, ?string $notes)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->notes = $notes;
    }

    public function build()
    {
        return $this->subject('Project Status Updated: ' . $this->project->name)
                    ->view('emails.project-status-updated');
    }
} 