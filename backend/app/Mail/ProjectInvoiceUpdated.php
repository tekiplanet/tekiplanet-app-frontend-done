<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectInvoiceUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $invoice;
    public $action;

    public function __construct(Project $project, ProjectInvoice $invoice, string $action)
    {
        $this->project = $project;
        $this->invoice = $invoice;
        $this->action = $action;
    }

    public function build()
    {
        return $this->view('emails.projects.invoice-updated')
            ->subject("Project Invoice {$this->action} - {$this->project->name}")
            ->with([
                'project' => $this->project,
                'invoice' => $this->invoice,
                'action' => $this->action
            ]);
    }
} 