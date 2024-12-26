<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::with('zoneRates')->orderBy('priority')->get();
        $zones = ShippingZone::all();
        return view('admin.shipping.methods.index', compact('methods', 'zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'estimated_days_min' => 'required|integer|min:0',
            'estimated_days_max' => 'required|integer|min:0|gte:estimated_days_min',
            'is_active' => 'boolean',
            'priority' => 'required|integer|min:0',
            'zone_rates' => 'array',
            'zone_rates.*.rate' => 'numeric|min:0',
            'zone_rates.*.estimated_days' => 'required|integer|min:0'
        ]);

        try {
            $method = ShippingMethod::create([
                'id' => Str::uuid(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'base_cost' => $validated['base_cost'],
                'estimated_days_min' => $validated['estimated_days_min'],
                'estimated_days_max' => $validated['estimated_days_max'],
                'is_active' => $validated['is_active'],
                'priority' => $validated['priority']
            ]);

            // Create zone rates
            if (!empty($validated['zone_rates'])) {
                foreach ($validated['zone_rates'] as $zoneId => $rateData) {
                    if (!empty($rateData['rate'])) {
                        $method->zoneRates()->create([
                            'id' => Str::uuid(),
                            'zone_id' => $zoneId,
                            'rate' => $rateData['rate'],
                            'estimated_days' => $rateData['estimated_days']
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipping method created successfully',
                'method' => $method->load('zoneRates')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipping method: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, ShippingMethod $method)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'estimated_days_min' => 'required|integer|min:0',
            'estimated_days_max' => 'required|integer|min:0|gte:estimated_days_min',
            'is_active' => 'boolean',
            'priority' => 'required|integer|min:0',
            'zone_rates' => 'array',
            'zone_rates.*.rate' => 'numeric|min:0',
            'zone_rates.*.estimated_days' => 'required|integer|min:0'
        ]);

        try {
            $method->update($validated);

            // Update zone rates
            $method->zoneRates()->delete(); // Remove existing rates
            if (!empty($validated['zone_rates'])) {
                foreach ($validated['zone_rates'] as $zoneId => $rateData) {
                    if (!empty($rateData['rate'])) {
                        $method->zoneRates()->create([
                            'id' => Str::uuid(),
                            'zone_id' => $zoneId,
                            'rate' => $rateData['rate'],
                            'estimated_days' => $rateData['estimated_days']
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipping method updated successfully',
                'method' => $method->load('zoneRates')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping method: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ShippingMethod $method)
    {
        try {
            $method->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shipping method deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shipping method: ' . $e->getMessage()
            ], 500);
        }
    }
} 