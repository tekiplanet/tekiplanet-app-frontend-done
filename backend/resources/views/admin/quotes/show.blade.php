@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Quote Details
        </h2>
        <a href="{{ route('admin.quotes.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Back to Quotes
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quote Information -->
        <div class="md:col-span-2 bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Quote Information
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Service</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $quote->service->name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        <div class="space-y-2">
                            <div class="font-medium">
                                {{ $quote->user->first_name }} {{ $quote->user->last_name }}
                            </div>
                            <div class="text-gray-600">
                                <div>Email: {{ $quote->user->email }}</div>
                                @if($quote->user->phone)
                                    <div>Phone: {{ $quote->user->phone }}</div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $quote->user->id) }}" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                                    <span>View Full Profile</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Industry</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $quote->industry }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Budget Range</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $quote->budget_range }}
                    </dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Project Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $quote->project_description }}
                    </dd>
                </div>
                <!-- Debug Info -->
                @php
                    \Log::info('Quote Fields:', ['fields' => $quote->quote_fields]);
                    \Log::info('Service Quote Fields:', ['fields' => $quote->service->quoteFields->toArray()]);
                @endphp

                @if($quote->quote_fields)
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Additional Information</dt>
                        <dd class="mt-1">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500">
                                    @php
                                        \Log::info('Quote Fields Keys:', ['keys' => array_keys($quote->quote_fields)]);
                                        // Get all the fields at once to avoid multiple queries
                                        $serviceQuoteFields = \App\Models\ServiceQuoteField::whereIn('id', array_keys($quote->quote_fields))
                                            ->pluck('label', 'id')
                                            ->toArray();
                                        \Log::info('Service Quote Fields Found:', ['fields' => $serviceQuoteFields]);
                                    @endphp
                                    @foreach($quote->quote_fields as $key => $value)
                                        @php
                                            $label = $serviceQuoteFields[$key] ?? 'Field ' . ($loop->iteration);
                                        @endphp
                                        <div class="mb-4">
                                            <dt class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ $label }}
                                            </dt>
                                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                                @if(is_array($value) || is_string($value) && str_contains($value, ','))
                                                    <ul class="list-disc list-inside">
                                                        @foreach((is_array($value) ? $value : explode(',', $value)) as $item)
                                                            <li>{{ trim($item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </dd>
                    </div>
                @endif
            </dl>

            <!-- Status and Assignment Section -->
            <div class="mt-6 border-t pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Select Status</option>
                            <option value="pending" {{ $quote->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $quote->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="accepted" {{ $quote->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ $quote->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign To</label>
                        <select id="assignedTo" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Select Assignee</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" 
                                        {{ $quote->assigned_to === $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Section -->
        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Messages
            </h3>
            <div class="space-y-4 max-h-96 overflow-y-auto mb-4" id="messages">
                @foreach($quote->messages as $message)
                    <div class="flex gap-4 {{ $message->sender_type === 'admin' ? 'flex-row-reverse' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                @if($message->sender_type === 'admin')
                                    {{ substr($message->user->name, 0, 1) }}
                                @else
                                    {{ substr($message->user->first_name, 0, 1) }}
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 {{ $message->sender_type === 'admin' ? 'bg-blue-100' : 'bg-gray-100' }} rounded-lg p-4">
                            <div class="text-sm text-gray-600">
                                @if($message->sender_type === 'admin')
                                    {{ $message->user->name }}
                                @else
                                    {{ $message->user->first_name }} {{ $message->user->last_name }}
                                @endif
                            </div>
                            <div class="mt-1">
                                {{ $message->message }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $message->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <form id="messageForm" class="mt-4">
                <textarea id="message" 
                          rows="3"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                          placeholder="Type your message..."></textarea>
                <button type="submit" 
                        class="mt-2 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update Status
document.getElementById('status').addEventListener('change', function() {
    fetch(`{{ route('admin.quotes.update-status', $quote) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: this.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Quote status updated successfully');
        }
    })
    .catch(error => {
        showNotification('Error', 'Failed to update status', 'error');
    });
});

// Assign Quote
document.getElementById('assignedTo').addEventListener('change', function() {
    if (!this.value) return;

    fetch(`{{ route('admin.quotes.assign', $quote) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            assigned_to: this.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Quote assigned successfully');
        }
    })
    .catch(error => {
        showNotification('Error', 'Failed to assign quote', 'error');
    });
});

// Send Message
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message');
    const message = messageInput.value.trim();
    
    if (!message) return;

    fetch(`{{ route('admin.quotes.messages.send', $quote) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            const messagesContainer = document.getElementById('messages');
            // Add new message to the container
            // You might want to create a proper template for this
            location.reload(); // Simple solution - reload the page
        }
    })
    .catch(error => {
        showNotification('Error', 'Failed to send message', 'error');
    });
});
</script>
@endpush
@endsection 