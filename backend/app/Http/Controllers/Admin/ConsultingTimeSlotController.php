<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultingTimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsultingTimeSlotController extends Controller
{
    public function index(Request $request)
    {
        $timeSlots = ConsultingTimeSlot::query()
            ->when($request->date, function($query, $date) {
                $query->whereDate('date', $date);
            })
            ->when($request->availability !== null, function($query) use ($request) {
                $query->where('is_available', $request->availability);
            })
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(10);

        return view('admin.consulting.timeslots.index', compact('timeSlots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'capacity' => 'required|integer|min:1',
            'is_available' => 'boolean'
        ]);

        ConsultingTimeSlot::create([
            'date' => $validated['date'],
            'time' => $validated['time'],
            'capacity' => $validated['capacity'],
            'is_available' => $validated['is_available'] ?? true,
            'booked_slots' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Time slot created successfully'
        ]);
    }

    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|array',
            'times' => 'required|array',
            'capacity' => 'required|integer|min:1'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            if (in_array($currentDate->format('N'), $validated['days'])) {
                foreach ($validated['times'] as $time) {
                    ConsultingTimeSlot::create([
                        'date' => $currentDate->format('Y-m-d'),
                        'time' => $time,
                        'capacity' => $validated['capacity'],
                        'is_available' => true,
                        'booked_slots' => 0
                    ]);
                }
            }
            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'message' => 'Time slots created successfully'
        ]);
    }

    public function update(Request $request, ConsultingTimeSlot $timeSlot)
    {
        $validated = $request->validate([
            'capacity' => 'required|integer|min:1',
            'is_available' => 'boolean'
        ]);

        $timeSlot->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Time slot updated successfully'
        ]);
    }

    public function destroy(ConsultingTimeSlot $timeSlot)
    {
        if ($timeSlot->booking()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete time slot with existing bookings'
            ], 422);
        }

        $timeSlot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time slot deleted successfully'
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:consulting_time_slots,id'
        ]);

        try {
            // Check if any of the selected slots have bookings
            $slotsWithBookings = ConsultingTimeSlot::whereIn('id', $validated['ids'])
                ->whereHas('booking')
                ->exists();

            if ($slotsWithBookings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete time slots with existing bookings'
                ], 422);
            }

            ConsultingTimeSlot::whereIn('id', $validated['ids'])->delete();

            return response()->json([
                'success' => true,
                'message' => 'Selected time slots deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete time slots: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(ConsultingTimeSlot $timeSlot)
    {
        return response()->json([
            'capacity' => $timeSlot->capacity,
            'is_available' => $timeSlot->is_available
        ]);
    }
} 