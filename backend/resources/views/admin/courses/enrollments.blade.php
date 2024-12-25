@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.courses.show', $course->id) }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                {{ $course->title }} - Enrollments
            </h2>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6">
        <form action="{{ route('admin.courses.enrollments', $course->id) }}" method="GET" 
              class="space-y-4 md:space-y-0 md:grid md:grid-cols-4 md:gap-4">
            <!-- Search -->
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search students..." 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Status Filter -->
            <select name="status" 
                    class="px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                    Pending
                </option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                    Completed
                </option>
                <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>
                    Dropped
                </option>
            </select>

            <!-- Payment Status Filter -->
            <select name="payment_status" 
                    class="px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Payment Status</option>
                <option value="fully_paid" {{ request('payment_status') === 'fully_paid' ? 'selected' : '' }}>
                    Fully Paid
                </option>
                <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>
                    Partially Paid
                </option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>
                    Unpaid
                </option>
            </select>

            <!-- Sort -->
            <div class="flex gap-2">
                <select name="sort_by" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="enrolled_at" {{ request('sort_by') === 'enrolled_at' ? 'selected' : '' }}>
                        Enrollment Date
                    </option>
                    <option value="progress" {{ request('sort_by') === 'progress' ? 'selected' : '' }}>
                        Progress
                    </option>
                </select>
                <select name="sort_order" 
                        class="px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>
                        Desc
                    </option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>
                        Asc
                    </option>
                </select>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Add this after the search filters and before the list -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div x-data="{ showBulkActions: false, selectedEnrollments: [] }">
            <!-- Bulk Actions -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" 
                           class="form-checkbox rounded border-gray-300"
                           x-on:change="$event.target.checked ? selectedEnrollments = enrollments.map(e => e.id) : selectedEnrollments = []">
                    <span class="ml-2">Select All</span>
                </label>
                
                <div class="flex items-center gap-4" x-show="selectedEnrollments.length > 0">
                    <span class="text-sm text-gray-600">
                        <span x-text="selectedEnrollments.length"></span> selected
                    </span>
                    
                    <!-- Status Update -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Status
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button @click="updateBulkEnrollments('status', 'active')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Active
                                </button>
                                <button @click="updateBulkEnrollments('status', 'pending')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Pending
                                </button>
                                <button @click="updateBulkEnrollments('status', 'completed')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Completed
                                </button>
                                <button @click="updateBulkEnrollments('status', 'dropped')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Dropped
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Update -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Update Payment
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button @click="updateBulkEnrollments('payment_status', 'fully_paid')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Fully Paid
                                </button>
                                <button @click="updateBulkEnrollments('payment_status', 'partially_paid')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Partially Paid
                                </button>
                                <button @click="updateBulkEnrollments('payment_status', 'unpaid')"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-100">
                                    Set Unpaid
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Update -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Update Progress
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
                            <div class="p-4">
                                <input type="number" 
                                       min="0" 
                                       max="100" 
                                       class="w-full px-3 py-2 border rounded-lg"
                                       placeholder="Enter progress %"
                                       x-ref="progressInput">
                                <button @click="updateBulkEnrollments('progress', $refs.progressInput.value)"
                                        class="w-full mt-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollments List -->
    @if($enrollments->isEmpty())
        <p class="text-gray-500 text-center py-4">No students enrolled yet.</p>
    @else
        <!-- Mobile View (Card Layout) -->
        <div class="block md:hidden space-y-4">
            @foreach($enrollments as $enrollment)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <img class="h-10 w-10 rounded-full" 
                             src="{{ $enrollment->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($enrollment->user->name) }}" 
                             alt="{{ $enrollment->user->name }}">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $enrollment->user->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $enrollment->user->email }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Enrolled:</span>
                            <span class="text-sm">{{ $enrollment->enrolled_at ? date('M d, Y', strtotime($enrollment->enrolled_at)) : 'N/A' }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Progress:</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded">
                                    <div class="h-full bg-blue-600 rounded" 
                                         style="width: {{ $enrollment->progress }}%">
                                    </div>
                                </div>
                                <span class="text-sm">{{ number_format($enrollment->progress, 1) }}%</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Status:</span>
                            <span class="px-2 text-xs font-semibold rounded-full 
                                {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($enrollment->status === 'active' ? 'bg-blue-100 text-blue-800' : 
                                   ($enrollment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Payment:</span>
                            <span class="px-2 text-xs font-semibold rounded-full 
                                {{ $enrollment->payment_status === 'fully_paid' ? 'bg-green-100 text-green-800' : 
                                   ($enrollment->payment_status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800') }}">
                                {{ str_replace('_', ' ', ucfirst($enrollment->payment_status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop View (Table Layout) -->
        <div class="hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Enrolled Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Progress
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Payment Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="{{ $enrollment->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($enrollment->user->name) }}" 
                                                 alt="{{ $enrollment->user->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $enrollment->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $enrollment->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $enrollment->enrolled_at ? date('M d, Y', strtotime($enrollment->enrolled_at)) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="relative w-48 h-2 bg-gray-200 rounded">
                                        <div class="absolute top-0 left-0 h-full bg-blue-600 rounded" 
                                             style="width: {{ $enrollment->progress }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($enrollment->progress, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($enrollment->status === 'active' ? 'bg-blue-100 text-blue-800' : 
                                           ($enrollment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $enrollment->payment_status === 'fully_paid' ? 'bg-green-100 text-green-800' : 
                                           ($enrollment->payment_status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ str_replace('_', ' ', ucfirst($enrollment->payment_status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function updateBulkEnrollments(action, value) {
    if (!selectedEnrollments.length) {
        showNotification('Error', 'Please select enrollments to update', 'error');
        return;
    }

    fetch(`{{ route('admin.courses.enrollments.bulk-update', $course->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            enrollment_ids: selectedEnrollments,
            action: action,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Enrollments updated successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.message || 'Failed to update enrollments');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error', error.message, 'error');
    });
}
</script>
@endpush 