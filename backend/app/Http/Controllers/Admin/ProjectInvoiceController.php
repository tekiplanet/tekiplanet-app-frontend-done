<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectInvoice;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Mail\ProjectInvoiceUpdated;
use Illuminate\Support\Facades\Mail;

class ProjectInvoiceController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,paid,overdue,cancelled'
            ]);

            $invoice = $project->invoices()->create($validated);

            // Send notification
            $this->notificationService->send([
                'type' => 'project_invoice_created',
                'title' => 'New Project Invoice Created',
                'message' => "A new invoice of ₦" . number_format($invoice->amount, 2) . " has been created for project '{$project->name}'",
                'icon' => 'currency-dollar',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id,
                    'invoice_id' => $invoice->id
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectInvoiceUpdated($project, $invoice, 'created'));

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Project $project, ProjectInvoice $invoice)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,paid,overdue,cancelled'
            ]);

            $oldStatus = $invoice->status;
            $invoice->update($validated);

            // Send notification
            $this->notificationService->send([
                'type' => 'project_invoice_updated',
                'title' => 'Project Invoice Updated',
                'message' => "Invoice status has been updated from {$oldStatus} to {$validated['status']}",
                'icon' => 'currency-dollar',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id,
                    'invoice_id' => $invoice->id,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status']
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectInvoiceUpdated($project, $invoice, 'updated'));

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project, ProjectInvoice $invoice)
    {
        try {
            $invoiceAmount = $invoice->amount;
            $invoice->delete();

            // Send notification
            $this->notificationService->send([
                'type' => 'project_invoice_deleted',
                'title' => 'Project Invoice Deleted',
                'message' => "Invoice of ₦" . number_format($invoiceAmount, 2) . " has been deleted from project '{$project->name}'",
                'icon' => 'trash',
                'action_url' => "/projects/{$project->id}",
                'extra_data' => [
                    'project_id' => $project->id
                ]
            ], $project->businessProfile->user);

            // Queue email
            Mail::to($project->businessProfile->user->email)
                ->queue(new ProjectInvoiceUpdated($project, $invoice, 'deleted'));

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }
} 