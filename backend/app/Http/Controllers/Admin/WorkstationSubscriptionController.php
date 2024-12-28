<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkstationSubscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WorkstationSubscriptionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $subscriptions = WorkstationSubscription::query()
            ->with(['user', 'plan'])
            ->when($request->search, function ($query, $search) {
                $query->where('tracking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->payment_type, function ($query, $type) {
                $query->where('payment_type', $type);
            })
            ->latest()
            ->paginate(10);

        return view('admin.workstation.subscriptions.index', compact('subscriptions'));
    }

    public function show(WorkstationSubscription $subscription)
    {
        $subscription->load(['user', 'plan', 'payments']);
        return view('admin.workstation.subscriptions.show', compact('subscription'));
    }

    public function updateStatus(Request $request, WorkstationSubscription $subscription)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,expired,cancelled,pending'
        ]);

        $oldStatus = $subscription->status;
        $subscription->update($validated);

        // Send notification
        $this->notificationService->send([
            'type' => 'subscription_status_updated',
            'title' => 'Subscription Status Updated',
            'message' => "Your workstation subscription status has been updated to {$validated['status']}",
            'action_url' => "/subscriptions/{$subscription->id}",
            'icon' => 'office-building'
        ], $subscription->user);

        return back()->with('success', 'Subscription status updated successfully');
    }
} 