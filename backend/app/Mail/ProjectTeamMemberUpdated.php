<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectTeamMember;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectTeamMemberUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $member;
    public $user;
    public $action;

    public function __construct(Project $project, ProjectTeamMember $member, User $user, string $action)
    {
        $this->project = $project;
        $this->member = $member;
        $this->user = $user;
        $this->action = $action;
    }

    public function build()
    {
        return $this->view('emails.projects.team-member-updated')
            ->subject("Project Team Member {$this->action} - {$this->project->name}")
            ->with([
                'project' => $this->project,
                'member' => $this->member,
                'user' => $this->user,
                'action' => $this->action
            ]);
    }
} 