<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use App\Notifications\CustomNotification;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $businesses = BusinessProfile::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('business_name', 'like', "%{$search}%")
                      ->orWhere('business_email', 'like', "%{$search}%")
                      ->orWhere('registration_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.businesses.index', compact('businesses'));
    }

    public function show(BusinessProfile $business)
    {
        return view('admin.businesses.show', compact('business'));
    }

    public function update(Request $request, BusinessProfile $business)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'required|email|unique:business_profiles,business_email,' . $business->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'registration_number' => 'nullable|string|unique:business_profiles,registration_number,' . $business->id,
            'tax_number' => 'nullable|string|unique:business_profiles,tax_number,' . $business->id,
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string|in:Abia,Adamawa,Akwa Ibom,Anambra,Bauchi,Bayelsa,Benue,Borno,Cross River,Delta,Ebonyi,Edo,Ekiti,Enugu,FCT,Gombe,Imo,Jigawa,Kaduna,Kano,Katsina,Kebbi,Kogi,Kwara,Lagos,Nasarawa,Niger,Ogun,Ondo,Osun,Oyo,Plateau,Rivers,Sokoto,Taraba,Yobe,Zamfara',
        ]);

        $validated['country'] = 'Nigeria';

        $business->update($validated);

        return response()->json([
            'message' => 'Business updated successfully'
        ]);
    }

    public function toggleStatus(Request $request, BusinessProfile $business)
    {
        $newStatus = $business->status === 'active' ? 'inactive' : 'active';
        $business->status = $newStatus;
        $business->save();

        $message = $newStatus === 'active' 
            ? "Your business account has been activated successfully."
            : "Your business account has been deactivated. Reason: " . $request->input('reason');

        // Send notification to the business owner
        $business->user->notify(new CustomNotification(
            'Business Status Update',
            $message,
            'business-status'
        ));

        return response()->json([
            'message' => 'Business status updated successfully',
            'status' => $business->status
        ]);
    }
} 