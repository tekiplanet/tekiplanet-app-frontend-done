<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $oldStatus;
    public $notes;

    public function __construct(Project $project, string $oldStatus, ?string $notes)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->notes = $notes;
    }

    public function build()
    {
        return $this->view('emails.projects.status-updated')
            ->subject("Project Status Updated - {$this->project->name}")
            ->with([
                'project' => $this->project,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->project->status,
                'notes' => $this->notes
            ]);
    }
} 