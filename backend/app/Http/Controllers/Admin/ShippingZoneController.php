<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::withCount(['rates', 'addresses'])->get();
        return view('admin.shipping.zones.index', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shipping_zones,name',
        ]);

        try {
            $zone = ShippingZone::create([
                'id' => Str::uuid(),
                'name' => $validated['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shipping zone created successfully',
                'zone' => $zone
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipping zone: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, ShippingZone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shipping_zones,name,' . $zone->id,
        ]);

        try {
            $zone->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Shipping zone updated successfully',
                'zone' => $zone
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping zone: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ShippingZone $zone)
    {
        try {
            if ($zone->addresses()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete zone with associated addresses'
                ], 422);
            }

            $zone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shipping zone deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shipping zone: ' . $e->getMessage()
            ], 500);
        }
    }
} 