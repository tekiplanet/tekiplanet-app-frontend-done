<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceQuoteField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('long_description', 'like', "%{$search}%");
            })
            ->when(request('category'), function($query, $category) {
                $query->where('category_id', $category);
            })
            ->latest()
            ->paginate(10);

        $categories = ServiceCategory::all();
        return view('admin.services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // First validate basic service data
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'icon_name' => 'required|string|max:50',
            'is_featured' => 'boolean',
        ]);

        // Then validate quote fields if they exist
        if ($request->has('quote_fields')) {
            // Check for duplicate field names
            $fieldNames = collect($request->quote_fields)->pluck('name');
            if ($fieldNames->count() !== $fieldNames->unique()->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Each field name must be unique within the service'
                ], 422);
            }

            // Validate other field attributes
            $request->validate([
                'quote_fields' => 'nullable|array',
                'quote_fields.*.name' => 'required|string|max:255',
                'quote_fields.*.label' => 'required|string|max:255',
                'quote_fields.*.type' => 'required|in:text,textarea,select,multiselect,checkbox,radio,date,datetime,time,number,email,tel,url,file',
                'quote_fields.*.required' => 'required|boolean',
                'quote_fields.*.options' => 'nullable'
            ]);
        }

        try {
            DB::beginTransaction();

            $service = Service::create([
                'id' => Str::uuid(),
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'short_description' => $validated['short_description'],
                'long_description' => $validated['long_description'],
                'starting_price' => $validated['starting_price'],
                'icon_name' => $validated['icon_name'],
                'is_featured' => $validated['is_featured'] ?? false
            ]);

            // Create quote fields
            if (!empty($validated['quote_fields'])) {
                foreach ($validated['quote_fields'] as $order => $field) {
                    // Format options if they exist
                    $options = null;
                    if (!empty($field['options'])) {
                        if (is_string($field['options'])) {
                            // If it's a JSON string, decode it first
                            $decodedOptions = json_decode($field['options'], true);
                            $options = is_array($decodedOptions) ? $decodedOptions : explode("\n", $field['options']);
                        } else {
                            $options = $field['options'];
                        }
                        // Clean the array
                        $options = array_values(array_filter(array_map('trim', $options)));
                    }

                    ServiceQuoteField::create([
                        'id' => Str::uuid(),
                        'service_id' => $service->id,
                        'name' => $field['name'],
                        'label' => $field['label'],
                        'type' => $field['type'],
                        'required' => $field['required'],
                        'order' => $order + 1,
                        'options' => $options
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'redirect' => route('admin.services.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::all();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        // First validate basic service data
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'icon_name' => 'required|string|max:50',
            'is_featured' => 'boolean',
        ]);

        // Then validate quote fields if they exist
        if ($request->has('quote_fields')) {
            // Check for duplicate field names
            $fieldNames = collect($request->quote_fields)->pluck('name');
            if ($fieldNames->count() !== $fieldNames->unique()->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Each field name must be unique within the service'
                ], 422);
            }

            // Validate other field attributes
            $request->validate([
                'quote_fields' => 'nullable|array',
                'quote_fields.*.id' => 'nullable|exists:service_quote_fields,id',
                'quote_fields.*.name' => 'required|string|max:255',
                'quote_fields.*.label' => 'required|string|max:255',
                'quote_fields.*.type' => 'required|in:text,textarea,select,multiselect,checkbox,radio,date,datetime,time,number,email,tel,url,file',
                'quote_fields.*.required' => 'required|boolean',
                'quote_fields.*.options' => 'nullable'
            ]);
        }

        try {
            DB::beginTransaction();

            $service->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'short_description' => $validated['short_description'],
                'long_description' => $validated['long_description'],
                'starting_price' => $validated['starting_price'],
                'icon_name' => $validated['icon_name'],
                'is_featured' => $validated['is_featured'] ?? false
            ]);

            // Handle quote fields
            if (isset($validated['quote_fields'])) {
                // Get existing field IDs
                $existingFieldIds = $service->quoteFields->pluck('id')->toArray();
                $updatedFieldIds = [];

                foreach ($validated['quote_fields'] as $order => $field) {
                    // Format options if they exist
                    $options = null;
                    if (!empty($field['options'])) {
                        if (is_string($field['options'])) {
                            // If it's a JSON string, decode it first
                            $decodedOptions = json_decode($field['options'], true);
                            $options = is_array($decodedOptions) ? $decodedOptions : explode("\n", $field['options']);
                        } else {
                            $options = $field['options'];
                        }
                        // Clean the array
                        $options = array_values(array_filter(array_map('trim', $options)));
                    }

                    if (isset($field['id'])) {
                        ServiceQuoteField::where('id', $field['id'])->update([
                            'name' => $field['name'],
                            'label' => $field['label'],
                            'type' => $field['type'],
                            'required' => $field['required'],
                            'order' => $order + 1,
                            'options' => $options
                        ]);
                        $updatedFieldIds[] = $field['id'];
                    } else {
                        ServiceQuoteField::create([
                            'id' => Str::uuid(),
                            'service_id' => $service->id,
                            'name' => $field['name'],
                            'label' => $field['label'],
                            'type' => $field['type'],
                            'required' => $field['required'],
                            'order' => $order + 1,
                            'options' => $options
                        ]);
                    }
                }

                // Delete removed fields
                $removedFieldIds = array_diff($existingFieldIds, $updatedFieldIds);
                if (!empty($removedFieldIds)) {
                    ServiceQuoteField::whereIn('id', $removedFieldIds)->delete();
                }
            } else {
                // If no fields provided, delete all existing fields
                $service->quoteFields()->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'redirect' => route('admin.services.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted successfully');
    }

    public function toggleFeatured(Service $service)
    {
        $service->update([
            'is_featured' => !$service->is_featured
        ]);

        return response()->json([
            'success' => true,
            'featured' => $service->is_featured
        ]);
    }
} 