<x-mail.layout>
    <div>
        <h2 class="text-xl font-bold mb-4">Account Status Update</h2>
        
        <div class="text-gray-600 dark:text-gray-400">
            <p>Your account status has been updated to <strong>{{ ucfirst($status) }}</strong>.</p>
            
            @if($status === 'active')
                <p class="mt-4">You can now access all platform features and services.</p>
            @else
                <p class="mt-4">Your access to the platform has been temporarily restricted. Please contact support if you need assistance.</p>
            @endif
        </div>

        <div class="mt-8 text-sm text-gray-500">
            <p>If you believe this change was made in error, please contact our support team.</p>
        </div>
    </div>
</x-mail.layout> 