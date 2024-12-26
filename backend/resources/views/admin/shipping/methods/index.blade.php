@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Shipping Methods
        </h2>
        <button onclick="openCreateModal()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Add Method
        </button>
    </div>

    <!-- Methods List -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Method Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Base Cost
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Delivery Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Priority
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($methods as $method)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $method->name }}
                                </div>
                                @if($method->description)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $method->description }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    ₦{{ number_format($method->base_cost, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $method->estimated_days_min }}-{{ $method->estimated_days_max }} days
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $method->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $method->priority }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditModal({{ json_encode($method) }})" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    Edit
                                </button>
                                <button onclick="deleteMethod('{{ $method->id }}')"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No shipping methods found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
@include('admin.shipping.methods.partials.modal')

@push('scripts')
<script>
const modal = document.getElementById('methodModal');
const form = document.getElementById('methodForm');
const submitButton = form.querySelector('button[type="submit"]');
const loadingSpinner = document.getElementById('loadingSpinner');
const submitButtonText = document.getElementById('submitButtonText');
let isEditMode = false;

function openCreateModal() {
    isEditMode = false;
    form.reset();
    document.getElementById('modalTitle').textContent = 'Create Shipping Method';
    submitButtonText.textContent = 'Create Method';
    modal.classList.remove('hidden');
}

function openEditModal(method) {
    isEditMode = true;
    document.getElementById('methodId').value = method.id;
    document.getElementById('methodName').value = method.name;
    document.getElementById('methodDescription').value = method.description || '';
    document.getElementById('baseCost').value = method.base_cost;
    document.getElementById('estimatedDaysMin').value = method.estimated_days_min;
    document.getElementById('estimatedDaysMax').value = method.estimated_days_max;
    document.getElementById('isActive').checked = method.is_active;
    document.getElementById('priority').value = method.priority;

    // Set zone rates
    method.zone_rates.forEach(rate => {
        document.querySelector(`[name="zone_rates[${rate.zone_id}][rate]"]`).value = rate.rate;
        document.querySelector(`[name="zone_rates[${rate.zone_id}][estimated_days]"]`).value = rate.estimated_days;
    });

    document.getElementById('modalTitle').textContent = 'Edit Shipping Method';
    submitButtonText.textContent = 'Update Method';
    modal.classList.remove('hidden');
}

function closeModal() {
    modal.classList.add('hidden');
    form.reset();
    // Clear zone rates
    document.querySelectorAll('[name^="zone_rates["]').forEach(input => input.value = '');
}

async function handleSubmit(e) {
    e.preventDefault();
    
    submitButton.disabled = true;
    loadingSpinner.classList.remove('hidden');
    submitButtonText.textContent = isEditMode ? 'Updating...' : 'Creating...';
    
    const formData = new FormData(form);
    const methodId = document.getElementById('methodId').value;
    
    // Convert form data to object and handle boolean
    const formObject = Object.fromEntries(formData);
    formObject.is_active = form.elements.is_active.checked;
    
    try {
        const url = isEditMode 
            ? `{{ url('admin/shipping/methods') }}/${methodId}`
            : '{{ route('admin.shipping.methods.store') }}';
        
        const response = await fetch(url, {
            method: isEditMode ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formObject)
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
        submitButtonText.textContent = isEditMode ? 'Update Method' : 'Create Method';
    }
}

async function deleteMethod(methodId) {
    if (!confirm('Are you sure you want to delete this shipping method?')) return;

    try {
        const response = await fetch(`{{ url('admin/shipping/methods') }}/${methodId}`, {
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