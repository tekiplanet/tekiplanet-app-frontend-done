@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <!-- Header with back button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.courses.exams.index', $course) }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                {{ $exam->title }}
            </h2>
        </div>
        <div class="flex items-center gap-3">
            <form id="statusForm" class="flex items-center gap-2">
                <select id="examStatus" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        {{ $exam->status === 'completed' ? 'disabled' : '' }}>
                    <option value="upcoming" {{ $exam->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ $exam->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ $exam->status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="submit" 
                        id="updateStatusBtn"
                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
                        {{ $exam->status === 'completed' ? 'disabled' : '' }}>
                    <svg id="loadingSpinner" class="animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="updateStatusBtnText">Update Status</span>
                </button>
            </form>
            <span class="px-3 py-1 text-sm rounded-full {{ 
                $exam->status === 'upcoming' ? 'bg-yellow-100 text-yellow-800' : 
                ($exam->status === 'ongoing' ? 'bg-green-100 text-green-800' : 
                ($exam->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) 
            }}">
                {{ ucfirst($exam->status) }}
            </span>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Exam Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                    <p class="font-medium">{{ $exam->date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                    <p class="font-medium">{{ $exam->duration }} ({{ $exam->duration_minutes }} minutes)</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                    <span class="px-2 py-1 text-xs rounded-full inline-block mt-1
                        {{ $exam->type === 'multiple_choice' ? 'bg-blue-100 text-blue-800' : 
                           ($exam->type === 'true_false' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ str_replace('_', ' ', ucfirst($exam->type)) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Difficulty</p>
                    <p class="font-medium capitalize">{{ $exam->difficulty }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Questions</p>
                    <p class="font-medium">{{ $exam->total_questions }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pass Percentage</p>
                    <p class="font-medium">{{ $exam->pass_percentage }}%</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                    <p class="font-medium">{{ $exam->description ?: 'No description provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Exam Statistics</h3>
            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Participants</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $userExams->total() }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Passed</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $userExams->where('status', 'passed')->count() }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Failed</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $userExams->where('status', 'failed')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants List -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Participants</h3>
        </div>

        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Score</th>
                        <th class="px-4 py-3">Attempt Date</th>
                        <th class="px-4 py-3">Time Taken</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($userExams as $userExam)
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" 
                                         src="{{ $userExam->user->avatar_url }}" 
                                         alt="{{ $userExam->user->name }}">
                                    <div class="ml-3">
                                        <p class="font-semibold">{{ $userExam->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $userExam->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $userExam->status === 'passed' ? 'bg-green-100 text-green-800' : 
                                       ($userExam->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($userExam->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $userExam->score ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $userExam->attempt_date ? $userExam->attempt_date->format('M d, Y H:i') : 'Not attempted' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $userExam->time_taken ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                No participants found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile List (hidden on desktop) -->
        <div class="md:hidden">
            @forelse($userExams as $userExam)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full" 
                                 src="{{ $userExam->user->avatar_url }}" 
                                 alt="{{ $userExam->user->name }}">
                            <div class="ml-3">
                                <p class="font-semibold">{{ $userExam->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $userExam->user->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $userExam->status === 'passed' ? 'bg-green-100 text-green-800' : 
                               ($userExam->status === 'failed' ? 'bg-red-100 text-red-800' : 
                               'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($userExam->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-gray-500">Score</p>
                            <p class="font-medium">{{ $userExam->score ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Time Taken</p>
                            <p class="font-medium">{{ $userExam->time_taken ?? 'N/A' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500">Attempt Date</p>
                            <p class="font-medium">
                                {{ $userExam->attempt_date ? $userExam->attempt_date->format('M d, Y H:i') : 'Not attempted' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    No participants found
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $userExams->links() }}
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
const statusForm = document.getElementById('statusForm');
const updateStatusBtn = document.getElementById('updateStatusBtn');
const loadingSpinner = document.getElementById('loadingSpinner');
const updateStatusBtnText = document.getElementById('updateStatusBtnText');
const statusLabel = document.querySelector('span.rounded-full'); // Get the status label

// Function to get status badge classes
function getStatusClasses(status) {
    switch(status) {
        case 'upcoming':
            return 'bg-yellow-100 text-yellow-800';
        case 'ongoing':
            return 'bg-green-100 text-green-800';
        case 'completed':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-red-100 text-red-800';
    }
}

statusForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (updateStatusBtn.disabled) return;
    
    const newStatus = document.getElementById('examStatus').value;
    
    // Set loading state
    updateStatusBtn.disabled = true;
    loadingSpinner.classList.remove('hidden');
    updateStatusBtnText.textContent = 'Updating...';
    
    fetch(`/admin/courses/{{ $course->id }}/exams/{{ $exam->id }}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(async response => {
        if (!response.ok) {
            const text = await response.text();
            try {
                const json = JSON.parse(text);
                throw new Error(json.message || `HTTP error! status: ${response.status}`);
            } catch (e) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the status label
            statusLabel.className = `px-3 py-1 text-sm rounded-full ${getStatusClasses(newStatus)}`;
            statusLabel.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            
            // If status is completed, disable the form
            if (newStatus === 'completed') {
                document.getElementById('examStatus').disabled = true;
                updateStatusBtn.disabled = true;
            }
            
            showNotification('', 'Exam status updated successfully', 'success');
            
            // Force reload after a short delay to ensure all updates are reflected
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to update exam status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('', error.message || 'An error occurred', 'error');
    })
    .finally(() => {
        // Reset loading state
        if (newStatus !== 'completed') {
            updateStatusBtn.disabled = false;
        }
        loadingSpinner.classList.add('hidden');
        updateStatusBtnText.textContent = 'Update Status';
    });
});
</script>
@endpush 