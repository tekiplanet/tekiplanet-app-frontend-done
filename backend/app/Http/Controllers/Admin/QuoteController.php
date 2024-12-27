<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteMessage;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class QuoteController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $quotes = Quote::with(['user:id,first_name,last_name,email', 'service', 'assignedTo'])
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('project_description', 'like', "%{$search}%")
                      ->orWhere('industry', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->latest()
            ->paginate(10);

        return view('admin.quotes.index', compact('quotes'));
    }

    public function show(Quote $quote)
    {
        $quote->load(['user', 'service', 'assignedTo', 'messages.user']);
        $admins = Admin::where('is_active', true)->get();
        
        return view('admin.quotes.show', compact('quote', 'admins'));
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,accepted,rejected'
        ]);

        $quote->update($validated);

        // Send notification to user
        $this->notificationService->send([
            'type' => 'quote_status_updated',
            'title' => 'Quote Status Updated',
            'message' => "Your quote status has been updated to " . ucfirst($validated['status']),
            'action_url' => "/quotes/{$quote->id}"
        ], $quote->user);

        return response()->json([
            'success' => true,
            'message' => 'Quote status updated successfully'
        ]);
    }

    public function assign(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:admins,id'
        ]);

        $quote->update($validated);

        // For now, we'll just return success without sending notification to admin
        // TODO: Implement admin notifications system

        return response()->json([
            'success' => true,
            'message' => 'Quote assigned successfully'
        ]);
    }

    public function sendMessage(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'message' => 'required|string'
        ]);

        $message = $quote->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'sender_type' => 'admin'
        ]);

        // Notify the quote owner
        $this->notificationService->send([
            'type' => 'new_quote_message',
            'title' => 'New Message on Your Quote',
            'message' => "You have received a new message regarding your quote",
            'action_url' => "/quotes/{$quote->id}"
        ], $quote->user);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message->load('user')
        ]);
    }
} 