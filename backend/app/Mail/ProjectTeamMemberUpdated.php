<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProjectTeamMember;

class ProjectTeamMemberUpdated extends Mailable
{
    use Queueable, SerializesModels;

    protected $teamMemberId;
    public $teamMember;
    protected $oldStatus;
    protected $isForBusinessOwner;

    public function __construct(ProjectTeamMember $teamMember, string $oldStatus, bool $isForBusinessOwner = false)
    {
        $this->teamMemberId = $teamMember->id;
        $this->oldStatus = $oldStatus;
        $this->isForBusinessOwner = $isForBusinessOwner;
        $this->afterCommit();
    }

    public function build()
    {
        // Fetch fresh data when processing the queue
        $this->teamMember = ProjectTeamMember::with(['project', 'professional.user'])
            ->findOrFail($this->teamMemberId);

        $view = $this->isForBusinessOwner 
            ? 'emails.project-team-member-updated-owner'
            : 'emails.project-team-member-updated';

        $greeting = $this->isForBusinessOwner
            ? 'Hello ' . $this->teamMember->project->businessProfile->user->first_name . ','
            : 'Hello ' . $this->teamMember->professional->user->first_name . ',';

        $subject = $this->oldStatus === 'removed'
            ? ($this->isForBusinessOwner ? 'Team Member Removed from Project: ' : 'Removed from Project Team: ') 
            . $this->teamMember->project->name
            : 'Project Team Status Updated: ' . $this->teamMember->project->name;

        return $this->view($view)
            ->subject($subject)
            ->with([
                'greeting' => $greeting,
                'closing' => 'Best regards,<br>TekiPlanet Team',
                'teamMember' => $this->teamMember,
                'oldStatus' => $this->oldStatus
            ]);
    }
} 