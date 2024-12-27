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

        @if($application->status === 'approved')
            <!-- Hustle Status Management -->
            <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                    Hustle Status
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Current Status:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $hustle->status === 'approved' ? 'bg-yellow-100 text-yellow-800' : 
                               ($hustle->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                               ($hustle->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($hustle->status) }}
                        </span>
                    </div>

                    @if($hustle->status === 'approved')
                        <button onclick="updateHustleStatus('{{ route('admin.hustles.update-status', $hustle) }}', 'in_progress')" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Mark as In Progress
                        </button>
                    @endif
                </div>
            </div>

            <!-- Payments Section -->
            @if($hustle->status === 'in_progress')
                <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                        Payments
                    </h3>
                    <div class="space-y-6">
                        @foreach($hustle->payments as $payment)
                            <div class="border-b pb-4 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($payment->payment_type) }} Payment 
                                            ({{ $payment->payment_type === 'initial' ? '40%' : '60%' }})
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            Amount: ₦{{ number_format($payment->amount, 2) }}
                                        </p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>

                                @if($payment->status === 'pending')
                                    <button onclick="updatePaymentStatus(
                                        '{{ route('admin.hustles.payments.update-status', [$hustle, $payment]) }}',
                                        'completed'
                                    )" class="text-sm text-green-600 hover:text-green-900">
                                        Mark as Completed
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
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