@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Consulting Time Slots
        </h2>
        <div class="flex gap-2">
            <button id="bulkDeleteBtn" 
                    onclick="confirmBulkDelete()" 
                    disabled
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                Delete Selected
            </button>
            <button onclick="openCreateModal()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Add Time Slot
            </button>
            <button onclick="openBulkCreateModal()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Bulk Create
            </button>
        </div>
    </div>

    <!-- Search/Filter Section -->
    <div class="mb-6 bg-white rounded-lg shadow-md dark:bg-gray-800 p-4">
        <form action="{{ route('admin.consulting.timeslots.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="date" 
                       name="date" 
                       value="{{ request('date') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div class="w-full md:w-48">
                <select name="availability" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">All Availability</option>
                    <option value="1" {{ request('availability') === '1' ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ request('availability') === '0' ? 'selected' : '' }}>Not Available</option>
                </select>
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Time Slots List -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-left bg-gray-50 dark:bg-gray-700">
                        <th class="px-6 py-3">
                            <input type="checkbox" 
                                   id="selectAll"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Capacity</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Booked</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($timeSlots as $slot)
                        <tr>
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       value="{{ $slot->id }}"
                                       class="slot-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4">{{ $slot->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $slot->time->format('h:i A') }}</td>
                            <td class="px-6 py-4">{{ $slot->capacity }}</td>
                            <td class="px-6 py-4">{{ $slot->booked_slots }}/{{ $slot->capacity }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $slot->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $slot->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick="openEditModal({{ $slot->id }})" 
                                            class="text-blue-600 hover:text-blue-900">Edit</button>
                                    <button onclick="confirmDelete({{ $slot->id }})" 
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No time slots found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $timeSlots->links() }}
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="timeSlotModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4" id="modalTitle">Add Time Slot</h3>
                <form id="timeSlotForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="date" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Time</label>
                        <input type="time" name="time" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacity</label>
                        <input type="number" name="capacity" required min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_available" id="is_available"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="is_available" class="ml-2 block text-sm text-gray-900">
                            Available for booking
                        </label>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                <button onclick="closeModal()" 
                        class="px-4 py-2 text-gray-700 hover:text-gray-900">
                    Cancel
                </button>
                <button onclick="saveTimeSlot()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Create Modal -->
<div id="bulkCreateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Bulk Create Time Slots</h3>
                <form id="bulkCreateForm" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Days of Week</label>
                        <div class="mt-2 space-x-2">
                            @foreach(['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6, 'Sun' => 7] as $day => $value)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="days[]" value="{{ $value }}"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-600">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Times</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex gap-2">
                                <input type="time" name="times[]" required
                                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" onclick="addTimeInput(this)"
                                        class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">+</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacity per Slot</label>
                        <input type="number" name="capacity" required min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                <button onclick="closeBulkCreateModal()" 
                        class="px-4 py-2 text-gray-700 hover:text-gray-900">
                    Cancel
                </button>
                <button onclick="saveBulkCreate()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTimeSlotId = null;

function openCreateModal() {
    currentTimeSlotId = null;
    document.getElementById('modalTitle').textContent = 'Add Time Slot';
    document.getElementById('timeSlotForm').reset();
    document.getElementById('timeSlotModal').classList.remove('hidden');
}

function openEditModal(id) {
    currentTimeSlotId = id;
    document.getElementById('modalTitle').textContent = 'Edit Time Slot';
    
    // Fetch time slot data and populate form
    fetch(`/admin/consulting/timeslots/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('timeSlotForm');
            form.date.value = data.date;
            form.time.value = data.time;
            form.capacity.value = data.capacity;
            form.is_available.checked = data.is_available;
        });
    
    document.getElementById('timeSlotModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('timeSlotModal').classList.add('hidden');
}

function saveTimeSlot() {
    const form = document.getElementById('timeSlotForm');
    const formData = new FormData(form);
    
    const url = currentTimeSlotId 
        ? `/admin/consulting/timeslots/${currentTimeSlotId}`
        : '/admin/consulting/timeslots';
        
    const method = currentTimeSlotId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'error');
    });
}

function openBulkCreateModal() {
    document.getElementById('bulkCreateModal').classList.remove('hidden');
}

function closeBulkCreateModal() {
    document.getElementById('bulkCreateModal').classList.add('hidden');
}

function addTimeInput(button) {
    const container = button.closest('.space-y-2');
    const newInput = document.createElement('div');
    newInput.className = 'flex gap-2';
    newInput.innerHTML = `
        <input type="time" name="times[]" required
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <button type="button" onclick="removeTimeInput(this)"
                class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">-</button>
    `;
    container.appendChild(newInput);
}

function removeTimeInput(button) {
    button.closest('.flex').remove();
}

function saveBulkCreate() {
    const form = document.getElementById('bulkCreateForm');
    const formData = new FormData(form);
    
    fetch('/admin/consulting/timeslots/bulk-create', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'error');
    });
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this time slot?')) {
        fetch(`/admin/consulting/timeslots/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Success', data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            showNotification('Error', error.message, 'error');
        });
    }
}

// Add these new functions for bulk delete
const selectAllCheckbox = document.getElementById('selectAll');
const slotCheckboxes = document.querySelectorAll('.slot-checkbox');
const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

selectAllCheckbox.addEventListener('change', function() {
    slotCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkDeleteButton();
});

slotCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkDeleteButton);
});

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.slot-checkbox:checked').length;
    bulkDeleteBtn.disabled = selectedCount === 0;
}

function confirmBulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.slot-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (confirm(`Are you sure you want to delete ${selectedIds.length} time slots?`)) {
        fetch('{{ route("admin.consulting.timeslots.bulk-destroy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Success', data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            showNotification('Error', error.message, 'error');
        });
    }
}
</script>
@endpush
@endsection 