@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Edit Service
        </h2>
        <a href="{{ route('admin.services.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Back to Services
        </a>
    </div>

    <div class="mt-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('admin.services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')

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
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
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
                               value="{{ old('name', $service->name) }}"
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
                               value="{{ old('short_description', $service->short_description) }}"
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
                                  required>{{ old('long_description', $service->long_description) }}</textarea>
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
                               value="{{ old('starting_price', $service->starting_price) }}"
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
                               value="{{ old('icon_name', $service->icon_name) }}"
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
                               {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Featured Service
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Update Service
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 