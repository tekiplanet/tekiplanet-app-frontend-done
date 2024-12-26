@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Shipping Zones
        </h2>
        <button onclick="openCreateModal()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Add Zone
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6">
        <form action="{{ route('admin.shipping.zones.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Search
                </label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search zones..."
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Has Methods Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Has Methods
                </label>
                <select name="has_methods" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="yes" {{ request('has_methods') === 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ request('has_methods') === 'no' ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <!-- Has Addresses Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Has Addresses
                </label>
                <select name="has_addresses" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="yes" {{ request('has_addresses') === 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ request('has_addresses') === 'no' ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" 
                        class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'has_methods', 'has_addresses']))
                    <a href="{{ route('admin.shipping.zones.index') }}" 
                       class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Zones List -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Zone Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Shipping Methods
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Addresses
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($zones as $zone)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $zone->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $zone->rates_count }} methods
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $zone->addresses_count }} addresses
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditModal({{ json_encode($zone) }})" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    Edit
                                </button>
                                <button onclick="deleteZone('{{ $zone->id }}')"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No shipping zones found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
@include('admin.shipping.zones.partials.modal')

@push('scripts')
<script>
const modal = document.getElementById('zoneModal');
const form = document.getElementById('zoneForm');
const submitButton = form.querySelector('button[type="submit"]');
const loadingSpinner = document.getElementById('loadingSpinner');
const submitButtonText = document.getElementById('submitButtonText');
let isEditMode = false;

function openCreateModal() {
    isEditMode = false;
    form.reset();
    document.getElementById('modalTitle').textContent = 'Create Shipping Zone';
    submitButtonText.textContent = 'Create Zone';
    modal.classList.remove('hidden');
}

function openEditModal(zone) {
    isEditMode = true;
    document.getElementById('zoneId').value = zone.id;
    document.getElementById('zoneName').value = zone.name;

    document.getElementById('modalTitle').textContent = 'Edit Shipping Zone';
    submitButtonText.textContent = 'Update Zone';
    modal.classList.remove('hidden');
}

function closeModal() {
    modal.classList.add('hidden');
    form.reset();
}

async function handleSubmit(e) {
    e.preventDefault();
    
    submitButton.disabled = true;
    loadingSpinner.classList.remove('hidden');
    submitButtonText.textContent = isEditMode ? 'Updating...' : 'Creating...';
    
    const formData = new FormData(form);
    const zoneId = document.getElementById('zoneId').value;
    
    try {
        const url = isEditMode 
            ? `{{ url('admin/shipping/zones') }}/${zoneId}`
            : '{{ route('admin.shipping.zones.store') }}';
        
        const response = await fetch(url, {
            method: isEditMode ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }

        if (data.success) {
            showNotification('Success', data.message);
            window.location.reload();
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error', error.message, 'error');
    } finally {
        submitButton.disabled = false;
        loadingSpinner.classList.add('hidden');
        submitButtonText.textContent = isEditMode ? 'Update Zone' : 'Create Zone';
    }
}

async function deleteZone(zoneId) {
    if (!confirm('Are you sure you want to delete this shipping zone?')) return;

    try {
        const response = await fetch(`{{ url('admin/shipping/zones') }}/${zoneId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }

        if (data.success) {
            showNotification('Success', data.message);
            window.location.reload();
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error', error.message, 'error');
    }
}

// Close modal when clicking outside
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        closeModal();
    }
});
</script>
@endpush
@endsection 