@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    {{ $business->business_name }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $business->business_email }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="openEditModal()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Details
                </button>
                <button onclick="toggleStatus()" 
                        class="px-4 py-2 {{ $business->status === 'active' ? 'bg-red-600' : 'bg-green-600' }} text-white rounded-lg hover:{{ $business->status === 'active' ? 'bg-red-700' : 'bg-green-700' }}">
                    {{ $business->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Business Information -->
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Business Information
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Registration Number</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $business->registration_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Tax Number</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $business->tax_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Phone</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $business->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Website</label>
                    <p class="text-gray-800 dark:text-gray-200">
                        @if($business->website)
                            <a href="{{ $business->website }}" target="_blank" class="text-blue-600 hover:underline">
                                {{ $business->website }}
                            </a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Address</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $business->address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Statistics
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Customers</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                        {{ $business->customers()->count() }}
                    </p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Invoices</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                        {{ $business->invoices()->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl dark:bg-gray-800 w-full max-w-2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                    Edit Business Details
                </h3>
                <form id="editForm" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Business Name</label>
                        <input type="text" name="business_name" value="{{ $business->business_name }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Business Email</label>
                        <input type="email" name="business_email" value="{{ $business->business_email }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    <!-- Add other fields similar to above -->
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex justify-end gap-4">
                <button onclick="closeEditModal()" 
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-700">
                    Cancel
                </button>
                <button onclick="submitEdit()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

async function submitEdit() {
    try {
        const form = document.getElementById('editForm');
        const response = await fetch('{{ route("admin.businesses.update", $business) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form)))
        });

        const data = await response.json();

        if (response.ok) {
            showNotification('Success', data.message);
            closeEditModal();
            location.reload();
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    } catch (error) {
        showNotification('Error', error.message, 'error');
    }
}

async function toggleStatus() {
    try {
        const response = await fetch('{{ route("admin.businesses.toggle-status", $business) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showNotification('Success', data.message);
            location.reload();
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    } catch (error) {
        showNotification('Error', error.message, 'error');
    }
}
</script>
@endpush 