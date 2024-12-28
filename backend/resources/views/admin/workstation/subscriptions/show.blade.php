@extends('admin.layouts.app')

@section('title', 'View Subscription')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subscription Details
        </h2>
        <a href="{{ route('admin.workstation.subscriptions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Subscriptions
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Subscription Details -->
                <div class="col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between items-start mb-6">
                                <h3 class="text-lg font-semibold">Subscription Information</h3>
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Update Status
                                    </button>
                                    
                                    <!-- Status Update Dropdown -->
                                    <div x-show="open" 
                                        @click.away="open = false"
                                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                        x-cloak>
                                        <div class="py-1">
                                            @foreach(['active', 'expired', 'cancelled', 'pending'] as $status)
                                                <form action="{{ route('admin.workstation.subscriptions.update-status', $subscription) }}"
                                                    method="POST"
                                                    x-data="{ loading: false }"
                                                    @submit.prevent="
                                                        loading = true;
                                                        fetch($el.action, {
                                                            method: 'PATCH',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            body: JSON.stringify({ status: '{{ $status }}' })
                                                        })
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.success) {
                                                                window.location.reload();
                                                            } else {
                                                                alert('Failed to update status');
                                                            }
                                                        })
                                                        .catch(error => {
                                                            alert('An error occurred');
                                                            console.error(error);
                                                        })
                                                        .finally(() => loading = false)
                                                    ">
                                                    <button type="submit" 
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                                                        :disabled="loading || '{{ $subscription->status }}' === '{{ $status }}'">
                                                        <span x-show="loading" class="mr-2">
                                                            <svg class="animate-spin h-4 w-4 text-gray-500 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                        </span>
                                                        {{ ucfirst($status) }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tracking Code</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->tracking_code }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $subscription->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->start_date->format('M d, Y') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->end_date->format('M d, Y') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($subscription->payment_type) }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900">₦{{ number_format($subscription->total_amount, 2) }}</dd>
                                </div>

                                @if($subscription->cancelled_at)
                                    <div class="col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Cancellation Details</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            Cancelled on {{ $subscription->cancelled_at->format('M d, Y H:i:s') }}
                                            @if($subscription->cancellation_reason)
                                                <br>
                                                Reason: {{ $subscription->cancellation_reason }}
                                            @endif
                                            @if($subscription->refund_amount)
                                                <br>
                                                Refund Amount: ₦{{ number_format($subscription->refund_amount, 2) }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- User and Plan Information -->
                <div class="space-y-6">
                    <!-- User Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">User Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->user->full_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->user->email }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Plan Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Plan Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Plan Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->plan->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription->plan->duration_days }} days</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900">₦{{ number_format($subscription->plan->price, 2) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Section -->
            @if($subscription->payments->isNotEmpty())
                <div class="mt-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Payment History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            @if($subscription->payment_type === 'installment')
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($subscription->payments as $payment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ₦{{ number_format($payment->amount, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ ucfirst($payment->type) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $payment->due_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $payment->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                @if($subscription->payment_type === 'installment')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $payment->installment_number }} of {{ $subscription->plan->installment_months }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @endpush
@endsection 