@extends('admin.layouts.app')

@section('content')
@include('admin.components.notification')
<div class="container px-6 mx-auto">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Create Service
        </h2>
        <a href="{{ route('admin.services.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Back to Services
        </a>
    </div>

    <div class="mt-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('admin.services.store') }}" method="POST" id="createForm">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Category
                        </label>
                        <select name="category_id" 
                                id="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Name
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name"
                               value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Short Description
                        </label>
                        <input type="text" 
                               name="short_description" 
                               id="short_description"
                               value="{{ old('short_description') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               required>
                        @error('short_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="long_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Long Description
                        </label>
                        <textarea name="long_description" 
                                  id="long_description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                  required>{{ old('long_description') }}</textarea>
                        @error('long_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="starting_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Starting Price (₦)
                        </label>
                        <input type="number" 
                               name="starting_price" 
                               id="starting_price"
                               value="{{ old('starting_price') }}"
                               step="0.01"
                               min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               required>
                        @error('starting_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Icon Name
                        </label>
                        <input type="text" 
                               name="icon_name" 
                               id="icon_name"
                               value="{{ old('icon_name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               required>
                        @error('icon_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="is_featured" 
                               id="is_featured"
                               value="1"
                               {{ old('is_featured') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Featured Service
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                id="submitButton"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            <svg id="loadingIcon" class="hidden w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="buttonText">Create Service</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const button = document.getElementById('submitButton');
    const loadingIcon = document.getElementById('loadingIcon');
    const buttonText = document.getElementById('buttonText');
    
    button.disabled = true;
    loadingIcon.classList.remove('hidden');
    buttonText.textContent = 'Creating...';

    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();
        if (data.success) {
            showNotification(data.title, data.message);
            window.location.href = data.redirect;
        } else {
            showNotification('Error', data.message || 'Failed to create service', 'error');
            button.disabled = false;
            loadingIcon.classList.add('hidden');
            buttonText.textContent = 'Create Service';
        }
    } catch (error) {
        showNotification('An error occurred while creating the service', 'error');
        button.disabled = false;
        loadingIcon.classList.add('hidden');
        buttonText.textContent = 'Create Service';
    }
});
</script>
@endpush
@endsection 