<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\ProfessionalCategory;
use Illuminate\Http\Request;
use App\Notifications\CustomNotification;
use Illuminate\Support\HtmlString;
use App\Services\NotificationService;

class ProfessionalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $categories = ProfessionalCategory::orderBy('name')->get();
        
        $professionals = Professional::query()
            ->with(['user', 'category'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.professionals.index', compact('professionals', 'categories'));
    }

    public function show(Professional $professional)
    {
        $professional->load(['user', 'category']);
        return view('admin.professionals.show', compact('professional'));
    }

    public function update(Request $request, Professional $professional)
    {
        $validated = $request->validate([
            'professional_category_id' => 'required|exists:professional_categories,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string|in:Abia,Adamawa,Akwa Ibom,Anambra,Bauchi,Bayelsa,Benue,Borno,Cross River,Delta,Ebonyi,Edo,Ekiti,Enugu,FCT,Gombe,Imo,Jigawa,Kaduna,Kano,Katsina,Kebbi,Kogi,Kwara,Lagos,Nasarawa,Niger,Ogun,Ondo,Osun,Oyo,Plateau,Rivers,Sokoto,Taraba,Yobe,Zamfara',
            'bio' => 'nullable|string',
            'years_of_experience' => 'required|integer|min:0',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        $validated['country'] = 'Nigeria';

        $professional->update($validated);

        return response()->json([
            'message' => 'Professional updated successfully'
        ]);
    }

    public function toggleStatus(Request $request, Professional $professional)
    {
        $newStatus = $professional->status === 'active' ? 'inactive' : 'active';
        $professional->status = $newStatus;
        $professional->save();

        // Prepare notification content
        $title = 'Professional Status Update';
        $message = $newStatus === 'active' 
            ? "Your professional account has been activated successfully."
            : "Your professional account has been deactivated. Reason: " . $request->input('reason');

        // Prepare email content
        $emailContent = new HtmlString("
            <p>Dear {$professional->user->name},</p>
            <p>{$message}</p>
            <p><strong>Professional Details:</strong></p>
            <ul>
                <li>Category: {$professional->professional_category->name}</li>
                <li>Current Status: " . ucfirst($newStatus) . "</li>
            </ul>
            " . ($newStatus === 'inactive' ? "
            <p><strong>Reason for Deactivation:</strong><br>
            {$request->input('reason')}</p>
            " : "") . "
            <p>If you have any questions, please contact our support team.</p>
        ");

        // Send in-app notification
        $this->notificationService->send([
            'type' => 'professional',
            'title' => $title,
            'message' => $message,
            'icon' => 'professional',
            'action_url' => '/professional/profile'
        ], $professional->user);

        // Send email notification
        $professional->user->notify(new CustomNotification(
            $title,
            $message,
            'professional-status',
            $emailContent
        ));

        return response()->json([
            'message' => 'Professional status updated successfully',
            'status' => $professional->status
        ]);
    }
} 