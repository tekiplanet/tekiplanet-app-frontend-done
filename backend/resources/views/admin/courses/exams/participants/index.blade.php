@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.courses.exams.show', [$course, $exam]) }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    Exam Participants
                </h2>
                <p class="text-sm text-gray-500">{{ $exam->title }}</p>
            </div>
        </div>
    </div>

    <!-- Search/Filter Section -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 mb-6">
        <form action="{{ route('admin.courses.exams.participants.index', [$course, $exam]) }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="col-span-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name or email..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="passed" {{ request('status') === 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <select name="sort" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="score" {{ request('sort') === 'score' ? 'selected' : '' }}>Score</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 mb-6">
        <form id="bulkActionForm" class="flex flex-wrap gap-4">
            <select id="bulkAction" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select Bulk Action</option>
                <option value="status">Change Status</option>
                <option value="score">Set Score</option>
            </select>
            
            <select id="bulkStatus" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 hidden">
                <option value="pending">Pending</option>
                <option value="passed">Passed</option>
                <option value="failed">Failed</option>
            </select>
            
            <div id="bulkScoreInputs" class="flex gap-2 hidden">
                <input type="number" id="bulkScore" placeholder="Score" 
                       class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="self-center">/</span>
                <input type="number" id="bulkTotalScore" placeholder="Total" 
                       class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <button type="submit" 
                    id="applyBulkAction"
                    disabled
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                <svg id="bulkLoadingSpinner" class="animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="bulkActionBtnText">Apply to Selected</span>
            </button>
        </form>
    </div>

    <!-- Participants List -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                        <th class="px-4 py-3">
                            <input type="checkbox" id="selectAll" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Score</th>
                        <th class="px-4 py-3">Attempt Date</th>
                        <th class="px-4 py-3">Time Taken</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($participants as $participant)
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-4 py-3">
                                <input type="checkbox" 
                                       name="selected_users[]" 
                                       value="{{ $participant->id }}" 
                                       class="user-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <!-- ... rest of the row content ... -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-center text-gray-500">
                                No participants found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $participants->links() }}
        </div>
    </div>
</div>

<!-- Individual Action Modal -->
@include('admin.courses.exams.participants._action_modal')

@endsection

@push('scripts')
@include('admin.courses.exams.participants._scripts')
@endpush 