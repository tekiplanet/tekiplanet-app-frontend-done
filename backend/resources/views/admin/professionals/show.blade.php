@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    {{ $professional->user->first_name }} {{ $professional->user->last_name }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $professional->title }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="openEditModal()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Details
                </button>
                <button onclick="toggleStatus()" 
                        class="px-4 py-2 {{ $professional->status === 'active' ? 'bg-red-600' : 'bg-green-600' }} text-white rounded-lg hover:{{ $professional->status === 'active' ? 'bg-red-700' : 'bg-green-700' }}">
                    {{ $professional->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Professional Information -->
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Professional Information
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Specialization</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->specialization }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Years of Experience</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->years_of_experience }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Hourly Rate</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->hourly_rate }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Bio</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->bio }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Status</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-sm rounded-full {{ $professional->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($professional->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                Contact Information
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Email</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->user->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Preferred Contact Method</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->preferred_contact_method }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Timezone</label>
                    <p class="text-gray-800 dark:text-gray-200">{{ $professional->timezone }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.professionals.partials.edit-modal')
@include('admin.professionals.partials.status-modal')

@endsection

@push('scripts')
<script>
// ... JavaScript for modals and status toggle will be added next
</script>
@endpush 