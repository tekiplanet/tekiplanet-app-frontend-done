<div x-data="{ 
    open: false, 
    mode: 'create',
    moduleId: null,
    moduleData: {
        title: '',
        description: '',
        duration_hours: '',
        order: ''
    }
}" 
    @open-module-modal.window="
        open = true; 
        mode = 'create';
        moduleData = {
            title: '',
            description: '',
            duration_hours: '',
            order: ''
        }
    "
    @edit-module.window="
        moduleId = $event.detail.id;
        mode = 'edit';
        loadModule($event.detail.id);
        open = true;
    "
    x-cloak>
    
    <!-- Modal Backdrop -->
    <div x-show="open" 
         class="fixed inset-0 bg-black bg-opacity-50 z-40"
         @click="open = false"></div>

    <!-- Modal Content -->
    <div x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto" 
             @click.outside="open = false">
            
            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <span x-text="mode === 'create' ? 'Add New Module' : 'Edit Module'"></span>
                </h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="mode === 'create' ? createModule() : updateModule()" class="p-4 space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Module Title</label>
                    <input type="text" 
                           x-model="moduleData.title"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea x-model="moduleData.description"
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              required></textarea>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (hours)</label>
                    <input type="number" 
                           x-model="moduleData.duration_hours"
                           min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>

                <!-- Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order</label>
                    <input type="number" 
                           x-model="moduleData.order"
                           min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" 
                            @click="open = false"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <span x-text="mode === 'create' ? 'Create Module' : 'Update Module'"></span>
                        <span class="hidden loading-spinner">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadModule(moduleId) {
    fetch(`/admin/courses/{{ $course->id }}/modules/${moduleId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Alpine.store('moduleData', data.module);
            }
        })
        .catch(error => {
            console.error('Error loading module:', error);
            showNotification('Error', 'Failed to load module data', 'error');
        });
}

function createModule() {
    const submitButton = document.querySelector('button[type="submit"]');
    const loadingSpinner = submitButton.querySelector('.loading-spinner');
    
    // Disable button and show spinner
    submitButton.disabled = true;
    loadingSpinner.classList.remove('hidden');

    const data = {
        course_id: '{{ $course->id }}',
        ...this.moduleData
    };

    fetch('/admin/courses/{{ $course->id }}/modules', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Module created successfully');
            this.open = false;
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error', 'Failed to create module', 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        loadingSpinner.classList.add('hidden');
    });
}

function updateModule() {
    fetch(`/admin/courses/{{ $course->id }}/modules/${this.moduleId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(this.moduleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Module updated successfully');
            this.open = false;
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error', 'Failed to update module', 'error');
    });
}
</script>
@endpush 