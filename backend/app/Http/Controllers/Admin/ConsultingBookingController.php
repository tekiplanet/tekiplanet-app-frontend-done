<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultingBooking;
use App\Models\Professional;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ConsultingBookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $bookings = ConsultingBooking::with(['user', 'expert.user', 'timeSlot'])
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->payment_status, function($query, $status) {
                $query->where('payment_status', $status);
            })
            ->latest()
            ->paginate(10);

        $experts = Professional::with('user')->get();

        return view('admin.consulting.bookings.index', compact('bookings', 'experts'));
    }

    public function show(ConsultingBooking $booking)
    {
        $booking->load(['user', 'expert.user', 'timeSlot', 'review', 'notifications']);
        return view('admin.consulting.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, ConsultingBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,ongoing,completed,cancelled',
            'cancellation_reason' => 'required_if:status,cancelled'
        ]);

        $oldStatus = $booking->status;
        $booking->update($validated);

        // Send notification to user
        $this->notificationService->send([
            'type' => 'booking_status_updated',
            'title' => 'Booking Status Updated',
            'message' => "Your booking status has been updated to " . ucfirst($validated['status']),
            'action_url' => "/bookings/{$booking->id}"
        ], $booking->user);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully'
        ]);
    }

    public function assignExpert(Request $request, ConsultingBooking $booking)
    {
        $validated = $request->validate([
            'expert_id' => 'required|exists:professionals,id'
        ]);

        $booking->update([
            'assigned_expert_id' => $validated['expert_id'],
            'expert_assigned_at' => now()
        ]);

        // Notify the expert
        $expert = Professional::find($validated['expert_id']);
        $this->notificationService->send([
            'type' => 'booking_assigned',
            'title' => 'New Booking Assigned',
            'message' => "You have been assigned to a new consulting booking",
            'action_url' => "/bookings/{$booking->id}"
        ], $expert->user);

        return response()->json([
            'success' => true,
            'message' => 'Expert assigned successfully'
        ]);
    }
} 