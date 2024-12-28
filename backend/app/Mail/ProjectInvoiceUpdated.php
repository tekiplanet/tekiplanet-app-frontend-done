<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProjectInvoice;
use App\Models\Project;

class ProjectInvoiceUpdated extends Mailable
{
    use Queueable, SerializesModels;

    protected $invoiceId;
    protected $projectId;
    public $invoice;
    public $project;

    public function __construct(ProjectInvoice $invoice)
    {
        $this->invoiceId = $invoice->id;
        $this->projectId = $invoice->project_id;
        $this->afterCommit();
    }

    public function build()
    {
        // Fetch fresh data when processing the queue
        $this->invoice = ProjectInvoice::findOrFail($this->invoiceId);
        $this->project = Project::findOrFail($this->projectId);

        return $this->view('emails.project-invoice-updated')
            ->subject('Invoice Updated: ' . $this->invoice->invoice_number)
            ->with([
                'greeting' => 'Hello,',
                'closing' => 'Best regards,<br>TekiPlanet Team',
                'invoice' => $this->invoice,
                'project' => $this->project
            ]);
    }
} 