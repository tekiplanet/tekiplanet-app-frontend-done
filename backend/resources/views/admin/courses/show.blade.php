@extends('admin.layouts.app')

@section('content')
<div x-data="{ open: false, courseId: null }">
    <div class="container px-6 mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.courses.index') }}" 
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    {{ $course->title }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-sm rounded-full {{ 
                    $course->status === 'active' ? 'bg-green-100 text-green-800' : 
                    ($course->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                }}">
                    {{ ucfirst($course->status) }}
                </span>
                <button @click="open = true; courseId = '{{ $course->id }}'"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Course
                </button>
            </div>
        </div>

        <!-- Course Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Course Image and Basic Info -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md dark:bg-gray-800 overflow-hidden">
                <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-full h-64 object-cover">
                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($course->category)
                            <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                                {{ $course->category }}
                            </span>
                        @endif
                        @if($course->category_id && $course->category()->exists())
                            <span class="px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded-full">
                                {{ $course->category()->first()->name }}
                            </span>
                        @endif
                        <span class="px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded-full">
                            {{ ucfirst($course->level) }}
                        </span>
                        <span class="px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded-full">
                            {{ $course->duration_hours }} months
                        </span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        {{ $course->description }}
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <h4 class="font-semibold mb-2">Prerequisites:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                                @php
                                    $prerequisites = is_string($course->prerequisites) 
                                        ? json_decode($course->prerequisites, true) 
                                        : $course->prerequisites;
                                @endphp
                                @forelse($prerequisites ?? [] as $prerequisite)
                                    <li>{{ $prerequisite }}</li>
                                @empty
                                    <li class="text-gray-500">No prerequisites specified</li>
                                @endforelse
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">Learning Outcomes:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                                @php
                                    $learningOutcomes = is_string($course->learning_outcomes) 
                                        ? json_decode($course->learning_outcomes, true) 
                                        : $course->learning_outcomes;
                                @endphp
                                @forelse($learningOutcomes ?? [] as $outcome)
                                    <li>{{ $outcome }}</li>
                                @empty
                                    <li class="text-gray-500">No learning outcomes specified</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Stats -->
            <div class="space-y-6">
                <!-- Instructor Info -->
                <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
                    <h3 class="text-lg font-semibold mb-4">Instructor</h3>
                    <div class="flex items-center gap-4">
                        <img src="{{ $course->instructor->avatar ?? asset('images/default-avatar.png') }}" 
                             alt="{{ $course->instructor->full_name }}"
                             class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h4 class="font-semibold">{{ $course->instructor->full_name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $course->instructor->expertise }}</p>
                        </div>
                    </div>
                </div>

                <!-- Course Statistics -->
                <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
                    <h3 class="text-lg font-semibold mb-4">Course Statistics</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $course->total_students }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Students</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ number_format($course->rating, 1) }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Rating ({{ $course->total_reviews }})
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Info -->
                <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
                    <h3 class="text-lg font-semibold mb-4">Price Information</h3>
                    <div class="text-3xl font-bold text-gray-700 dark:text-gray-200">
                        ₦{{ number_format($course->price, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div x-data="{ activeTab: 'modules' }" class="bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-4 px-4" aria-label="Tabs">
                    <button @click="activeTab = 'modules'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'modules' }"
                            class="px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-gray-300">
                        Modules & Lessons
                    </button>
                    <button @click="activeTab = 'students'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'students' }"
                            class="px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-gray-300">
                        Enrolled Students
                    </button>
                    <button @click="activeTab = 'reviews'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'reviews' }"
                            class="px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-gray-300">
                        Reviews
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Modules Tab -->
                <div x-show="activeTab === 'modules'">
                    @if($course->modules->isEmpty())
                        <p class="text-gray-500 text-center py-4">No modules added yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($course->modules as $module)
                                <div class="border rounded-lg p-4">
                                    <h3 class="font-semibold mb-2">{{ $module->title }}</h3>
                                    @if($module->lessons->isEmpty())
                                        <p class="text-sm text-gray-500">No lessons added yet.</p>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($module->lessons as $lesson)
                                                <li class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $lesson->title }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Students Tab -->
                <div x-show="activeTab === 'students'">
                    @if($course->enrollments->isEmpty())
                        <p class="text-gray-500 text-center py-4">No students enrolled yet.</p>
                    @else
                        <div class="space-y-4">
                            <!-- Student list will go here -->
                        </div>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div x-show="activeTab === 'reviews'">
                    @if($course->reviews->isEmpty())
                        <p class="text-gray-500 text-center py-4">No reviews yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($course->reviews as $review)
                                <div class="border-b pb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold">{{ $review->user->name }}</span>
                                            <span class="text-yellow-400">
                                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.courses.partials.edit-modal')
</div>

@push('scripts')
<script>
function loadCourse(courseId) {
    fetch(`/admin/courses/${courseId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate form fields
                const form = document.getElementById('editCourseForm');
                form.action = `/admin/courses/${courseId}`;

                // Set basic fields
                ['title', 'description', 'category_id', 'instructor_id', 'level', 
                 'price', 'duration_hours', 'image_url', 'status'].forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) input.value = data.course[field];
                });

                // Handle arrays (prerequisites and learning outcomes)
                const prerequisites = data.course.prerequisites;
                const learningOutcomes = data.course.learning_outcomes;

                form.querySelector('[name="prerequisites"]').value = 
                    Array.isArray(prerequisites) 
                        ? prerequisites.join('\n')
                        : JSON.parse(prerequisites).join('\n');

                form.querySelector('[name="learning_outcomes"]').value = 
                    Array.isArray(learningOutcomes)
                        ? learningOutcomes.join('\n')
                        : JSON.parse(learningOutcomes).join('\n');
            }
        })
        .catch(error => {
            console.error('Error loading course:', error);
            showNotification('Error', 'Failed to load course data', 'error');
        });
}

function updateCourse(e) {
    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const loadingSpinner = submitButton.querySelector('.loading-spinner');
    
    // Disable button and show spinner
    submitButton.disabled = true;
    loadingSpinner.classList.remove('hidden');
    
    // Prepare form data
    const formData = new FormData(form);
    const jsonData = {};
    
    // Convert prerequisites and learning outcomes to arrays
    for (const [key, value] of formData.entries()) {
        if (key === '_method' || key === '_token') continue;
        if (key === 'prerequisites' || key === 'learning_outcomes') {
            jsonData[key] = value.split('\n')
                .map(item => item.trim())
                .filter(item => item.length > 0);
        } else {
            jsonData[key] = value;
        }
    }

    fetch(form.action, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'PUT'
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to update course');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Course updated successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.message || 'Failed to update course');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error', error.message, 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        loadingSpinner.classList.add('hidden');
    });
}
</script>
@endpush
@endsection 