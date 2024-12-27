@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.hustles.applications.index', $hustle) }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    Application Details
                </h2>
                <p class="text-sm text-gray-500">{{ $hustle->title }}</p>
            </div>
        </div>
        @if($application->status === 'pending')
            <div class="flex gap-2">
                <button onclick="updateApplicationStatus(
                    '{{ route('admin.hustles.applications.update-status', [$hustle, $application]) }}',
                    'approved',
                    'Approve'
                )" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Approve Application
                </button>
                <button onclick="updateApplicationStatus(
                    '{{ route('admin.hustles.applications.update-status', [$hustle, $application]) }}',
                    'rejected',
                    'Reject'
                )" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject Application
                </button>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Application Information -->
        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Application Information
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-500 dark:text-gray-400">Status</label>
                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $application->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($application->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                            'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($application->status) }}
                    </span>
                </div>
                <div>
                    <label class="text-sm text-gray-500 dark:text-gray-400">Applied At</label>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $application->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
                @if($application->status !== 'pending')
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Status Updated At</label>
                        <p class="text-gray-900 dark:text-gray-100">
                            {{ $application->updated_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Professional Information -->
        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Professional Information
            </h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <img class="h-16 w-16 rounded-full" 
                         src="{{ $application->professional->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($application->professional->user->name) }}" 
                         alt="{{ $application->professional->user->name }}">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $application->professional->user->name }}
                        </h4>
                        <p class="text-sm text-gray-500">
                            {{ $application->professional->user->email }}
                        </p>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-gray-500 dark:text-gray-400">Category</label>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $application->professional->category->name }}
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-500 dark:text-gray-400">Phone</label>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $application->professional->user->phone }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.professionals.show', $application->professional) }}" 
                       class="text-blue-600 hover:text-blue-900">
                        View Professional Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($application->status === 'approved')
        <!-- Payment Information -->
        <div class="mt-6 bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Payment Information
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Total Budget</label>
                        <p class="text-gray-900 dark:text-gray-100">
                            ₦{{ number_format($hustle->budget, 2) }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Initial Payment</label>
                        <p class="text-gray-900 dark:text-gray-100">
                            {{ $hustle->initial_payment_released ? 'Released' : 'Pending' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Final Payment</label>
                        <p class="text-gray-900 dark:text-gray-100">
                            {{ $hustle->final_payment_released ? 'Released' : 'Pending' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
    @include('admin.hustles.applications._status-update-script')
@endpush 