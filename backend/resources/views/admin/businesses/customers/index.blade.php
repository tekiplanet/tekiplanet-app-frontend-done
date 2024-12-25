@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $business->business_name }} - Customers
        </h2>
        <a href="{{ route('admin.businesses.show', $business) }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Back to Business
        </a>
    </div>

    <!-- Filters and Search -->
    <div class="mb-6 bg-white rounded-lg shadow-md dark:bg-gray-800 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search customers..."
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Customers List -->
    <div class="bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50 dark:bg-gray-700">
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Phone
                        </th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $customer->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $customer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $customer->phone }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="showCustomerDetails('{{ $customer->id }}')" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                View Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- Customer Details Modal -->
<div id="customerDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[60]">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl dark:bg-gray-800 w-full max-w-2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                    Customer Details
                </h3>
                <div id="customerDetailsContent" class="space-y-4">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex justify-end">
                <button onclick="closeCustomerModal()" 
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function showCustomerDetails(customerId) {
    try {
        const response = await fetch(`{{ route('admin.businesses.customers.show', ['business' => $business->id, 'customer' => '__ID__']) }}`.replace('__ID__', customerId));
        const data = await response.json();
        
        if (response.ok) {
            const content = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">Name</label>
                        <p class="text-gray-800 dark:text-gray-200">${data.name}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">Email</label>
                        <p class="text-gray-800 dark:text-gray-200">${data.email}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">Phone</label>
                        <p class="text-gray-800 dark:text-gray-200">${data.phone || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">Status</label>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-sm rounded-full ${
                                data.status === 'active' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'
                            }">
                                ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Address</label>
                    <p class="text-gray-800 dark:text-gray-200">${data.address || 'N/A'}</p>
                </div>
                <div class="mt-4">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Notes</label>
                    <p class="text-gray-800 dark:text-gray-200">${data.notes || 'N/A'}</p>
                </div>
            `;
            
            document.getElementById('customerDetailsContent').innerHTML = content;
            document.getElementById('customerDetailsModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error', 'Failed to load customer details', 'error');
    }
}

function closeCustomerModal() {
    document.getElementById('customerDetailsModal').classList.add('hidden');
}

// Search functionality
const searchInput = document.getElementById('searchInput');
let searchTimeout;

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchQuery = this.value;
        window.location.href = `{{ route('admin.businesses.customers.index', $business) }}?search=${searchQuery}`;
    }, 500);
});
</script>
@endpush
@endsection 