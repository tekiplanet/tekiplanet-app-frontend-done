<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProjectTeamMember;

class ProjectTeamMemberAdded extends Mailable
{
    use Queueable, SerializesModels;

    protected $teamMemberId;
    public $teamMember;

    public function __construct(ProjectTeamMember $teamMember)
    {
        $this->teamMemberId = $teamMember->id;
        $this->afterCommit();
    }

    public function build()
    {
        // Fetch fresh data when processing the queue
        $this->teamMember = ProjectTeamMember::with(['project', 'professional.user'])
            ->findOrFail($this->teamMemberId);

        return $this->view('emails.project-team-member-added')
            ->subject('Added to Project Team: ' . $this->teamMember->project->name)
            ->with([
                'greeting' => 'Hello ' . $this->teamMember->professional->user->first_name . ',',
                'closing' => 'Best regards,<br>TekiPlanet Team',
                'teamMember' => $this->teamMember
            ]);
    }
} 