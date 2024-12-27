@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ isset($service) ? 'Edit Service' : 'Create Service' }}
        </h2>
        <a href="{{ route('admin.services.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Back to Services
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <form action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" 
              method="POST" 
              class="p-6 space-y-6">
            @csrf
            @if(isset($service))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Category
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id', $service->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Name
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $service->name ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Short Description -->
                <div>
                    <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Short Description
                    </label>
                    <input type="text" 
                           name="short_description" 
                           id="short_description"
                           value="{{ old('short_description', $service->short_description ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           required>
                    @error('short_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Starting Price -->
                <div>
                    <label for="starting_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Starting Price (₦)
                    </label>
                    <input type="number" 
                           name="starting_price" 
                           id="starting_price"
                           value="{{ old('starting_price', $service->starting_price ?? '') }}"
                           step="0.01"
                           min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           required>
                    @error('starting_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Long Description -->
            <div>
                <label for="long_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Long Description
                </label>
                <textarea name="long_description" 
                          id="long_description" 
                          rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                          required>{{ old('long_description', $service->long_description ?? '') }}</textarea>
                @error('long_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Icon Name -->
            <div>
                <label for="icon_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Icon Name
                </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </span>
                    <input type="text" 
                           name="icon_name" 
                           id="icon_name"
                           value="{{ old('icon_name', $service->icon_name ?? '') }}"
                           placeholder="wrench"
                           class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           required>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Enter Lucide icon name (e.g., wrench, settings, tool)
                </p>
                <div class="mt-2 flex flex-wrap gap-4" id="iconPreview">
                    <!-- Icon preview will be shown here -->
                </div>
                @error('icon_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Featured -->
            <div class="flex items-center">
                <input type="checkbox" 
                       name="is_featured" 
                       id="is_featured" 
                       value="1"
                       {{ old('is_featured', $service->is_featured ?? false) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Featured Service
                </label>
                @error('is_featured')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ isset($service) ? 'Update Service' : 'Create Service' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const iconInput = document.getElementById('icon_name');
    const iconPreview = document.getElementById('iconPreview');

    function updateIconPreview() {
        const iconName = iconInput.value.trim();
        if (!iconName) return;

        iconPreview.innerHTML = `
            <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                     class="text-gray-600 dark:text-gray-300">
                    <!-- Icon paths will be injected here by Lucide -->
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-300">${iconName}</span>
            </div>
        `;

        // Initialize Lucide icon
        lucide.createIcons({
            icons: {
                [iconName]: lucide[iconName]
            }
        });
    }

    // Update preview on input change
    iconInput.addEventListener('input', updateIconPreview);

    // Show initial preview if icon exists
    if (iconInput.value) {
        updateIconPreview();
    }
</script>
@endpush 